<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ArchivoOptimizerService
{
    public function procesar(UploadedFile $archivo, string $carpeta = 'laboratorio'): array
    {
        $extension  = strtolower($archivo->getClientOriginalExtension());
        $nombreBase = Str::uuid()->toString();

        if ($extension === 'pdf') {
            return $this->comprimirPdf($archivo, $carpeta, $nombreBase);
        }

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
            return $this->convertirAWebp($archivo, $carpeta, $nombreBase);
        }

        // Tipo no soportado — guarda tal cual en public
        $ruta = $archivo->storeAs($carpeta, $nombreBase . '.' . $extension, 'public');
        return [
            'ruta'    => $ruta,
            'nombre'  => $archivo->getClientOriginalName(),
            'tamanio' => $archivo->getSize(),
        ];
    }

    // ─────────────────────────────────────────
    // IMÁGENES → WebP (calidad 75, máx 2000px)
    // ─────────────────────────────────────────
    private function convertirAWebp(UploadedFile $archivo, string $carpeta, string $nombre): array
    {
        $manager     = new ImageManager(new Driver());
        $imagen      = $manager->read($archivo->getRealPath());
        $nombreFinal = $nombre . '.webp';
        $dirFisico   = storage_path("app/public/{$carpeta}");
        $rutaFisica  = "{$dirFisico}/{$nombreFinal}";

        if (!file_exists($dirFisico)) {
            mkdir($dirFisico, 0755, true);
        }

        // Redimensionar si supera 2000px de ancho
        if ($imagen->width() > 2000) {
            $imagen->scale(width: 2000);
        }

        $imagen->toWebp(75)->save($rutaFisica);

        return [
            'ruta'    => "{$carpeta}/{$nombreFinal}",
            'nombre'  => pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME) . '.webp',
            'tamanio' => file_exists($rutaFisica) ? filesize($rutaFisica) : 0,
            'tipo'    => 'image/webp',
        ];
    }

    // ─────────────────────────────────────────
    // PDF → Ghostscript (doble intento)
    // ─────────────────────────────────────────
    private function comprimirPdf(UploadedFile $archivo, string $carpeta, string $nombre): array
    {
        $nombreFinal   = $nombre . '.pdf';
        $dirFisico     = storage_path("app/public/{$carpeta}");
        $origen        = $archivo->getRealPath();
        $destino       = "{$dirFisico}/{$nombreFinal}";
        $tamanioOrigen = $archivo->getSize();

        if (!file_exists($dirFisico)) {
            mkdir($dirFisico, 0755, true);
        }

        $gs = $this->rutaGhostscript();

        if ($this->ghostscriptDisponible($gs)) {

            // ── Intento 1: /screen (72 DPI, más agresivo) ──────────
            $ok = $this->ejecutarGhostscript($gs, $origen, $destino, '/screen');

            // Si el resultado sigue siendo más del 85% del original,
            // significa que el PDF ya estaba bastante comprimido.
            // Aplicamos parámetros manuales ultra-agresivos.
            if ($ok && file_exists($destino) && filesize($destino) > ($tamanioOrigen * 0.85)) {
                $this->ejecutarGhostscriptUltra($gs, $origen, $destino);
            }

            // Seguro: si falló todo, copia el original sin comprimir
            if (!file_exists($destino) || filesize($destino) === 0) {
                copy($origen, $destino);
            }

        } else {
            copy($origen, $destino);
        }

        return [
            'ruta'    => "{$carpeta}/{$nombreFinal}",
            'nombre'  => $archivo->getClientOriginalName(),
            'tamanio' => file_exists($destino) ? filesize($destino) : 0,
            'tipo'    => 'application/pdf',
        ];
    }

    /**
     * Ejecuta Ghostscript con el preset dado (/screen, /ebook, etc.)
     */
    private function ejecutarGhostscript(string $gs, string $origen, string $destino, string $preset): bool
    {
        $comando = sprintf(
            '"%s" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 ' .
            '-dPDFSETTINGS=%s ' .
            '-dNOPAUSE -dQUIET -dBATCH ' .
            '-sOutputFile="%s" "%s" 2>&1',
            $gs, $preset, $destino, $origen
        );

        exec($comando, $output, $codigo);

        return $codigo === 0 && file_exists($destino) && filesize($destino) > 0;
    }

    /**
     * Parámetros ultra-agresivos: baja imágenes a 96 DPI con JPEG calidad 55.
     * Útil cuando el PDF ya está semi-comprimido y /screen no ayuda mucho.
     */
    private function ejecutarGhostscriptUltra(string $gs, string $origen, string $destino): void
    {
        $comando = sprintf(
            '"%s" -sDEVICE=pdfwrite ' .
            '-dCompatibilityLevel=1.4 ' .
            '-dNOPAUSE -dQUIET -dBATCH ' .
            // Imágenes color
            '-dDownsampleColorImages=true ' .
            '-dColorImageResolution=96 ' .
            '-dColorImageDownsampleType=/Bicubic ' .
            '-dColorImageFilter=/DCTEncode ' .
            '-dAutoFilterColorImages=false ' .
            // Imágenes gris
            '-dDownsampleGrayImages=true ' .
            '-dGrayImageResolution=96 ' .
            '-dGrayImageDownsampleType=/Bicubic ' .
            '-dGrayImageFilter=/DCTEncode ' .
            '-dAutoFilterGrayImages=false ' .
            // Imágenes mono
            '-dDownsampleMonoImages=true ' .
            '-dMonoImageResolution=96 ' .
            // Calidad JPEG
            '-dJPEGQ=55 ' .
            // Fuentes
            '-dCompressFonts=true ' .
            '-dSubsetFonts=true ' .
            '-sOutputFile="%s" "%s" 2>&1',
            $gs, $destino, $origen
        );

        exec($comando, $output, $codigo);

        // Si el ultra falló, volvemos al /screen como fallback
        if ($codigo !== 0 || !file_exists($destino) || filesize($destino) === 0) {
            $this->ejecutarGhostscript($gs, $origen, $destino, '/screen');
        }
    }

    // ─────────────────────────────────────────
    // Helpers Ghostscript
    // ─────────────────────────────────────────
    private function rutaGhostscript(): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $base = 'C:\\Program Files\\gs';
            if (is_dir($base)) {
                $versiones = array_diff(scandir($base), ['.', '..']);
                foreach (array_reverse($versiones) as $v) {
                    $ruta = "{$base}\\{$v}\\bin\\gswin64c.exe";
                    if (file_exists($ruta)) return $ruta;
                }
            }
            return 'gswin64c';
        }

        return 'gs'; // Linux/Mac
    }

    private function ghostscriptDisponible(string $gs): bool
    {
        exec('"' . $gs . '" --version 2>&1', $out, $code);
        return $code === 0;
    }
}