<?php

namespace App\Services;

use App\Models\EventRegistration;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketService
{
    /**
     * Générer un code de billet unique
     */
    public function generateTicketCode(EventRegistration $registration): string
    {
        $prefix = 'EVT';
        $year = date('Y');
        $eventId = str_pad($registration->event_id, 4, '0', STR_PAD_LEFT);
        $random = strtoupper(Str::random(6));
        
        return "{$prefix}-{$year}-{$eventId}-{$random}";
    }

    /**
     * Générer un QR Code pour un billet
     */
    public function generateQRCode(EventRegistration $registration): string
    {
        try {
            // Données à encoder dans le QR code
            $qrData = json_encode([
                'ticket_code' => $registration->ticket_code,
                'event_id' => $registration->event_id,
                'user_id' => $registration->user_id,
                'registration_id' => $registration->id,
                'event_title' => $registration->event->title,
                'user_name' => $registration->user->name,
                'registered_at' => $registration->created_at->toDateTimeString(),
            ]);

            // Créer le QR code (API v6 - constructeur direct)
            $qrCode = new QrCode($qrData);
            
            // Créer le writer PNG
            $writer = new PngWriter();

            // Générer l'image
            $result = $writer->write($qrCode);

            // Sauvegarder le QR code
            $fileName = 'qrcodes/' . $registration->ticket_code . '.png';
            Storage::disk('public')->put($fileName, $result->getString());

            return $fileName;
        } catch (\Exception $e) {
            \Log::error('Erreur génération QR Code: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Générer un billet complet (ticket code + QR code)
     */
    public function generateTicket(EventRegistration $registration): EventRegistration
    {
        // Générer le code du billet s'il n'existe pas
        if (empty($registration->ticket_code)) {
            $registration->ticket_code = $this->generateTicketCode($registration);
        }

        // Générer le QR code
        $qrCodePath = $this->generateQRCode($registration);
        $registration->qr_code_path = $qrCodePath;
        $registration->ticket_status = 'active';
        $registration->save();

        return $registration->fresh();
    }

    /**
     * Valider un billet (check-in)
     */
    public function validateTicket(string $ticketCode, int $userId): array
    {
        $registration = EventRegistration::with(['event', 'user'])
            ->where('ticket_code', $ticketCode)
            ->first();

        if (!$registration) {
            return [
                'success' => false,
                'message' => 'Billet non trouvé',
                'data' => null
            ];
        }

        // Vérifier si le billet est déjà utilisé
        if ($registration->ticket_status === 'used') {
            return [
                'success' => false,
                'message' => 'Ce billet a déjà été utilisé le ' . $registration->checked_in_at->format('d/m/Y à H:i'),
                'data' => $registration
            ];
        }

        // Vérifier si le billet est annulé
        if ($registration->ticket_status === 'cancelled') {
            return [
                'success' => false,
                'message' => 'Ce billet a été annulé',
                'data' => $registration
            ];
        }

        // Vérifier si l'événement est passé
        if ($registration->event->start_date->isPast()) {
            return [
                'success' => false,
                'message' => 'Cet événement est déjà terminé',
                'data' => $registration
            ];
        }

        // Marquer le billet comme utilisé
        $registration->update([
            'ticket_status' => 'used',
            'checked_in_at' => now(),
            'checked_in_by' => $userId,
        ]);

        return [
            'success' => true,
            'message' => 'Billet validé avec succès ! Bienvenue ' . $registration->user->name,
            'data' => $registration->fresh()
        ];
    }

    /**
     * Décoder les données d'un QR code
     */
    public function decodeQRData(string $qrData): ?array
    {
        try {
            return json_decode($qrData, true);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Annuler un billet
     */
    public function cancelTicket(EventRegistration $registration): bool
    {
        if ($registration->ticket_status === 'used') {
            return false; // Ne peut pas annuler un billet déjà utilisé
        }

        $registration->update([
            'ticket_status' => 'cancelled',
            'status' => 'cancelled'
        ]);

        return true;
    }

    /**
     * Régénérer le QR code (si nécessaire)
     */
    public function regenerateQRCode(EventRegistration $registration): string
    {
        // Supprimer l'ancien QR code s'il existe
        if ($registration->qr_code_path && Storage::disk('public')->exists($registration->qr_code_path)) {
            Storage::disk('public')->delete($registration->qr_code_path);
        }

        // Générer un nouveau QR code
        $qrCodePath = $this->generateQRCode($registration);
        $registration->qr_code_path = $qrCodePath;
        $registration->save();

        return $qrCodePath;
    }

    /**
     * Obtenir les statistiques de check-in pour un événement
     */
    public function getCheckInStats(int $eventId): array
    {
        $total = EventRegistration::where('event_id', $eventId)->count();
        $checkedIn = EventRegistration::where('event_id', $eventId)
            ->where('ticket_status', 'used')
            ->count();
        $active = EventRegistration::where('event_id', $eventId)
            ->where('ticket_status', 'active')
            ->count();
        $cancelled = EventRegistration::where('event_id', $eventId)
            ->where('ticket_status', 'cancelled')
            ->count();

        return [
            'total' => $total,
            'checked_in' => $checkedIn,
            'active' => $active,
            'cancelled' => $cancelled,
            'percentage' => $total > 0 ? round(($checkedIn / $total) * 100, 2) : 0
        ];
    }
}
