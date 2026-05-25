<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Atencion;
use App\Models\Ingreso;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        // Métricas generales del día
        $ingresosHoy = Ingreso::whereBetween('fecha_ingreso', [$today, $tomorrow])->count();

        $esperandoEnfermeria = Ingreso::where('estado', 'esperando_enfermeria')
            ->whereBetween('fecha_ingreso', [$today, $tomorrow])
            ->count();

        $enEnfermeria = Ingreso::where('estado', 'en_enfermeria')
            ->whereBetween('fecha_ingreso', [$today, $tomorrow])
            ->count();

        $enArea = Ingreso::where('estado', 'en_area')
            ->whereBetween('fecha_ingreso', [$today, $tomorrow])
            ->count();

        $finalizadosHoy = Ticket::where('estado', 'atendido')
            ->whereBetween('created_at', [$today, $tomorrow])
            ->count();

        $urgentesPendientes = Ingreso::where('prioridad_inicial', 'urgente')
            ->whereIn('estado', ['esperando_enfermeria', 'en_enfermeria', 'en_area'])
            ->whereBetween('fecha_ingreso', [$today, $tomorrow])
            ->count();

        // Stats por área desde tickets
        $areas = Area::where('estado', 'activo')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'codigo', 'tipo']);

        $ticketStats = Ticket::select(
                'area_id',
                'estado',
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('created_at', [$today, $tomorrow])
            ->groupBy('area_id', 'estado')
            ->get();

        $totalesPorArea = Ticket::select(
                'area_id',
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('created_at', [$today, $tomorrow])
            ->groupBy('area_id')
            ->pluck('total', 'area_id');

        $areasDashboard = $areas->map(function ($area) use ($ticketStats, $totalesPorArea) {
            $statsArea = $ticketStats->where('area_id', $area->id);

            return [
                'id' => $area->id,
                'nombre' => $area->nombre,
                'codigo' => $area->codigo,
                'tipo' => $area->tipo,
                'esperando' => (int) optional($statsArea->firstWhere('estado', 'en_espera'))->total,
                'en_turno' => (int) optional($statsArea->firstWhere('estado', 'en_turno'))->total,
                'atendidos' => (int) optional($statsArea->firstWhere('estado', 'atendido'))->total,
                'cancelados' => (int) optional($statsArea->firstWhere('estado', 'cancelado'))->total,
                'total_hoy' => (int) ($totalesPorArea[$area->id] ?? 0),
            ];
        });

        // Gráfico: pacientes por área
        $chartAreasLabels = $areasDashboard->pluck('nombre')->values();
        $chartAreasData = $areasDashboard->pluck('total_hoy')->values();

        // Gráfico: estado de flujo del día
        $chartEstadoLabels = [
            'Esperando enfermeria',
            'En enfermeria',
            'En área',
            'Finalizados',
        ];

        $chartEstadoData = [
            $esperandoEnfermeria,
            $enEnfermeria,
            $enArea,
            $finalizadosHoy,
        ];

        // Gráfico: ingresos por hora
        $ingresosPorHoraRaw = Ingreso::select(
                DB::raw('EXTRACT(HOUR FROM fecha_ingreso) as hora'),
                DB::raw('COUNT(*) as total')
            )
            ->whereBetween('fecha_ingreso', [$today, $tomorrow])
            ->groupBy('hora')
            ->orderBy('hora')
            ->get();

        $chartHorasLabels = [];
        $chartHorasData = [];

        for ($h = 0; $h < 24; $h++) {
            $chartHorasLabels[] = str_pad((string) $h, 2, '0', STR_PAD_LEFT) . ':00';
            $registro = $ingresosPorHoraRaw->firstWhere('hora', $h);
            $chartHorasData[] = $registro ? (int) $registro->total : 0;
        }

        // Movimientos recientes
        $movimientosRecientes = Ingreso::with([
                'paciente',
                'tickets.area',
            ])
            ->whereBetween('fecha_ingreso', [$today, $tomorrow])
            ->latest('fecha_ingreso')
            ->take(10)
            ->get()
            ->map(function ($ingreso) {
                $ultimoTicket = $ingreso->tickets->sortByDesc('created_at')->first();

                return [
                    'paciente' => $ingreso->paciente?->nombre_completo ?? 'Paciente no disponible',
                    'ci' => $ingreso->paciente?->ci ?? '-',
                    'hora' => optional($ingreso->fecha_ingreso)->format('H:i'),
                    'estado' => $ingreso->estado,
                    'area' => $ultimoTicket?->area?->nombre ?? 'Sin área asignada',
                    'prioridad' => $ingreso->prioridad_inicial,
                    'preingreso' => $ingreso->numero_preingreso ?: '-',
                ];
            });

        return view('dashboard', compact(
            'ingresosHoy',
            'esperandoEnfermeria',
            'enEnfermeria',
            'enArea',
            'finalizadosHoy',
            'urgentesPendientes',
            'areasDashboard',
            'chartAreasLabels',
            'chartAreasData',
            'chartEstadoLabels',
            'chartEstadoData',
            'chartHorasLabels',
            'chartHorasData',
            'movimientosRecientes'
        ));
    }
}