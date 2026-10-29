<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TechnicianController extends Controller
{
    public function index()
    {
        $technicians = User::where('role', 'technician')->withCount(['assignedRequests as open_jobs' => fn ($q) => $q->whereIn('status', ['scheduled', 'in_progress'])])->orderBy('name')->paginate(12);

        return view('technician.index', compact('technicians'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:30'], 'specialty' => ['nullable', 'string', 'max:120'],
            'password' => ['required', 'min:8'],
        ]);
        User::create([...$data, 'role' => 'technician', 'password' => Hash::make($data['password'])]);

        return back()->with('success', 'Technician account created. Share the temporary password securely.');
    }
}
