@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Real-time air quality monitoring overview')

@section('content')

{{-- AIR QUALITY STATUS BANNER --}}
@if($hasAlerts)
<div class="card mb-4 border-0" style="background: linear-gradient(135deg, #fef2f2, #fff7ed); border-left: 4px solid #ef4444 !important;">
    <div class="card-body d-flex align-items-center justify-content-between py-4 px-5">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1e293b;">
                Air quality is <span class="text-danger">{{ $latest?->air_status ?? 'Unknown' }}</span>
            </h4>
            <p class="mb-0 text-muted" style="font-size:13px;">
                {{ $alertCount }} active {{ Str::plural('alert', $alertCount) }} detected. Review conditions below.
            </p>
        </div>
        <div class="d-flex gap-2">
            @foreach(['fa-skull-crossbones', 'fa-cloud', 'fa-smog'] as $icon)
            <span class="badge bg-light text-dark" style="font-size:11px;padding:6px 12px;">
                <i class="fa-solid {{ $icon }} me-1"></i> Active
            </span>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ALERTS SECTION --}}
@if($hasAlerts)
<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0"><i class="fa-solid fa-bell text-danger me-2"></i>Active Alerts</h6>
        <span class="badge bg-danger rounded-pill">{{ $alertCount }}</span>
    </div>
    <div class="card-body" id="alertsContainer">
        @foreach($alerts as $alert)
        @php
            $alertBg = match($alert['type']) {
                'danger' => 'rgba(239,68,68,0.1)',
                'warning' => 'rgba(234,179,8,0.1)',
                'orange' => 'rgba(249,115,22,0.1)',
                default => 'rgba(99,102,241,0.1)',
            };
            $alertColor = match($alert['type']) {
                'danger' => '#ef4444',
                'warning' => '#eab308',
                'orange' => '#f97316',
                default => '#6366f1',
            };
            $alertTitleColor = match($alert['type']) {
                'danger' => '#dc2626',
                'warning' => '#ca8a04',
                'orange' => '#ea580c',
                default => '#4f46e5',
            };
        @endphp
        <div class="alert-item alert-{{ $alert['type'] }}">
            <div class="alert-icon" style="background:{{ $alertBg }};color:{{ $alertColor }};">
                <i class="{{ $alert['icon'] }}"></i>
            </div>
            <div class="flex-grow-1">
                <div class="alert-title" style="color:{{ $alertTitleColor }};">{{ $alert['title'] }}</div>
                <p class="alert-msg">{{ $alert['message'] }}</p>
            </div>
            <span class="alert-time">{{ $alert['time'] }}</span>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- SUMMARY CARDS --}}
