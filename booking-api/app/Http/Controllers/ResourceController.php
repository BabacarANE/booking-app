<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Availability;
use App\Http\Resources\ResourceResource;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    // ✅ Liste avec filtres
    public function index(Request $request)
    {
        $query = Resource::with('category')->where('is_active', true);

        // Filtre par catégorie
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filtre par capacité
        if ($request->has('capacity')) {
            $query->where('capacity', '>=', $request->capacity);
        }

        // Filtre par prix max
        if ($request->has('price_max')) {
            $query->where('price_per_night', '<=', $request->price_max);
        }

        // Filtre par prix min
        if ($request->has('price_min')) {
            $query->where('price_per_night', '>=', $request->price_min);
        }

        // Filtre par localisation
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filtre par disponibilité (dates)
        if ($request->has('check_in') && $request->has('check_out')) {
            $query->whereDoesntHave('bookings', function ($q) use ($request) {
                $q->whereIn('status', ['pending', 'confirmed'])
                    ->where(function ($q) use ($request) {
                        $q->whereBetween('check_in_date', [$request->check_in, $request->check_out])
                            ->orWhereBetween('check_out_date', [$request->check_in, $request->check_out]);
                    });
            });
        }

        // Tri
        $sortBy  = $request->get('sort_by', 'price_per_night');
        $sortDir = $request->get('sort_dir', 'asc');
        $query->orderBy($sortBy, $sortDir);

        $resources = $query->paginate(12);

        return ResourceResource::collection($resources);
    }

    // ✅ Détail d'une ressource
    public function show($id)
    {
        $resource = Resource::with('category')->findOrFail($id);

        return new ResourceResource($resource);
    }

    // ✅ Disponibilités d'une ressource
    public function availability(Request $request, $id)
    {
        $resource = Resource::findOrFail($id);

        $request->validate([
            'month' => 'nullable|integer|min:1|max:12',
            'year'  => 'nullable|integer|min:2024',
        ]);

        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        // Récupère les réservations confirmées du mois
        $bookedDates = $resource->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereYear('check_in_date', $year)
            ->whereMonth('check_in_date', $month)
            ->get(['check_in_date', 'check_out_date']);

        // Récupère les indisponibilités manuelles
        $unavailableDates = $resource->availabilities()
            ->where('is_available', false)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->pluck('date');

        return response()->json([
            'resource_id'      => $resource->id,
            'month'            => $month,
            'year'             => $year,
            'booked_dates'     => $bookedDates,
            'unavailable_dates'=> $unavailableDates,
        ]);
    }
}
