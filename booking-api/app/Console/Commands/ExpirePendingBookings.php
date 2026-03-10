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
            ->whereNull('payment_intent_id') // Pas encore de tentative de paiement
            ->where('created_at', '<', now()->subMinutes(15))
            ->get();

        foreach ($expired as $booking) {
            $booking->update(['status' => 'cancelled']);
            $this->info("Réservation #{$booking->id} expirée.");
        }

        $this->info("✅ {$expired->count()} réservation(s) expirée(s).");
    }
}
