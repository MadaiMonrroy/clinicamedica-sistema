<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Ingreso;
use App\Models\Area;
use App\Models\AdjuntoLaboratorio;
use App\Models\LaboratorioSolicitud;
use App\Services\ArchivoOptimizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LaboratorioController extends Controller
{
    /**
     * Cola de tickets pendientes del laboratorio.
     * Vista principal del laboratista.
     */
    public function index()
    {
        $areaLab = Area::where('tipo', 'laboratorio')->first();

        abort_unless($areaLab, 404, 'Área de laboratorio no configurada.');

        // Tickets en espera o en turno del área LAB
        $ticketsPendientes = Ticket::with(['paciente', 'ingreso.solicitudesLab.examen'])
            ->where('area_id', $areaLab->id)
            ->whereIn('estado', ['en_espera', 'en_turno'])
            ->orderByRaw("CASE estado WHEN 'en_turno' THEN 0 ELSE 1 END")
            ->orderBy('created_at')
            ->get();

        // Tickets donde ya se tomó muestra pero faltan resultados
        $ticketsEnProceso = Ticket::with(['paciente', 'ingreso.solicitudesLab.examen', 'adjuntosLab'])
            ->where('area_id', $areaLab->id)
            ->where('estado', 'atendido')
            ->whereHas('ingreso.solicitudesLab', fn($q) => $q->whereIn('estado', ['muestra_tomada', 'en_proceso']))
            ->orderBy('updated_at', 'desc')
            ->get();

        // Tickets completados (con resultados subidos) — últimos 20
        $ticketsCompletados = Ticket::with(['paciente', 'ingreso', 'adjuntosLab'])
            ->where('area_id', $areaLab->id)
            ->where('estado', 'atendido')
            ->whereDoesntHave('ingreso.solicitudesLab', fn($q) => $q->whereNotIn('estado', ['completado']))
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        return view('laboratorio.index', compact(
            'ticketsPendientes',
            'ticketsEnProceso',
            'ticketsCompletados',
            'areaLab'
        ));
    }

    /**
     * Ver detalle de un ticket LAB.
     * Aquí el laboratista registra la toma de muestra.
     */
    public function show(Ticket $ticket)
    {
        $this->autorizarTicketLab($ticket);

        $ticket->load([
            'paciente',
            'ingreso.solicitudesLab.examen.categoriaExamen',
            'adjuntosLab.subidoPor',
        ]);

        return view('laboratorio.show', compact('ticket'));
    }

    /**
     * Llamar al siguiente paciente (pasa de en_espera → en_turno).
     */
    public function llamar(Ticket $ticket)
    {
        $this->autorizarTicketLab($ticket);

        abort_unless($ticket->estado === 'en_espera', 422, 'El ticket no está en espera.');

        // Solo puede haber 1 en turno a la vez en LAB
        Ticket::where('area_id', $ticket->area_id)
            ->where('estado', 'en_turno')
            ->update(['estado' => 'en_espera']);

        $ticket->update([
            'estado'     => 'en_turno',
            'llamado_en' => now(),
        ]);

        return back()->with('success', "Paciente {$ticket->paciente->nombres} llamado al laboratorio.");
    }

    /**
     * Registrar toma de muestra + observación.
     * Pasa todas las solicitudes a 'muestra_tomada' y el ticket a 'atendido'.
     */
    public function registrarMuestra(Request $request, Ticket $ticket)
    {
        $this->autorizarTicketLab($ticket);

        abort_unless($ticket->estado === 'en_turno', 422, 'Debes llamar al paciente primero.');

        $data = $request->validate([
            'observacion_muestra' => ['nullable', 'string', 'max:1000'],
        ]);

        // Marcar todas las solicitudes de este ingreso como muestra_tomada
        LaboratorioSolicitud::where('ingreso_id', $ticket->ingreso_id)
            ->where('estado', 'pendiente')
            ->update([
                'estado'              => 'muestra_tomada',
                'muestra_tomada_at'   => now(),
                'observacion_muestra' => $data['observacion_muestra'] ?? null,
            ]);

        // El ticket pasa a atendido (paciente ya se fue, muestra en proceso)
        $ticket->update([
            'estado'        => 'atendido',
            'finalizado_en' => now(),
        ]);

        return redirect()
            ->route('laboratorio.index')
            ->with('success', 'Muestra registrada. El paciente puede retirarse.');
    }

    /**
     * Subir PDF/imagen de resultado.
     * Se vincula al ticket. Puede haber múltiples adjuntos por ticket.
     */
    public function subirResultado(Request $request, Ticket $ticket)
{
    $this->autorizarTicketLab($ticket);

    $request->validate([
        'archivo'        => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:15360'],
        'nombre_archivo' => ['required', 'string', 'max:255'],
        'observacion'    => ['nullable', 'string', 'max:500'],
    ]);

    // ── Optimizar/comprimir el archivo ──────────────────────
    $optimizer   = new ArchivoOptimizerService();
    $carpeta     = "laboratorio/tickets/{$ticket->id}";
    $resultado   = $optimizer->procesar($request->file('archivo'), $carpeta);

    // ── Guardar registro en BD ───────────────────────────────
    AdjuntoLaboratorio::create([
        'ticket_id'      => $ticket->id,
        'subido_por'     => Auth::id(),
        'nombre_archivo' => $request->nombre_archivo,
        'ruta_archivo'   => $resultado['ruta'],
        'observacion'    => $request->observacion,
        'fecha_subida'   => now(),
        'estado'         => 'subido',
    ]);

    // Calcular ahorro para mostrar en el flash
    $original    = $request->file('archivo')->getSize();
    $comprimido  = $resultado['tamanio'];
    $ahorroPct   = $original > 0 ? round((1 - $comprimido / $original) * 100, 1) : 0;

    $msg = 'Resultado subido correctamente.';
    if ($ahorroPct > 5) {
        $msg .= " (Comprimido un {$ahorroPct}% — de "
              . round($original / 1024) . " KB a "
              . round($comprimido / 1024) . " KB)";
    }

    return back()->with('success', $msg);
}

    /**
     * Eliminar un adjunto subido (solo si no fue entregado).
     */
    public function eliminarAdjunto(AdjuntoLaboratorio $adjunto)
    {
        abort_unless($adjunto->estado === 'subido', 422, 'No se puede eliminar un resultado ya entregado.');

        Storage::disk('public')->delete($adjunto->ruta_archivo);
        $adjunto->delete();

        return back()->with('success', 'Archivo eliminado.');
    }

    /**
     * Cerrar el ticket marcando todos los exámenes como completados.
     * Solo se puede si hay al menos 1 adjunto subido.
     */
    public function cerrar(Ticket $ticket)
    {
        $this->autorizarTicketLab($ticket);

        $tieneAdjuntos = AdjuntoLaboratorio::where('ticket_id', $ticket->id)->exists();
        abort_unless($tieneAdjuntos, 422, 'Debes subir al menos un resultado antes de cerrar.');

        // Marcar todas las solicitudes como completado
        LaboratorioSolicitud::where('ingreso_id', $ticket->ingreso_id)
            ->update(['estado' => 'completado']);

        // Marcar adjuntos como entregado
        AdjuntoLaboratorio::where('ticket_id', $ticket->id)
            ->update(['estado' => 'entregado']);

        // Finalizar el ingreso
        $ticket->ingreso->update(['estado' => 'finalizado']);

        return redirect()
            ->route('laboratorio.index')
            ->with('success', 'Resultados entregados. Ticket cerrado correctamente.');
    }

    /**
     * Vista de detalle de un ticket en proceso (para subir resultados).
     */
    public function showResultados(Ticket $ticket)
    {
        $this->autorizarTicketLab($ticket);

        $ticket->load([
            'paciente',
            'ingreso.solicitudesLab.examen',
            'adjuntosLab.subidoPor',
        ]);

        return view('laboratorio.resultados', compact('ticket'));
    }

    // ── Helpers privados ───────────────────────────────────────────

    private function autorizarTicketLab(Ticket $ticket): void
    {
        $areaLab = Area::where('tipo', 'laboratorio')->first();
        abort_unless($ticket->area_id === optional($areaLab)->id, 403, 'Ticket no pertenece al laboratorio.');
    }
}