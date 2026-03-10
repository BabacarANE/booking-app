<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class ExpirePendingBookings extends Command
{
    protected $signature   = 'bookings:expire-pending';
    protected $description = 'Expire les réservations pending non payées après 15 minutes';

    public function handle()
    {
        $expired = Booking::where('status', 'pending')
            ->where('payment_status', 'pending')
            ->whereNull('payment_intent_id') // ✅ Jamais amorcé le paiement
            ->where('created_at', '<', now()->subMinutes(15))
            ->get();

        foreach ($expired as $booking) {
            $booking->update(['status' => 'cancelled']);
            $this->info("Réservation #{$booking->id} expirée.");
        }

        // ✅ Aussi expirer les pending avec payment_intent mais non payés après 30 min
        $expiredWithIntent = Booking::where('status', 'pending')
            ->where('payment_status', 'pending')
            ->whereNotNull('payment_intent_id')
            ->where('created_at', '<', now()->subMinutes(30))
            ->get();

        foreach ($expiredWithIntent as $booking) {
            $booking->update(['status' => 'cancelled']);
            $this->info("Réservation #{$booking->id} (avec intent) expirée.");
        }

        $total = $expired->count() + $expiredWithIntent->count();
        $this->info("✅ {$total} réservation(s) expirée(s).");
    }
}
