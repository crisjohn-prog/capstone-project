<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $query = ServiceRequest::query();
        if ($user->role === 'customer') {
            $query->where('customer_id', $user->id);
        }
        if ($user->role === 'technician') {
            $query->where('technician_id', $user->id);
        }

        $base = clone $query;
        $stats = [
            'active' => (clone $base)->whereIn('status', ['pending', 'approved', 'scheduled', 'in_progress'])->count(),
            'scheduled' => (clone $base)->where('status', 'scheduled')->count(),
            'completed' => (clone $base)->where('status', 'completed')->count(),
            'technicians' => $user->isStaff() ? User::where('role', 'technician')->count() : null,
        ];
        $requests = $query->with(['customer', 'technician'])->latest()->take(7)->get();
        $servicesByType = $user->isStaff()
            ? ServiceRequest::selectRaw('service_type, count(*) as total')->groupBy('service_type')->pluck('total', 'service_type')
            : collect();

        return view('technician.dashboard', compact('stats', 'requests', 'servicesByType'));
    }
}
