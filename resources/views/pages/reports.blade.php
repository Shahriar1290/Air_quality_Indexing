@extends('layouts.app')

@section('title', 'Reports')
@section('subtitle', 'Sensor data summary and statistics')

@section('content')

{{-- FILTERS --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="filter-pills d-flex flex-wrap gap-2">
                <a href="{{ route('reports', ['filter' => 'today']) }}" class="btn {{ $currentFilter === 'today' ? 'active' : '' }}">
                    <i class="fa-regular fa-calendar me-1"></i> Today
                </a>
                <a href="{{ route('reports', ['filter' => 'yesterday']) }}" class="btn {{ $currentFilter === 'yesterday' ? 'active' : '' }}">
                    <i class="fa-regular fa-calendar me-1"></i> Yesterday
                </a>
                <a href="{{ route('reports', ['filter' => 'week']) }}" class="btn {{ $currentFilter === 'week' ? 'active' : '' }}">
                    <i class="fa-regular fa-calendar me-1"></i> Last 7 Days
                </a>
                <a href="{{ route('reports', ['filter' => 'month']) }}" class="btn {{ $currentFilter === 'month' ? 'active' : '' }}">
                    <i class="fa-regular fa-calendar me-1"></i> Last Month
                </a>
                <button class="btn {{ $currentFilter === 'custom' ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#customDateFilter">
                    <i class="fa-regular fa-calendar me-1"></i> Custom Date
                </button>
            </div>
        </div>
        <div class="collapse mt-3 {{ $currentFilter === 'custom' ? 'show' : '' }}" id="customDateFilter">
            <form method="GET" action="{{ route('reports') }}" class="d-flex align-items-center gap-3">
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

@if($hasData)

{{-- OVERVIEW CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-lg-6">
        <div class="sensor-card">
            <div class="sensor-icon" style="background:rgba(99,102,241,0.1);color:#6366f1;">
                <i class="fa-solid fa-database"></i>
            </div>
            <div class="sensor-label">Total Readings</div>
            <div class="sensor-value">{{ $stats['count'] }}</div>
            <div class="sensor-meta">In selected period</div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6">
        <div class="sensor-card">
            <div class="sensor-icon" style="background:rgba(239,68,68,0.1);color:#ef4444;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="sensor-label">Danger Events</div>
            <div class="sensor-value">{{ $stats['danger_count'] }}</div>
            <div class="sensor-meta">Gas danger detected</div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6">
        <div class="sensor-card">
            <div class="sensor-icon" style="background:rgba(34,197,94,0.1);color:#22c55e;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="sensor-label">Safe Readings</div>
            <div class="sensor-value">{{ $stats['safe_count'] }}</div>
            <div class="sensor-meta">No gas danger</div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6">
        <div class="sensor-card">
            <div class="sensor-icon" style="background:rgba(234,179,8,0.1);color:#eab308;">
                <i class="fa-solid fa-chart-simple"></i>
            </div>
            <div class="sensor-label">Avg AQI</div>
            <div class="sensor-value">{{ $stats['avg_aqi'] }}</div>
            <div class="sensor-meta">Max: {{ $stats['max_aqi'] }}</div>
        </div>
    </div>
</div>

{{-- SENSOR STATISTICS --}}
<div class="row g-4 mb-4">
    {{-- Temperature Stats --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-temperature-half text-danger me-2"></i>Temperature</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div class="text-center">
                        <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;">Average</div>
                        <div style="font-size:22px;font-weight:800;color:#1e293b;">{{ $stats['avg_temperature'] }}°C</div>
                    </div>
                    <div class="text-center">
                        <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;">Min</div>
                        <div style="font-size:22px;font-weight:800;color:#22c55e;">{{ $stats['min_temperature'] }}°C</div>
                    </div>
                    <div class="text-center">
                        <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;">Max</div>
                        <div style="font-size:22px;font-weight:800;color:#ef4444;">{{ $stats['max_temperature'] }}°C</div>
                    </div>
                </div>
                @php $tempRange = $stats['max_temperature'] - $stats['min_temperature']; @endphp
                <div class="progress" style="height:8px;border-radius:4px;">
                    <div class="progress-bar bg-danger" style="width:{{ $tempRange > 0 ? (($stats['avg_temperature'] - $stats['min_temperature']) / $tempRange * 100) : 50 }}%;border-radius:4px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Humidity Stats --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-droplet text-primary me-2"></i>Humidity</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div class="text-center">
                        <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;">Average</div>
                        <div style="font-size:22px;font-weight:800;color:#1e293b;">{{ $stats['avg_humidity'] }}%</div>
                    </div>
                    <div class="text-center">
                        <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;">Min</div>
                        <div style="font-size:22px;font-weight:800;color:#22c55e;">{{ $stats['min_humidity'] }}%</div>
                    </div>
                    <div class="text-center">
                        <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;">Max</div>
                        <div style="font-size:22px;font-weight:800;color:#ef4444;">{{ $stats['max_humidity'] }}%</div>
                    </div>
                </div>
                @php $humRange = $stats['max_humidity'] - $stats['min_humidity']; @endphp
                <div class="progress" style="height:8px;border-radius:4px;">
                    <div class="progress-bar bg-primary" style="width:{{ $humRange > 0 ? (($stats['avg_humidity'] - $stats['min_humidity']) / $humRange * 100) : 50 }}%;border-radius:4px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gas Stats --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-fire-flame-curved text-purple me-2"></i>Gas Sensors</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <div class="text-center">
                        <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;">MQ2 Avg</div>
                        <div style="font-size:22px;font-weight:800;color:#1e293b;">{{ $stats['avg_mq2'] }}</div>
                    </div>
                    <div class="text-center">
                        <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;">MQ2 Max</div>
                        <div style="font-size:22px;font-weight:800;color:#ef4444;">{{ $stats['max_mq2'] }}</div>
                    </div>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <div class="text-center">
                        <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;">MQ5 Avg</div>
                        <div style="font-size:22px;font-weight:800;color:#1e293b;">{{ $stats['avg_mq5'] }}</div>
                    </div>
                    <div class="text-center">
                        <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;">MQ5 Max</div>
                        <div style="font-size:22px;font-weight:800;color:#ef4444;">{{ $stats['max_mq5'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- AIR QUALITY BREAKDOWN --}}
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-lungs text-success me-2"></i>Air Quality Distribution</h6>
            </div>
            <div class="card-body">
                @php $total = $stats['count']; @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size:12px;font-weight:600;color:#16a34a;">Good</span>
                        <span style="font-size:12px;color:#94a3b8;">{{ $stats['good_air'] }} ({{ $total > 0 ? round($stats['good_air'] / $total * 100) : 0 }}%)</span>
                    </div>
                    <div class="progress" style="height:10px;border-radius:5px;">
                        <div class="progress-bar bg-success" style="width:{{ $total > 0 ? ($stats['good_air'] / $total * 100) : 0 }}%;border-radius:5px;"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size:12px;font-weight:600;color:#ca8a04;">Moderate</span>
                        <span style="font-size:12px;color:#94a3b8;">{{ $stats['moderate_air'] }} ({{ $total > 0 ? round($stats['moderate_air'] / $total * 100) : 0 }}%)</span>
                    </div>
                    <div class="progress" style="height:10px;border-radius:5px;">
                        <div class="progress-bar bg-warning" style="width:{{ $total > 0 ? ($stats['moderate_air'] / $total * 100) : 0 }}%;border-radius:5px;"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size:12px;font-weight:600;color:#ea580c;">Unhealthy</span>
                        <span style="font-size:12px;color:#94a3b8;">{{ $stats['unhealthy_air'] }} ({{ $total > 0 ? round($stats['unhealthy_air'] / $total * 100) : 0 }}%)</span>
                    </div>
                    <div class="progress" style="height:10px;border-radius:5px;">
                        <div class="progress-bar bg-orange" style="width:{{ $total > 0 ? ($stats['unhealthy_air'] / $total * 100) : 0 }}%;border-radius:5px;background-color:#f97316;"></div>
                    </div>
                </div>
                <div class="mb-0">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="font-size:12px;font-weight:600;color:#dc2626;">Hazardous</span>
                        <span style="font-size:12px;color:#94a3b8;">{{ $stats['hazardous_air'] }} ({{ $total > 0 ? round($stats['hazardous_air'] / $total * 100) : 0 }}%)</span>
                    </div>
                    <div class="progress" style="height:10px;border-radius:5px;">
                        <div class="progress-bar bg-danger" style="width:{{ $total > 0 ? ($stats['hazardous_air'] / $total * 100) : 0 }}%;border-radius:5px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Dust & AQI Stats --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-smog text-orange me-2"></i>Dust & AQI Summary</h6>
            </div>
            <div class="card-body">
                <div class="row text-center mb-4">
                    <div class="col-6">
                        <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;">Avg Dust</div>
                        <div style="font-size:22px;font-weight:800;color:#1e293b;">{{ $stats['avg_dust'] }} <span style="font-size:12px;color:#94a3b8;">µg/m³</span></div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;">Max Dust</div>
                        <div style="font-size:22px;font-weight:800;color:#ef4444;">{{ $stats['max_dust'] }} <span style="font-size:12px;color:#94a3b8;">µg/m³</span></div>
                    </div>
                </div>
                <hr style="border-color:#f1f5f9;">
                <div class="row text-center">
                    <div class="col-6">
                        <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;">Avg AQI</div>
                        <div style="font-size:22px;font-weight:800;color:#1e293b;">{{ $stats['avg_aqi'] }}</div>
                    </div>
                    <div class="col-6">
                        <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;">Max AQI</div>
                        <div style="font-size:22px;font-weight:800;color:#ef4444;">{{ $stats['max_aqi'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@else
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fa-solid fa-chart-bar text-muted" style="font-size:48px;opacity:0.3;"></i>
        <p class="text-muted mt-3" style="font-size:14px;">No data available for the selected period.</p>
        <a href="{{ route('reports', ['filter' => 'week']) }}" class="btn btn-sm" style="background:var(--color-primary);color:#fff;border-radius:10px;">View Last 7 Days</a>
    </div>
</div>
@endif

@endsection
