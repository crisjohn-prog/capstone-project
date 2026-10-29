<?php

namespace App\Http\Controllers;

use App\Models\ServiceRecord;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $period = $request->input('period', 'month');
        $from = $period === 'year' ? now()->startOfYear() : now()->startOfMonth();
        $records = ServiceRecord::with(['serviceRequest.customer', 'technician'])->where('completed_at', '>=', $from)->latest('completed_at')->paginate(15)->withQueryString();
        $summary = [
            'received' => ServiceRequest::where('created_at', '>=', $from)->count(),
            'completed' => ServiceRecord::where('completed_at', '>=', $from)->count(),
            'pending' => ServiceRequest::whereIn('status', ['pending', 'approved'])->count(),
            'scheduled' => ServiceRequest::whereIn('status', ['scheduled', 'in_progress'])->count(),
        ];

        return view('reports.index', compact('records', 'summary', 'period'));
    }
}
