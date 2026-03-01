<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Resource;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // ✅ Stats dashboard
    public function stats()
    {
        $totalBookings    = Booking::count();
        $totalRevenue     = Booking::where('payment_status', 'succeeded')->sum('total_price');
        $activeBookings   = Booking::whereIn('status', ['pending', 'confirmed'])->count();
        $totalResources   = Resource::where('is_active', true)->count();
        $totalUsers       = User::where('role', 'client')->count();

        // Réservations par mois (6 derniers mois)
        $bookingsByMonth = Booking::selectRaw("TO_CHAR(created_at, 'Mon') as month, COUNT(*) as count")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupByRaw("TO_CHAR(created_at, 'Mon'), DATE_TRUNC('month', created_at)")
            ->orderByRaw("DATE_TRUNC('month', created_at)")
            ->get();

        return response()->json([
            'total_bookings'   => $totalBookings,
            'total_revenue'    => $totalRevenue,
            'active_bookings'  => $activeBookings,
            'total_resources'  => $totalResources,
            'total_users'      => $totalUsers,
            'bookings_by_month'=> $bookingsByMonth,
        ]);
    }

    // ✅ Liste utilisateurs
    public function users()
    {
        $users = User::latest()->paginate(20);
        return UserResource::collection($users);
    }

    // ✅ Changer le rôle d'un utilisateur
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:client,admin,manager',
        ]);

        $user->update(['role' => $request->role]);

        return new UserResource($user);
    }
}
