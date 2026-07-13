@extends('layouts.app')

@section('title', 'Analytics')
@section('subtitle', 'In-depth sensor data analysis and trends')

@section('content')

{{-- AIR QUALITY DISTRIBUTION --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-chart-area text-primary me-2"></i>All Sensors — Last 50 Readings</h6>
            </div>
            <div class="card-body">
                <div style="height:350px;">
                    <canvas id="allSensorsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-chart-pie text-warning me-2"></i>AQI Distribution</h6>
            </div>
            <div class="card-body">
                <div style="height:280px;">
                    <canvas id="aqiPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- PER-SENSOR CHARTS --}}
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h6 class="fw-bold mb-0"><i class="fa-solid fa-temperature-half text-danger me-2"></i>Temperature History</h6></div>
            <div class="card-body"><div class="chart-container"><canvas id="analyticsTempChart"></canvas></div></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h6 class="fw-bold mb-0"><i class="fa-solid fa-droplet text-primary me-2"></i>Humidity History</h6></div>
            <div class="card-body"><div class="chart-container"><canvas id="analyticsHumidChart"></canvas></div></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h6 class="fw-bold mb-0"><i class="fa-solid fa-fire-flame-curved text-purple me-2"></i>MQ2 History</h6></div>
            <div class="card-body"><div class="chart-container"><canvas id="analyticsMQ2Chart"></canvas></div></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h6 class="fw-bold mb-0"><i class="fa-solid fa-gas-pump text-warning me-2"></i>MQ5 History</h6></div>
            <div class="card-body"><div class="chart-container"><canvas id="analyticsMQ5Chart"></canvas></div></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h6 class="fw-bold mb-0"><i class="fa-solid fa-smog text-orange me-2"></i>Dust History</h6></div>
            <div class="card-body"><div class="chart-container"><canvas id="analyticsDustChart"></canvas></div></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h6 class="fw-bold mb-0"><i class="fa-solid fa-lungs text-success me-2"></i>AQI Trend</h6></div>
            <div class="card-body"><div class="chart-container"><canvas id="analyticsAQIChart"></canvas></div></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const chartData = @json($chartData);

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#94a3b8';

    function makeLine(ctx, labels, data, color, label, yLabel = '') {
        return new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label,
                    data,
                    borderColor: color,
                    backgroundColor: color + '15',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1e293b', titleColor: '#f1f5f9', bodyColor: '#cbd5e1', padding: 12, cornerRadius: 10 } },
                scales: { x: { grid: { display: false }, ticks: { maxTicksLimit: 8 } }, y: { grid: { color: '#f1f5f9' }, title: { display: !!yLabel, text: yLabel } } }
            }
        });
    }

    // All sensors chart
    new Chart(document.getElementById('allSensorsChart'), {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                { label: 'Temp (°C)', data: chartData.temperature, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,0.05)', fill: false, tension: 0.4, borderWidth: 2, pointRadius: 0 },
                { label: 'Humidity (%)', data: chartData.humidity, borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.05)', fill: false, tension: 0.4, borderWidth: 2, pointRadius: 0 },
                { label: 'AQI', data: chartData.aqi, borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.05)', fill: false, tension: 0.4, borderWidth: 2, pointRadius: 0 },
                { label: 'Dust', data: chartData.dust, borderColor: '#f97316', backgroundColor: 'rgba(249,115,22,0.05)', fill: false, tension: 0.4, borderWidth: 2, pointRadius: 0 },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: { legend: { position: 'top', labels: { usePointStyle: true, padding: 16 } }, tooltip: { backgroundColor: '#1e293b', titleColor: '#f1f5f9', bodyColor: '#cbd5e1', padding: 12, cornerRadius: 10 } },
            scales: { x: { grid: { display: false }, ticks: { maxTicksLimit: 10 } }, y: { grid: { color: '#f1f5f9' } } }
        }
    });

    // AQI Pie Chart
    const aqiCounts = { Good: 0, Moderate: 0, Unhealthy: 0, Hazardous: 0 };
    chartData.aqi.forEach(v => {
        if (v <= 50) aqiCounts.Good++;
        else if (v <= 100) aqiCounts.Moderate++;
        else if (v <= 150) aqiCounts.Unhealthy++;
        else aqiCounts.Hazardous++;
    });

    new Chart(document.getElementById('aqiPieChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(aqiCounts),
            datasets: [{
                data: Object.values(aqiCounts),
                backgroundColor: ['#22c55e', '#eab308', '#f97316', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 12 } } }
        }
    });

    // Individual charts
    makeLine(document.getElementById('analyticsTempChart'), chartData.labels, chartData.temperature, '#ef4444', 'Temperature', '°C');
    makeLine(document.getElementById('analyticsHumidChart'), chartData.labels, chartData.humidity, '#3b82f6', 'Humidity', '%');
    makeLine(document.getElementById('analyticsMQ2Chart'), chartData.labels, chartData.mq2, '#a855f7', 'MQ2', 'ppm');
    makeLine(document.getElementById('analyticsMQ5Chart'), chartData.labels, chartData.mq5, '#eab308', 'MQ5', 'ppm');
    makeLine(document.getElementById('analyticsDustChart'), chartData.labels, chartData.dust, '#f97316', 'Dust', 'µg/m³');
    makeLine(document.getElementById('analyticsAQIChart'), chartData.labels, chartData.aqi, '#22c55e', 'AQI', 'AQI');
</script>
@endpush