<div class="row g-3 mb-4">
    {{-- Temperature --}}
    <div class="col-xl-2 col-lg-4 col-md-4 col-6">
        <div class="sensor-card" id="cardTemperature">
            <div class="sensor-icon" style="background:rgba(239,68,68,0.1);color:#ef4444;">
                <i class="fa-solid fa-temperature-half"></i>
            </div>
            <div class="sensor-label">Temperature</div>
            <div class="sensor-value" id="valTemperature">
                {{ $latest->temperature ?? '--' }}<span class="sensor-unit">°C</span>
            </div>
            <div class="sensor-meta" id="metaTemperature">
                {{ $latest?->temperature_status ?? '' }}
            </div>
        </div>
    </div>

    {{-- Humidity --}}
    <div class="col-xl-2 col-lg-4 col-md-4 col-6">
        <div class="sensor-card" id="cardHumidity">
            <div class="sensor-icon" style="background:rgba(59,130,246,0.1);color:#3b82f6;">
                <i class="fa-solid fa-droplet"></i>
            </div>
            <div class="sensor-label">Humidity</div>
            <div class="sensor-value" id="valHumidity">
                {{ $latest->humidity ?? '--' }}<span class="sensor-unit">%</span>
            </div>
            <div class="sensor-meta" id="metaHumidity">
                {{ $latest?->humidity_status ?? '' }}
            </div>
        </div>
    </div>

    {{-- MQ2 --}}
    <div class="col-xl-2 col-lg-4 col-md-4 col-6">
        <div class="sensor-card" id="cardMQ2">
            <div class="sensor-icon" style="background:rgba(168,85,247,0.1);color:#a855f7;">
                <i class="fa-solid fa-fire-flame-curved"></i>
            </div>
            <div class="sensor-label">MQ2 Gas</div>
            <div class="sensor-value" id="valMQ2">
                {{ $latest->mq2 ?? '--' }}<span class="sensor-unit">ppm</span>
            </div>
            <div class="sensor-meta">LPG / Smoke / CO</div>
        </div>
    </div>

    {{-- MQ5 --}}
    <div class="col-xl-2 col-lg-4 col-md-4 col-6">
        <div class="sensor-card" id="cardMQ5">
            <div class="sensor-icon" style="background:rgba(234,179,8,0.1);color:#eab308;">
                <i class="fa-solid fa-gas-pump"></i>
            </div>
            <div class="sensor-label">MQ5 Gas</div>
            <div class="sensor-value" id="valMQ5">
                {{ $latest->mq5 ?? '--' }}<span class="sensor-unit">ppm</span>
            </div>
            <div class="sensor-meta">Natural Gas / Methane</div>
        </div>
    </div>

    {{-- Dust --}}
    <div class="col-xl-2 col-lg-4 col-md-4 col-6">
        <div class="sensor-card" id="cardDust">
            <div class="sensor-icon" style="background:rgba(249,115,22,0.1);color:#f97316;">
                <i class="fa-solid fa-smog"></i>
            </div>
            <div class="sensor-label">Dust Level</div>
            <div class="sensor-value" id="valDust">
                {{ $latest->dust ?? '--' }}<span class="sensor-unit">µg/m³</span>
            </div>
            <div class="sensor-meta" id="metaDust">
                {{ $latest?->dust_status ?? '' }}
            </div>
        </div>
    </div>

    {{-- AQI --}}
    <div class="col-xl-2 col-lg-4 col-md-4 col-6">
        <div class="sensor-card" id="cardAQI">
            @php
                $aqiBg = match($latest?->aqi_color ?? 'success') {
                    'success' => 'rgba(34,197,94,0.1)',
                    'warning' => 'rgba(234,179,8,0.1)',
                    'orange' => 'rgba(249,115,22,0.1)',
                    default => 'rgba(239,68,68,0.1)',
                };
                $aqiColor = match($latest?->aqi_color ?? 'success') {
                    'success' => '#22c55e',
                    'warning' => '#eab308',
                    'orange' => '#f97316',
                    default => '#ef4444',
                };
            @endphp
            <div class="sensor-icon" style="background:{{ $aqiBg }};color:{{ $aqiColor }};">
                <i class="fa-solid fa-lungs"></i>
            </div>
            <div class="sensor-label">AQI</div>
            <div class="sensor-value" id="valAQI">
                {{ $latest->estimated_aqi ?? '--' }}
            </div>
            <div class="sensor-meta" id="metaAQI">
                <span class="status-badge {{ ($latest?->aqi_color ?? 'good') }}" id="badgeAQI">
                    {{ $latest?->aqi_label ?? '--' }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- CHARTS + LIVE STATUS --}}
