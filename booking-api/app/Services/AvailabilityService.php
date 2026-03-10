<?php

namespace App\Services;

use App\Models\Availability;
use App\Models\Booking;
use Carbon\Carbon;

class AvailabilityService
{
    /**
     * Vérifie si une ressource est disponible pour une plage de dates
     */
    public function isAvailable(int $resourceId, string $checkIn, string $checkOut): bool
    {
        // ✅ Vérification 1 — Table availabilities (dates bloquées manuellement ou par paiement confirmé)
        $blockedDates = Availability::where('resource_id', $resourceId)
            ->where('is_available', false)
            ->whereBetween('date', [
                $checkIn,
                Carbon::parse($checkOut)->subDay()->toDateString()
            ])
            ->exists();

        if ($blockedDates) {
            return false;
        }

        // ✅ Vérification 2 — Réservations confirmées + payées
        $conflict = Booking::where('resource_id', $resourceId)
            ->where(function ($q) {
                $q->where(function ($q) {
                    // Confirmées et payées
                    $q->where('status', 'confirmed')
                        ->where('payment_status', 'succeeded');
                })->orWhere(function ($q) {
                    // Pending avec paiement amorcé depuis moins de 30 min
                    $q->where('status', 'pending')
                        ->whereNotNull('payment_intent_id')
                        ->where('updated_at', '>=', now()->subMinutes(30));
                });
            })
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                    });
            })
            ->exists();

        return !$conflict;
    }

    /**
     * Retourne les dates indisponibles pour un mois donné
     */
    public function getUnavailableDates(int $resourceId, int $month, int $year): array
    {
        // Dates bloquées dans availabilities
        $blockedDates = Availability::where('resource_id', $resourceId)
            ->where('is_available', false)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        // Dates des réservations confirmées + payées
        $bookings = Booking::where('resource_id', $resourceId)
            ->where('status', 'confirmed')
            ->where('payment_status', 'succeeded')
            ->whereNotNull('payment_intent_id')
            ->where(function ($q) use ($month, $year) {
                $q->whereYear('check_in_date', $year)
                    ->whereMonth('check_in_date', $month);
            })
            ->orWhere(function ($q) use ($resourceId, $month, $year) {
                $q->where('resource_id', $resourceId)
                    ->where('status', 'confirmed')
                    ->where('payment_status', 'succeeded')
                    ->whereYear('check_out_date', $year)
                    ->whereMonth('check_out_date', $month);
            })
            ->get(['check_in_date', 'check_out_date']);

        $bookedDates = [];
        foreach ($bookings as $booking) {
            $current = Carbon::parse($booking->check_in_date);
            $end     = Carbon::parse($booking->check_out_date);
            while ($current->lt($end)) {
                $bookedDates[] = $current->toDateString();
                $current->addDay();
            }
        }

        return array_unique(array_merge($blockedDates, $bookedDates));
    }
}
