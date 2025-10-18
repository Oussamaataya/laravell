<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventRegistration;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Afficher l'interface de scan de billets
     */
    public function scanInterface($eventId = null)
    {
        $event = null;
        $stats = null;

        if ($eventId) {
            $event = \App\Models\Event::findOrFail($eventId);
            $stats = $this->ticketService->getCheckInStats($eventId);
        }

        $events = \App\Models\Event::where('status', 'active')
            ->where('start_date', '>=', now()->subDays(1))
            ->orderBy('start_date', 'asc')
            ->get();

        return view('admin.tickets.scan', compact('event', 'events', 'stats'));
    }

    /**
     * Valider un billet via le code QR
     */
    public function validateTicket(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string',
        ]);

        $result = $this->ticketService->validateTicket(
            $request->ticket_code,
            auth()->id()
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'user_name' => $result['data']->user->name,
                    'event_title' => $result['data']->event->title,
                    'checked_in_at' => $result['data']->checked_in_at->format('d/m/Y H:i'),
                    'ticket_code' => $result['data']->ticket_code,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'data' => $result['data'] ? [
                'user_name' => $result['data']->user->name ?? 'N/A',
                'event_title' => $result['data']->event->title ?? 'N/A',
            ] : null
        ], 422);
    }

    /**
     * Régénérer le QR code d'un billet
     */
    public function regenerateQRCode($registrationId)
    {
        $registration = EventRegistration::findOrFail($registrationId);
        
        $this->ticketService->regenerateQRCode($registration);

        return back()->with('success', 'QR Code régénéré avec succès');
    }

    /**
     * Liste des billets pour un événement
     */
    public function eventTickets($eventId)
    {
        $event = \App\Models\Event::with(['registrations.user', 'registrations.checkedInBy'])
            ->findOrFail($eventId);

        $stats = $this->ticketService->getCheckInStats($eventId);

        return view('admin.tickets.list', compact('event', 'stats'));
    }

    /**
     * Télécharger le QR code
     */
    public function downloadQRCode($registrationId)
    {
        $registration = EventRegistration::findOrFail($registrationId);

        if (!$registration->qr_code_path || !Storage::disk('public')->exists($registration->qr_code_path)) {
            return back()->with('error', 'QR Code non trouvé');
        }

        return Storage::disk('public')->download(
            $registration->qr_code_path,
            $registration->ticket_code . '.png'
        );
    }

    /**
     * Annuler un billet
     */
    public function cancelTicket($registrationId)
    {
        $registration = EventRegistration::findOrFail($registrationId);

        if ($this->ticketService->cancelTicket($registration)) {
            return back()->with('success', 'Billet annulé avec succès');
        }

        return back()->with('error', 'Impossible d\'annuler ce billet (déjà utilisé)');
    }
}
