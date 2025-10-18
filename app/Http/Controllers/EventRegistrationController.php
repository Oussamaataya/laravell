<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventRegistrationController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * S'inscrire à un événement
     */
    public function register(Request $request, Event $event)
    {
        // Vérifier que l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour vous inscrire à un événement.');
        }

        $user = Auth::user();

        // Vérifier que l'événement est actif
        if ($event->status !== 'active') {
            return back()->with('error', 'Cet événement n\'est pas disponible pour inscription.');
        }

        // Vérifier que l'événement n'est pas passé
        if ($event->start_date < now()->toDateString()) {
            return back()->with('error', 'Cet événement est déjà passé.');
        }

        // Vérifier la date limite d'inscription
        if ($event->registration_deadline && $event->registration_deadline < now()) {
            return back()->with('error', 'La date limite d\'inscription est dépassée.');
        }

        // Vérifier si l'utilisateur n'est pas déjà inscrit
        $existingRegistration = EventRegistration::where('event_id', $event->id)
                                                ->where('user_id', $user->id)
                                                ->first();

        if ($existingRegistration) {
            return back()->with('warning', 'Vous êtes déjà inscrit à cet événement.');
        }

        // Vérifier les places disponibles
        if ($event->max_participants && $event->current_participants >= $event->max_participants) {
            return back()->with('error', 'Cet événement est complet. Aucune place disponible.');
        }

        try {
            DB::beginTransaction();

            // Créer l'inscription
            $registration = EventRegistration::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'registered_at' => now(),
                'status' => 'confirmed',
                'payment_status' => $event->is_free ? 'not_required' : 'pending',
            ]);

            // Recharger l'inscription avec les relations
            $registration->load(['event', 'user']);

            // Générer automatiquement le billet électronique avec QR Code
            try {
                $this->ticketService->generateTicket($registration);
                // Recharger pour avoir le QR Code
                $registration->refresh();
            } catch (\Exception $ticketException) {
                // Log l'erreur mais ne bloque pas l'inscription
                \Log::error('Erreur génération ticket: ' . $ticketException->getMessage());
                \Log::error($ticketException->getTraceAsString());
            }

            // Envoyer l'email de confirmation
            try {
                \Mail::to($user->email)->send(new \App\Mail\EventRegistrationMail($registration));
                \Log::info('Email de confirmation envoyé', [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'registration_id' => $registration->id,
                ]);
            } catch (\Exception $mailException) {
                // Log l'erreur mais ne bloque pas l'inscription
                \Log::error('Erreur envoi email: ' . $mailException->getMessage());
            }

            // Incrémenter le nombre de participants
            $event->increment('current_participants');

            DB::commit();

            return back()->with('success', 'Inscription réussie ! Un email de confirmation vous a été envoyé avec votre billet électronique.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erreur inscription: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return back()->with('error', 'Une erreur est survenue lors de l\'inscription: ' . $e->getMessage());
        }
    }

    /**
     * Se désinscrire d'un événement
     */
    public function unregister(Request $request, Event $event)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $registration = EventRegistration::where('event_id', $event->id)
                                        ->where('user_id', $user->id)
                                        ->first();

        if (!$registration) {
            return back()->with('error', 'Vous n\'êtes pas inscrit à cet événement.');
        }

        // Vérifier si on peut encore se désinscrire (par exemple, pas moins de 24h avant l'événement)
        $eventDateTime = \Carbon\Carbon::parse($event->start_date->format('Y-m-d') . ' ' . $event->start_time);
        if ($eventDateTime->diffInHours(now()) < 24) {
            return back()->with('error', 'Vous ne pouvez plus vous désinscrire moins de 24h avant l\'événement.');
        }

        try {
            DB::beginTransaction();

            // Supprimer l'inscription
            $registration->delete();

            // Décrémenter le nombre de participants
            $event->decrement('current_participants');

            DB::commit();

            return back()->with('success', 'Désinscription réussie.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Une erreur est survenue lors de la désinscription.');
        }
    }

    /**
     * Voir mes inscriptions
     */
    public function myRegistrations()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $registrations = EventRegistration::where('user_id', Auth::id())
                                         ->with(['event' => function($query) {
                                             $query->orderBy('start_date', 'asc');
                                         }])
                                         ->get();

        return view('events.my-registrations', compact('registrations'));
    }

    /**
     * Afficher un billet spécifique
     */
    public function showTicket($registrationId)
    {
        $registration = EventRegistration::with('event', 'user')
            ->where('id', $registrationId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('events.ticket', compact('registration'));
    }
}
