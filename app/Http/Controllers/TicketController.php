<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Lista general de tickets.
     */
    public function index(Request $request)
    {
        $estado = $request->get('estado');

        $tickets = Ticket::with(['paciente', 'area'])
            ->when($estado, fn ($query) => $query->where('estado', $estado))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('medico.tickets.index', compact('tickets', 'estado'));
    }

    /**
     * Cola por área.
     */
    public function queueByArea(Area $area)
    {
        $tickets = Ticket::with('paciente')
            ->where('area_id', $area->id)
            ->whereIn('estado', ['en_espera', 'en_turno'])
            ->orderByRaw("CASE WHEN prioridad_turno = 'critico' THEN 0 WHEN prioridad_turno = 'urgente' THEN 1 ELSE 2 END")
            ->orderBy('created_at')
            ->get();

        return view('medico.tickets.queue-area', compact('area', 'tickets'));
    }

    /**
     * Ver detalle del ticket.
     */
    public function show(Ticket $ticket)
    {
        $ticket->load(['paciente', 'area', 'ingreso', 'enfermeria']);

        return view('medico.tickets.show', compact('ticket'));
    }

    /**
     * Llamar ticket.
     */
    public function call(Ticket $ticket)
    {
        $ticket->update([
            'llamado_en' => now(),
        ]);

        return back()->with('success', 'Paciente llamado correctamente.');
    }

    /**
     * Marcar ticket en turno.
     */
    public function setInTurn(Ticket $ticket)
    {
        Ticket::where('area_id', $ticket->area_id)
            ->where('estado', 'en_turno')
            ->update(['estado' => 'en_espera']);

        $ticket->update([
            'estado' => 'en_turno',
            'llamado_en' => now(),
        ]);

        return back()->with('success', 'Ticket marcado como en turno.');
    }

    /**
     * Finalizar ticket.
     */
    public function finish(Ticket $ticket)
    {
        $ticket->update([
            'estado' => 'atendido',
            'finalizado_en' => now(),
        ]);

        return back()->with('success', 'Ticket finalizado correctamente.');
    }

    /**
     * Cancelar ticket.
     */
    public function cancel(Ticket $ticket)
    {
        $ticket->update([
            'estado' => 'cancelado',
            'finalizado_en' => now(),
        ]);

        return back()->with('success', 'Ticket cancelado.');
    }
}