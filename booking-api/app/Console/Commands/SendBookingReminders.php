<?php

namespace App\Console\Commands;

use App\Mail\BookingReminder;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBookingReminders extends Command
{
    protected $signature   = 'bookings:send-reminders';
    protected $description = 'Envoie les rappels J-1 aux clients';

    public function handle()
    {
        $tomorrow = now()->addDay()->toDateString();

        $bookings = Booking::with(['user', 'resource'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in_date', $tomorrow)
            ->get();

        foreach ($bookings as $booking) {
            Mail::to($booking->user->email)
                ->queue(new BookingReminder($booking));
            $this->info("Rappel envoyé à {$booking->user->email}");
        }

        $this->info("✅ {$bookings->count()} rappel(s) envoyé(s).");
    }
}
