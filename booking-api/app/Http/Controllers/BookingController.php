<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Resource;
use App\Http\Resources\BookingResource;
use Illuminate\Http\Request;

use App\Mail\BookingConfirmed;
use App\Mail\BookingCancelled;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    // ✅ Mes réservations
    public function index(Request $request)
    {
        $bookings = $request->user()
            ->bookings()
            ->with(['resource.category', 'payment'])
            ->latest()
            ->paginate(10);

        return BookingResource::collection($bookings);
    }

    // ✅ Détail d'une réservation
    public function show(Request $request, $id)
    {
        $booking = $request->user()
            ->bookings()
            ->with(['resource.category', 'payment'])
            ->findOrFail($id);

        return new BookingResource($booking);
    }

    // ✅ Créer une réservation
    public function store(Request $request)
    {
        $validated = $request->validate([
            'resource_id'      => 'required|exists:resources,id',
            'check_in_date'    => 'required|date|after_or_equal:today',
            'check_out_date'   => 'required|date|after:check_in_date',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $resource = Resource::findOrFail($validated['resource_id']);

        // ✅ Vérification de disponibilité
        $conflict = Booking::where('resource_id', $resource->id)
            ->where(function ($q) {
                $q->where('status', 'confirmed')
                    ->orWhere(function ($q) {
                        // Pending avec paiement amorcé (payment_intent_id présent)
                        $q->where('status', 'pending')
                            ->whereNotNull('payment_intent_id');
                    });
            })
            ->where(function ($query) use ($validated) {
                $query->whereBetween('check_in_date', [
                    $validated['check_in_date'],
                    $validated['check_out_date']
                ])
                    ->orWhereBetween('check_out_date', [
                        $validated['check_in_date'],
                        $validated['check_out_date']
                    ])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('check_in_date', '<=', $validated['check_in_date'])
                            ->where('check_out_date', '>=', $validated['check_out_date']);
                    });
            })->exists();

        if ($conflict) {
            return response()->json([
                'message' => 'Ces dates ne sont pas disponibles pour cette ressource.',
            ], 409);
        }

        // ✅ Calcul du prix total
        $checkIn  = \Carbon\Carbon::parse($validated['check_in_date']);
        $checkOut = \Carbon\Carbon::parse($validated['check_out_date']);
        $nights   = $checkIn->diffInDays($checkOut);
        $total    = $nights * $resource->price_per_night;

        // ✅ Création de la réservation
        $booking = Booking::create([
            'user_id'          => $request->user()->id,
            'resource_id'      => $resource->id,
            'check_in_date'    => $validated['check_in_date'],
            'check_out_date'   => $validated['check_out_date'],
            'total_price'      => $total,
            'status'           => 'pending',
            'payment_status'   => 'pending',
            'special_requests' => $validated['special_requests'] ?? null,
        ]);

        //  ✅ Mail de création

        Mail::to($request->user()->email)
            ->queue(new BookingConfirmed($booking->load(['user', 'resource'])));


        return new BookingResource($booking->load(['resource.category']));
    }

    // ✅ Modifier une réservation
    public function update(Request $request, $id)
    {
        $booking = $request->user()
            ->bookings()
            ->findOrFail($id);

        // Seulement si la réservation est encore en attente
        if (!in_array($booking->status, ['pending'])) {
            return response()->json([
                'message' => 'Cette réservation ne peut plus être modifiée.',
            ], 403);
        }

        $validated = $request->validate([
            'check_in_date'    => 'sometimes|date|after_or_equal:today',
            'check_out_date'   => 'sometimes|date|after:check_in_date',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        // Recalcul du prix si les dates changent
        if (isset($validated['check_in_date']) && isset($validated['check_out_date'])) {
            $checkIn  = \Carbon\Carbon::parse($validated['check_in_date']);
            $checkOut = \Carbon\Carbon::parse($validated['check_out_date']);
            $nights   = $checkIn->diffInDays($checkOut);
            $validated['total_price'] = $nights * $booking->resource->price_per_night;
        }

        $booking->update($validated);

        return new BookingResource($booking->load(['resource.category', 'payment']));
    }

    // ✅ Annuler une réservation
    public function destroy(Request $request, $id)
    {
        $booking = $request->user()
            ->bookings()
            ->findOrFail($id);

        if ($booking->status === 'cancelled') {
            return response()->json([
                'message' => 'Cette réservation est déjà annulée.',
            ], 409);
        }

        $booking->update(['status' => 'cancelled']);

        // ✅ Libérer les dates
        \App\Models\Availability::where('resource_id', $booking->resource_id)
            ->whereBetween('date', [
                $booking->check_in_date,
                \Carbon\Carbon::parse($booking->check_out_date)->subDay()->toDateString()
            ])
            ->where('reason', 'booked')
            ->update(['is_available' => true, 'reason' => null]);

        // Email annulation
        \Illuminate\Support\Facades\Mail::to($request->user()->email)
            ->queue(new \App\Mail\BookingCancelled($booking->load(['user', 'resource'])));

        return response()->json([
            'message' => 'Réservation annulée avec succès.',
        ]);
    }
}
