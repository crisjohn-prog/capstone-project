<?php

namespace App\Http\Controllers;

use App\Models\ServiceRecord;
use App\Models\ServiceRequest as WorkOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkOrder::with(['customer', 'technician'])->latest();
        if ($request->user()->role === 'customer') {
            $query->where('customer_id', $request->user()->id);
        }
        if ($request->user()->role === 'technician') {
            $query->where('technician_id', $request->user()->id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $query->where(fn ($q) => $q->where('reference', 'like', "%$term%")
                ->orWhere('service_address', 'like', "%$term%")
                ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%$term%")));
        }
        $requests = $query->paginate(12)->withQueryString();

        return view('request.index', compact('requests'));
    }

    public function create()
    {
        return view('request.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_type' => ['required', Rule::in(['Repair', 'Installation', 'Cleaning', 'Maintenance', 'Inspection'])],
            'unit_brand' => ['nullable', 'string', 'max:100'], 'unit_model' => ['nullable', 'string', 'max:100'],
            'unit_location' => ['nullable', 'string', 'max:120'], 'service_address' => ['required', 'string', 'max:255'],
            'issue_description' => ['required', 'string', 'max:4000'], 'preferred_at' => ['nullable', 'date', 'after:now'],
        ]);
        $data['customer_id'] = $request->user()->id;
        $data['reference'] = 'AC-'.now()->format('ymd').'-'.strtoupper(str()->random(5));
        $workOrder = WorkOrder::create($data);

        return redirect()->route('requests.show', $workOrder)->with('success', "Request {$workOrder->reference} submitted. We'll review the details shortly.");
    }

    public function show(Request $request, WorkOrder $serviceRequest)
    {
        $this->authorizeView($request->user(), $serviceRequest);
        $serviceRequest->load(['customer', 'technician', 'record.technician']);
        $technicians = $request->user()->isStaff() ? User::where('role', 'technician')->orderBy('name')->get() : collect();

        return view('request.show', ['workOrder' => $serviceRequest, 'technicians' => $technicians]);
    }

    public function schedule(Request $request, WorkOrder $serviceRequest)
    {
        $data = $request->validate([
            'technician_id' => ['required', Rule::exists('users', 'id')->where('role', 'technician')],
            'scheduled_at' => ['required', 'date', 'after:now'], 'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'staff_notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $start = Carbon::parse($data['scheduled_at']);
        $conflict = WorkOrder::where('technician_id', $data['technician_id'])
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->whereBetween('scheduled_at', [$start->copy()->subHours(2), $start->copy()->addHours(2)])
            ->whereKeyNot($serviceRequest->id)->exists();
        if ($conflict) {
            return back()->withErrors(['scheduled_at' => 'This technician has another job within two hours of that appointment.'])->withInput();
        }
        $serviceRequest->update([...$data, 'status' => 'scheduled']);

        return back()->with('success', 'Technician assigned and appointment scheduled.');
    }

    public function updateStatus(Request $request, WorkOrder $serviceRequest)
    {
        $allowed = $request->user()->role === 'technician'
            ? ['in_progress', 'completed']
            : ['approved', 'cancelled'];
        $data = $request->validate(['status' => ['required', Rule::in($allowed)]]);
        if ($request->user()->role === 'technician' && $serviceRequest->technician_id !== $request->user()->id) {
            abort(403);
        }
        if ($data['status'] === 'in_progress' && $serviceRequest->status !== 'scheduled') {
            abort(422, 'Only scheduled jobs can be started.');
        }
        if ($data['status'] === 'completed' && ! $serviceRequest->technician_id) {
            abort(422, 'Assign a technician before completing this job.');
        }
        if ($data['status'] === 'completed') {
            $request->validate([
                'diagnosis' => ['required', 'string', 'max:4000'],
                'work_performed' => ['required', 'string', 'max:4000'],
                'parts_used' => ['nullable', 'string', 'max:2000'],
                'recommendations' => ['nullable', 'string', 'max:2000'],
            ]);
        }

        DB::transaction(function () use ($serviceRequest, $data, $request) {
            $serviceRequest->update(['status' => $data['status']]);
            if ($data['status'] === 'completed') {
                ServiceRecord::updateOrCreate(['service_request_id' => $serviceRequest->id], [
                    'technician_id' => $serviceRequest->technician_id,
                    'diagnosis' => $request->input('diagnosis'),
                    'work_performed' => $request->input('work_performed'),
                    'parts_used' => $request->input('parts_used'),
                    'recommendations' => $request->input('recommendations'),
                    'completed_at' => now(),
                ]);
            }
        });

        return back()->with('success', $data['status'] === 'completed' ? 'Job completed and maintenance record saved.' : 'Request status updated.');
    }

    private function authorizeView(User $user, WorkOrder $workOrder): void
    {
        if ($user->role === 'customer' && $workOrder->customer_id !== $user->id) {
            abort(403);
        }
        if ($user->role === 'technician' && $workOrder->technician_id !== $user->id) {
            abort(403);
        }
    }
}