<div class="row g-4 mb-4">
    {{-- Charts --}}
    <div class="col-xl-8 col-lg-7">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-chart-area text-primary me-2"></i>Live Trend</h6>
                <span class="text-muted" style="font-size:12px;">Temperature, Humidity & AQI — Last 24 readings</span>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="mainChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Live Status Panel --}}
    <div class="col-xl-4 col-lg-5" id="live-status">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-signal text-success me-2"></i>Live Status</h6>
                <span class="live-dot"></span>
            </div>
            <div class="card-body live-panel" id="livePanel">
                <div class="live-item">
                    <span class="live-label"><i class="fa-solid fa-temperature-half text-danger"></i> Temperature</span>
                    <span class="live-value" id="liveTemp">{{ $latest->temperature ?? '--' }}°C</span>
                </div>
                <div class="live-item">
                    <span class="live-label"><i class="fa-solid fa-droplet text-primary"></i> Humidity</span>
                    <span class="live-value" id="liveHumid">{{ $latest->humidity ?? '--' }}%</span>
                </div>
                <div class="live-item">
                    <span class="live-label"><i class="fa-solid fa-lungs text-success"></i> AQI</span>
                    <span class="live-value" id="liveAQI">{{ $latest->estimated_aqi ?? '--' }}</span>
                </div>
                <div class="live-item">
                    <span class="live-label"><i class="fa-solid fa-fire-flame-curved text-purple"></i> MQ2</span>
                    <span class="live-value" id="liveMQ2">{{ $latest->mq2 ?? '--' }}</span>
                </div>
                <div class="live-item">
                    <span class="live-label"><i class="fa-solid fa-gas-pump text-warning"></i> MQ5</span>
                    <span class="live-value" id="liveMQ5">{{ $latest->mq5 ?? '--' }}</span>
                </div>
                <div class="live-item">
                    <span class="live-label"><i class="fa-solid fa-smog text-orange"></i> Dust</span>
                    <span class="live-value" id="liveDust">{{ $latest->dust ?? '--' }}</span>
                </div>
                <div class="live-item">
                    <span class="live-label"><i class="fa-solid fa-shield-halved"></i> Gas Status</span>
                    <span class="live-value" id="liveGas">
                        <span class="status-badge {{ $latest?->gas_status_color ?? 'safe' }}">{{ $latest->gas_status ?? '--' }}</span>
                    </span>
                </div>
                <div class="live-item">
                    <span class="live-label"><i class="fa-solid fa-cloud"></i> Air Status</span>
                    <span class="live-value" id="liveAir">
                        <span class="status-badge {{ ($latest?->aqi_color ?? 'good') }}">{{ $latest->air_status ?? '--' }}</span>
                    </span>
                </div>
                <div class="live-item" style="border-bottom:none;">
                    <span class="live-label"><i class="fa-solid fa-clock"></i> Last Updated</span>
                    <span class="live-value text-muted" style="font-size:12px;" id="lastUpdated">{{ $latest?->created_at?->format('H:i:s') ?? '--' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- GAS CHARTS ROW --}}
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-fire-flame-curved text-purple me-2"></i>MQ2 Trend</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="mq2Chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-gas-pump text-warning me-2"></i>MQ5 Trend</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="mq5Chart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-smog text-orange me-2"></i>Dust Trend</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="dustChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-lungs text-success me-2"></i>AQI Trend</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="aqiChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Chart instances
    let mainChart, mq2Chart, mq5Chart, dustChart, aqiChart;

    // Chart.js defaults
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#94a3b8';

    const chartColors = {
        temperature: { border: '#ef4444', bg: 'rgba(239,68,68,0.08)' },
        humidity: { border: '#3b82f6', bg: 'rgba(59,130,246,0.08)' },
        mq2: { border: '#a855f7', bg: 'rgba(168,85,247,0.08)' },
        mq5: { border: '#eab308', bg: 'rgba(234,179,8,0.08)' },
        dust: { border: '#f97316', bg: 'rgba(249,115,22,0.08)' },
        aqi: { border: '#22c55e', bg: 'rgba(34,197,94,0.08)' },
    };

    function createLineChart(canvasId, labels, datasets, yTitle = '') {
        const ctx = document.getElementById(canvasId).getContext('2d');
        return new Chart(ctx, {
            type: 'line',
            data: { labels, datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: datasets.length > 1, position: 'top', labels: { usePointStyle: true, padding: 16 } },
                    tooltip: { backgroundColor: '#1e293b', titleColor: '#f1f5f9', bodyColor: '#cbd5e1', padding: 12, cornerRadius: 10 }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 10 } },
                    y: { grid: { color: '#f1f5f9' }, title: { display: !!yTitle, text: yTitle }, beginAtZero: false }
                },
                elements: { point: { radius: 0, hoverRadius: 5 }, line: { tension: 0.4, borderWidth: 2 } }
            }
        });
    }

    function makeDataset(label, data, color) {
        return {
            label,
            data,
            borderColor: color.border,
            backgroundColor: color.bg,
            fill: true,
        };
    }

    // Initialize charts with initial data
    function initCharts(data) {
        mainChart = createLineChart('mainChart', data.labels, [
            makeDataset('Temp (°C)', data.temperature, chartColors.temperature),
            makeDataset('Humidity (%)', data.humidity, chartColors.humidity),
        ]);
        mq2Chart = createLineChart('mq2Chart', data.labels, [makeDataset('MQ2', data.mq2, chartColors.mq2)], 'ppm');
        mq5Chart = createLineChart('mq5Chart', data.labels, [makeDataset('MQ5', data.mq5, chartColors.mq5)], 'ppm');
        dustChart = createLineChart('dustChart', data.labels, [makeDataset('Dust', data.dust, chartColors.dust)], 'µg/m³');
        aqiChart = createLineChart('aqiChart', data.labels, [makeDataset('AQI', data.aqi, chartColors.aqi)], 'AQI');
    }

    function updateCharts(data) {
        [mainChart, mq2Chart, mq5Chart, dustChart, aqiChart].forEach(c => {
            if (c) { c.data.labels = data.labels; }
        });
        if (mainChart) { mainChart.data.datasets[0].data = data.temperature; mainChart.data.datasets[1].data = data.humidity; mainChart.update('none'); }
        if (mq2Chart) { mq2Chart.data.datasets[0].data = data.mq2; mq2Chart.update('none'); }
        if (mq5Chart) { mq5Chart.data.datasets[0].data = data.mq5; mq5Chart.update('none'); }
        if (dustChart) { dustChart.data.datasets[0].data = data.dust; dustChart.update('none'); }
        if (aqiChart) { aqiChart.data.datasets[0].data = data.aqi; aqiChart.update('none'); }
    }

    // Fetch live data
    async function fetchLiveData() {
        try {
            const res = await fetch('{{ route("ajax.live") }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            if (!json.success) return;
            if (!json.data) {
                console.warn('No sensor data available yet.');
                return;
            }

            const d = json.data;

            // Update summary cards
            document.getElementById('valTemperature').innerHTML = d.temperature + '<span class="sensor-unit">°C</span>';
            document.getElementById('valHumidity').innerHTML = d.humidity + '<span class="sensor-unit">%</span>';
            document.getElementById('valMQ2').innerHTML = d.mq2 + '<span class="sensor-unit">ppm</span>';
            document.getElementById('valMQ5').innerHTML = d.mq5 + '<span class="sensor-unit">ppm</span>';
            document.getElementById('valDust').innerHTML = d.dust + '<span class="sensor-unit">µg/m³</span>';
            document.getElementById('valAQI').innerHTML = d.estimated_aqi;
            document.getElementById('badgeAQI').className = 'status-badge ' + d.aqi_color;
            document.getElementById('badgeAQI').textContent = d.aqi_label;

            // Update live panel
            document.getElementById('liveTemp').textContent = d.temperature + '°C';
            document.getElementById('liveHumid').textContent = d.humidity + '%';
            document.getElementById('liveAQI').textContent = d.estimated_aqi;
            document.getElementById('liveMQ2').textContent = d.mq2;
            document.getElementById('liveMQ5').textContent = d.mq5;
            document.getElementById('liveDust').textContent = d.dust;

            const gasEl = document.getElementById('liveGas');
            gasEl.innerHTML = '<span class="status-badge ' + d.gas_status_color + '">' + d.gas_status + '</span>';
            const airEl = document.getElementById('liveAir');
            airEl.innerHTML = '<span class="status-badge ' + d.aqi_color + '">' + d.air_status + '</span>';

            document.getElementById('lastUpdated').textContent = formatTime(d.created_at);

            // Check for danger alerts
            if (d.gas_status === 'DANGER') {
                showToast('Danger gas detected! MQ2: ' + d.mq2 + ', MQ5: ' + d.mq5, 'danger');
            }
        } catch (e) {
            console.error('Live fetch error:', e);
        }
    }

    // Fetch chart data
    async function fetchChartData() {
        try {
            const res = await fetch('{{ route("ajax.chart") }}?limit=24', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            if (json.success) updateCharts(json.data);
        } catch (e) {
            console.error('Chart fetch error:', e);
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        const initialData = @json($chartData);
        initCharts(initialData);

        // Fetch immediately on load
        fetchLiveData();
        fetchChartData();

        // Auto-refresh every 5 seconds
        setInterval(fetchLiveData, 5000);
        setInterval(fetchChartData, 5000);
    });
</script>
@endpush
