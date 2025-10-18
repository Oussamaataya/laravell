<?php

use App\Models\EventRegistration;
use App\Services\TicketService;
use Illuminate\Support\Facades\Route;

Route::get('/regenerate-all-qrcodes', function () {
    $ticketService = app(TicketService::class);
    $registrations = EventRegistration::with(['event', 'user'])
        ->get();
    
    $results = [];
    $count = 0;
    
    foreach ($registrations as $registration) {
        // Skip si QR Code existe déjà
        if (!empty($registration->qr_code_path) && \Storage::disk('public')->exists($registration->qr_code_path)) {
            continue;
        }
        
        try {
            // Générer un ticket code si manquant
            if (empty($registration->ticket_code)) {
                $registration->ticket_code = $ticketService->generateTicketCode($registration);
                $registration->save();
                $registration->refresh();
            }
            
            $ticketService->generateTicket($registration);
            $results[] = "✅ QR généré pour le billet {$registration->ticket_code}";
            $count++;
        } catch (\Exception $e) {
            $results[] = "❌ Erreur pour l'inscription #{$registration->id}: {$e->getMessage()}";
        }
    }
    
    return response()->json([
        'total' => count($registrations),
        'generated' => $count,
        'results' => $results
    ]);
})->name('temp.regenerate-qrcodes');
