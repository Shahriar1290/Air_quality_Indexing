@extends('layouts.app')

@section('title', 'Sensor History')
@section('subtitle', 'Browse and filter all sensor readings')

@section('content')

{{-- FILTERS --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="filter-pills d-flex flex-wrap gap-2">
                <a href="{{ route('history', ['filter' => 'today']) }}" class="btn {{ $currentFilter === 'today' ? 'active' : '' }}">
                    <i class="fa-regular fa-calendar me-1"></i> Today
                </a>
                <a href="{{ route('history', ['filter' => 'yesterday']) }}" class="btn {{ $currentFilter === 'yesterday' ? 'active' : '' }}">
                    <i class="fa-regular fa-calendar me-1"></i> Yesterday
                </a>
                <a href="{{ route('history', ['filter' => 'week']) }}" class="btn {{ $currentFilter === 'week' ? 'active' : '' }}">
                    <i class="fa-regular fa-calendar me-1"></i> Last 7 Days
                </a>
                <a href="{{ route('history', ['filter' => 'month']) }}" class="btn {{ $currentFilter === 'month' ? 'active' : '' }}">
                    <i class="fa-regular fa-calendar me-1"></i> Last Month
                </a>
                <button class="btn {{ $currentFilter === 'custom' ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#customDateFilter">
                    <i class="fa-regular fa-calendar me-1"></i> Custom Date
                </button>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted" style="font-size:12px;">
                    <i class="fa-solid fa-database me-1"></i> {{ $readings->total() }} total records
                </span>
            </div>
        </div>

        {{-- Custom Date Filter --}}
        <div class="collapse mt-3 {{ $currentFilter === 'custom' ? 'show' : '' }}" id="customDateFilter">
            <form method="GET" action="{{ route('history') }}" class="d-flex align-items-center gap-3">
                <input type="hidden" name="filter" value="custom">
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted" style="font-size:12px;">From:</label>
                    <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm" style="width:160px;border-radius:10px;">
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="text-muted" style="font-size:12px;">To:</label>
                    <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm" style="width:160px;border-radius:10px;">
                </div>
                <button type="submit" class="btn btn-sm" style="background:var(--color-primary);color:#fff;border-radius:10px;padding:6px 16px;">
                    <i class="fa-solid fa-filter me-1"></i> Apply
                </button>
            </form>
        </div>
    </div>
</div>

{{-- SENSOR TABLE --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0"><i class="fa-solid fa-table text-primary me-2"></i>Sensor Readings</h6>
        <span class="badge bg-primary rounded-pill">Page {{ $readings->currentPage() }} of {{ $readings->lastPage() }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th class="text-center">Temp</th>
                        <th class="text-center">Humidity</th>
                        <th class="text-center">MQ2</th>
                        <th class="text-center">MQ5</th>
                        <th class="text-center">Dust</th>
                        <th class="text-center">AQI</th>
                        <th class="text-center">Gas Status</th>
                        <th class="text-center">Air Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($readings as $reading)
                    <tr>
                        <td>
                            <div style="font-size:12px;color:#64748b;">
                                <i class="fa-regular fa-clock me-1"></i>{{ $reading->created_at->format('M d, Y') }}
                            </div>
                            <div style="font-size:11px;color:#94a3b8;">{{ $reading->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="text-center">
                            @php
                                $tempColor = match(true) {
                                    $reading->temperature >= 40 => '#ef4444',
                                    $reading->temperature >= 35 => '#eab308',
                                    default => '#22c55e',
                                };
                            @endphp
                            <span class="fw-bold" style="color:{{ $tempColor }}">
                                {{ $reading->temperature }}°C
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold" style="color:{{ $reading->humidity >= 80 || $reading->humidity < 30 ? '#eab308' : '#3b82f6' }}">
                                {{ $reading->humidity }}%
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold">{{ $reading->mq2 }}</span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold">{{ $reading->mq5 }}</span>
                        </td>
                        <td class="text-center">
                            @php
                                $dustColor = match(true) {
                                    $reading->dust >= 300 => '#ef4444',
                                    $reading->dust >= 150 => '#eab308',
                                    default => '#22c55e',
                                };
                            @endphp
                            <span class="fw-bold" style="color:{{ $dustColor }}">
                                {{ $reading->dust }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="fw-bold">{{ $reading->estimated_aqi }}</span>
                        </td>
                        <td class="text-center">
                            <span class="status-badge {{ $reading->gas_status === 'DANGER' ? 'danger' : 'safe' }}">
                                <i class="fa-solid {{ $reading->gas_status === 'DANGER' ? 'fa-triangle-exclamation' : 'fa-shield-halved' }}"></i>
                                {{ $reading->gas_status }}
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                                $airColorMap = ['Good' => 'good', 'Moderate' => 'moderate', 'Unhealthy' => 'unhealthy', 'Hazardous' => 'hazardous'];
                            @endphp
                            <span class="status-badge {{ $airColorMap[$reading->air_status] ?? 'good' }}">
                                {{ $reading->air_status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="fa-solid fa-inbox text-muted" style="font-size:40px;opacity:0.3;"></i>
                            <p class="text-muted mt-2" style="font-size:13px;">No sensor readings found for this filter.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($readings->hasPages())
    <div class="card-footer d-flex justify-content-center py-3">
        {{ $readings->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@endsection
