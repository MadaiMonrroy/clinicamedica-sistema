<?php

namespace App\Http\Controllers\Recepcion;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Ingreso;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EstadisticasEsperaController extends Controller
{
    /**
     * Mapa de colores por tipo de área.
     * Se define aquí en el backend para que el frontend
     * no tenga que conocer los tipos — basta con recibir el color.
     *
     * Añade aquí cualquier tipo nuevo que agregues en la BD.
     */
    private array $coloresPorTipo = [
        'consulta'     => ['dot' => '#8b5cf6', 'bg' => 'rgba(139,92,246,.10)', 'border' => 'rgba(139,92,246,.25)'],
        'laboratorio'  => ['dot' => '#52ABB1', 'bg' => 'rgba(82,171,177,.10)', 'border' => 'rgba(82,171,177,.25)'],
        'enfermeria'   => ['dot' => '#f59e0b', 'bg' => 'rgba(245,158,11,.10)',  'border' => 'rgba(245,158,11,.25)'],
        'observacion'  => ['dot' => '#ef4444', 'bg' => 'rgba(239,68,68,.10)',   'border' => 'rgba(239,68,68,.25)'],
        'emergencia'   => ['dot' => '#ef4444', 'bg' => 'rgba(239,68,68,.10)',   'border' => 'rgba(239,68,68,.25)'],
        // Fallback para cualquier tipo no listado:
        '_default'     => ['dot' => '#94a3b8', 'bg' => 'rgba(148,163,184,.10)', 'border' => 'rgba(148,163,184,.25)'],
    ];

    public function panelEspera(): JsonResponse
    {
        $hoy = Carbon::today();

        // ── 1. Cola de triaje ─────────────────────────────────────────────────
        $esperandoEnfermeria = Ingreso::whereDate('created_at', $hoy)
            ->where('estado', 'esperando_enfermeria')
            ->count();

        // ── 2. Resumen del día ────────────────────────────────────────────────
        $totalIngresos = Ingreso::whereDate('created_at', $hoy)->count();

        $totalEnEspera = Ticket::whereDate('tickets.created_at', $hoy)
            ->where('tickets.estado', 'en_espera')->count();

        $totalEnCurso = Ticket::whereDate('tickets.created_at', $hoy)
            ->where('tickets.estado', 'en_turno')->count();

        $finalizados = Ingreso::whereDate('created_at', $hoy)
            ->where('estado', 'finalizado')->count();

        $cancelados = Ingreso::whereDate('created_at', $hoy)
            ->where('estado', 'cancelado')->count();

        // ── 3. Detalle por área ───────────────────────────────────────────────
        $areaIds = Ticket::select('area_id')
            ->whereDate('created_at', $hoy)
            ->whereIn('estado', ['en_espera', 'en_turno'])
            ->distinct()
            ->pluck('area_id');

        $areas = [];

        foreach ($areaIds as $areaId) {
            $areaInfo = DB::table('areas')->where('id', $areaId)->first();
            if (!$areaInfo || $areaInfo->estado === 'inactivo') continue;

            // Color según tipo (con fallback)
            $color = $this->coloresPorTipo[$areaInfo->tipo]
                  ?? $this->coloresPorTipo['_default'];

            // Pacientes EN TURNO
            $enTurnoRaw = Ticket::select(
                    'tickets.id as ticket_id',
                    'tickets.numero_ticket',
                    'tickets.prioridad_turno as prioridad',
                    DB::raw("CONCAT(p.nombres, ' ', p.apellido_paterno) as nombre_completo"),
                    DB::raw("UPPER(LEFT(p.nombres,1)) || UPPER(LEFT(p.apellido_paterno,1)) as iniciales")
                )
                ->join('pacientes as p', 'p.id', '=', 'tickets.paciente_id')
                ->where('tickets.area_id', $areaId)
                ->whereDate('tickets.created_at', $hoy)
                ->where('tickets.estado', 'en_turno')
                ->orderBy('tickets.llamado_en')
                ->get()
                ->map(fn($t) => [
                    'ticket_id'     => $t->ticket_id,
                    'numero_ticket' => $t->numero_ticket,
                    'prioridad'     => $t->prioridad,
                    'nombre'        => trim($t->nombre_completo),
                    'iniciales'     => $t->iniciales,
                ]);

            // Pacientes EN ESPERA
            $enEsperaRaw = Ticket::select(
                    'tickets.id as ticket_id',
                    'tickets.numero_ticket',
                    'tickets.prioridad_turno as prioridad',
                    DB::raw("CONCAT(p.nombres, ' ', p.apellido_paterno) as nombre_completo")
                )
                ->join('pacientes as p', 'p.id', '=', 'tickets.paciente_id')
                ->where('tickets.area_id', $areaId)
                ->whereDate('tickets.created_at', $hoy)
                ->where('tickets.estado', 'en_espera')
                ->orderByRaw("CASE WHEN tickets.prioridad_turno = 'critico' THEN 1
                                   WHEN tickets.prioridad_turno = 'urgente' THEN 2
                                   ELSE 3 END")
                ->orderBy('tickets.created_at')
                ->get()
                ->map(fn($t) => [
                    'ticket_id'     => $t->ticket_id,
                    'numero_ticket' => $t->numero_ticket,
                    'prioridad'     => $t->prioridad,
                    'nombre'        => trim($t->nombre_completo),
                ]);

            $areas[] = [
                'area_id'             => $areaId,
                'area_nombre'         => $areaInfo->nombre,
                'area_codigo'         => $areaInfo->codigo,
                'area_tipo'           => $areaInfo->tipo,
                // Colores ya resueltos en el backend
                'color_dot'           => $color['dot'],
                'color_bg'            => $color['bg'],
                'color_border'        => $color['border'],
                'en_espera'           => $enEsperaRaw->count(),
                'en_turno'            => $enTurnoRaw->count(),
                'total'               => $enEsperaRaw->count() + $enTurnoRaw->count(),
                'pacientes_en_curso'  => $enTurnoRaw->values(),
                'pacientes_en_espera' => $enEsperaRaw->values(),
            ];
        }

        usort($areas, fn($a, $b) => $b['total'] <=> $a['total']);

        return response()->json([
            'fecha'                => $hoy->translatedFormat('d \d\e F, Y'),
            'hora_actualizacion'   => now()->format('H:i:s'),
            'esperando_enfermeria' => $esperandoEnfermeria,
            'areas'                => array_values($areas),
            'resumen_dia' => [
                'total'      => $totalIngresos,
                'en_espera'  => $totalEnEspera,
                'en_curso'   => $totalEnCurso,
                'finalizados'=> $finalizados,
                'cancelados' => $cancelados,
            ],
        ]);
    }
}