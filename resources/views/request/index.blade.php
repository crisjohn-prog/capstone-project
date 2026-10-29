@extends('layouts.app')
@section('title', 'Service requests')
@section('content')
<div class="page-intro"><div><div class="eyebrow">SERVICE OPERATIONS</div>
<h1>Service requests</h1><p>Review request details, monitor progress, and coordinate the next step.</p>
</div>
@if(auth()->user()->role === 'customer')
<a class="button primary" href="{{ route('requests.create') }}">＋ New request</a>
@endif
</div>
<section class="panel"><form method="GET" class="filters">
    @csrf
    <div class="searchbox"><span>⌕</span><input name="q" value="{{ request('q') }}" placeholder="Search reference, customer, or address">
    </div>
    <select name="status"><option value="">All statuses</option>
        @foreach(['pending','approved','scheduled','in_progress','completed','cancelled'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}
            </option>
            @endforeach
        </select>
        <button class="button secondary">Filter</button>
        @if(request()->hasAny(['q','status']))<a class="clear-link" href="{{ route('requests.index') }}">Clear</a>
        @endif
    </form>@csrf<div class="table-wrap"><table><thead><tr><th>REFERENCE</th><th>CUSTOMER</th><th>SERVICE</th><th>TECHNICIAN</th><th>STATUS</th><th>SCHEDULE</th><th></th></tr></thead><tbody>@forelse($requests as $item)<tr><td><a class="ref" href="{{ route('requests.show', $item) }}">{{ $item->reference }}</a><small class="table-sub">{{ $item->created_at->format('M d, Y') }}</small></td><td>{{ $item->customer->name }}</td><td>{{ $item->service_type }}</td><td>{{ $item->technician?->name ?? 'Unassigned' }}</td><td><span class="badge {{ $item->status }}">{{ str_replace('_', ' ', $item->status) }}</span></td><td>{{ $item->scheduled_at?->format('M d, Y · g:i A') ?? '—' }}</td><td><a href="{{ route('requests.show', $item) }}" class="arrow-link">↗</a></td></tr>@empty<tr><td colspan="7"><div class="empty"><div class="empty-icon">⌕</div><b>No matching requests</b><span>Try a different search or filter.</span></div></td></tr>@endforelse</tbody></table></div><div class="pagination-row">{{ $requests->links() }}</div></section>
@endsection
