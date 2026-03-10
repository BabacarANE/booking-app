<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Http\Resources\PaymentResource;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    // ✅ Créer un Payment Intent
    public function createIntent(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = $request->user()
            ->bookings()
            ->findOrFail($request->booking_id);

        // Vérifier que la réservation est en attente
        if ($booking->payment_status !== 'pending') {
            return response()->json([
                'message' => 'Cette réservation a déjà été payée ou annulée.',
            ], 409);
        }

        // Créer le Payment Intent Stripe
        $paymentIntent = PaymentIntent::create([
            'amount'   => (int) ($booking->total_price * 100), // En centimes
            'currency' => 'eur',
            'metadata' => [
                'booking_id' => $booking->id,
                'user_id'    => $request->user()->id,
            ],
        ]);

        // Sauvegarder le payment intent id
        $booking->update([
            'payment_intent_id' => $paymentIntent->id,
        ]);

        // Créer l'enregistrement Payment
        Payment::create([
            'booking_id'        => $booking->id,
            'amount'            => $booking->total_price,
            'currency'          => 'EUR',
            'stripe_payment_id' => $paymentIntent->id,
            'status'            => 'pending',
        ]);

        return response()->json([
            'client_secret' => $paymentIntent->client_secret,
            'booking_id'    => $booking->id,
            'amount'        => $booking->total_price,
        ]);
    }

    // ✅ Confirmer un paiement
    public function confirm(Request $request)
    {
        $request->validate([
            'booking_id'         => 'required|exists:bookings,id',
            'payment_intent_id'  => 'required|string',
        ]);

        $booking = $request->user()
            ->bookings()
            ->with('payment')
            ->findOrFail($request->booking_id);

        // Vérifier le statut du Payment Intent côté Stripe
        $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

        if ($paymentIntent->status === 'succeeded') {
            // Mettre à jour la réservation
            $booking->update([
                'status'         => 'confirmed',
                'payment_status' => 'succeeded',
            ]);

            // Mettre à jour le paiement
            $booking->payment->update([
                'status'         => 'succeeded',
                'payment_method' => $paymentIntent->payment_method,
            ]);

            // ✅ Bloquer les dates dans la table availabilities
            $this->blockDates($booking);

            return response()->json([
                'message' => 'Paiement confirmé avec succès.',
                'booking' => $booking->load(['resource', 'payment']),
            ]);
        }

        return response()->json([
            'message' => 'Le paiement n\'a pas pu être confirmé.',
            'status'  => $paymentIntent->status,
        ], 400);
    }

    private function blockDates(Booking $booking): void
    {
        $start = \Carbon\Carbon::parse($booking->check_in_date);
        $end   = \Carbon\Carbon::parse($booking->check_out_date);

        $current = $start->copy();

        while ($current->lt($end)) {
            \App\Models\Availability::updateOrCreate(
                [
                    'resource_id' => $booking->resource_id,
                    'date'        => $current->toDateString(),
                ],
                [
                    'is_available' => false,
                    'reason'       => 'booked',
                ]
            );
            $current->addDay();
        }
    }
}
