@extends('layouts.app')

@section('title', 'Settings')
@section('subtitle', 'System configuration and preferences')

@section('content')

<div class="row g-4">

    {{-- System Info --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-circle-info text-primary me-2"></i>System Information</h6>
            </div>
            <div class="card-body">
                <div class="live-panel">
                    <div class="live-item">
                        <span class="live-label"><i class="fa-solid fa-tag"></i> App Name</span>
                        <span class="live-value">{{ $appName }}</span>
                    </div>
                    <div class="live-item">
                        <span class="live-label"><i class="fa-solid fa-globe"></i> App URL</span>
                        <span class="live-value" style="font-size:12px;">{{ $appUrl }}</span>
                    </div>
                    <div class="live-item">
                        <span class="live-label"><i class="fa-solid fa-server"></i> Environment</span>
                        <span class="live-value">
                            <span class="status-badge {{ $appEnv === 'production' ? 'danger' : 'safe' }}">{{ ucfirst($appEnv) }}</span>
                        </span>
                    </div>
                    <div class="live-item">
                        <span class="live-label"><i class="fa-solid fa-bug"></i> Debug Mode</span>
                        <span class="live-value">
                            <span class="status-badge {{ $appDebug ? 'danger' : 'safe' }}">{{ $appDebug ? 'Enabled' : 'Disabled' }}</span>
                        </span>
                    </div>
                    <div class="live-item" style="border-bottom:none;">
                        <span class="live-label"><i class="fa-solid fa-code"></i> Laravel Version</span>
                        <span class="live-value">12.x</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ESP32 API Info --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-microchip text-success me-2"></i>ESP32 API Endpoint</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label style="font-size:12px;font-weight:600;color:#64748b;">POST Endpoint</label>
                    <div class="input-group" style="border-radius:10px;overflow:hidden;">
                        <input type="text" class="form-control form-control-sm" value="{{ $appUrl }}/api/sensor-data" readonly style="background:#f8fafc;border:1px solid #e2e8f0;font-size:12px;font-family:monospace;">
                        <button class="btn btn-sm" style="background:var(--color-primary);color:#fff;" onclick="navigator.clipboard.writeText('{{ $appUrl }}/api/sensor-data'); this.innerHTML='<i class=\'fa-solid fa-check\'></i>'; setTimeout(()=>this.innerHTML='<i class=\'fa-solid fa-copy\'></i>', 1500);">
                            <i class="fa-solid fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label style="font-size:12px;font-weight:600;color:#64748b;">Expected JSON Payload</label>
                    <pre style="background:#1e293b;color:#e2e8f0;padding:14px;border-radius:10px;font-size:11px;overflow-x:auto;margin:0;">{
  "temperature": 30.4,
  "humidity": 68,
  "mq2": 420,
  "mq5": 890,
  "dust": 250,
  "estimated_aqi": 72,
  "gas_status": "SAFE",
  "air_status": "Good"
}</pre>
                </div>

                <div class="mb-0">
                    <label style="font-size:12px;font-weight:600;color:#64748b;">Field Ranges</label>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0" style="font-size:11px;">
                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th>Type</th>
                                    <th>Range</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>temperature</td><td>decimal</td><td>-40 to 80</td></tr>
                                <tr><td>humidity</td><td>decimal</td><td>0 to 100</td></tr>
                                <tr><td>mq2</td><td>integer</td><td>0 to 4095</td></tr>
                                <tr><td>mq5</td><td>integer</td><td>0 to 4095</td></tr>
                                <tr><td>dust</td><td>integer</td><td>0 to 1000</td></tr>
                                <tr><td>estimated_aqi</td><td>integer</td><td>0 to 500</td></tr>
                                <tr><td>gas_status</td><td>string</td><td>SAFE / DANGER</td></tr>
                                <tr><td>air_status</td><td>string</td><td>Good / Moderate / Unhealthy / Hazardous</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Preferences --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-sliders text-warning me-2"></i>Dashboard Preferences</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label style="font-size:12px;font-weight:600;color:#64748b;">Auto-Refresh Interval</label>
                    <select class="form-select form-select-sm" style="border-radius:10px;" id="refreshInterval" onchange="updateRefreshInterval(this.value)">
                        <option value="3000">3 seconds</option>
                        <option value="5000" selected>5 seconds (default)</option>
                        <option value="10000">10 seconds</option>
                        <option value="30000">30 seconds</option>
                        <option value="0">Disabled</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label style="font-size:12px;font-weight:600;color:#64748b;">Theme</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm flex-fill" style="border:1px solid #e2e8f0;border-radius:10px;" onclick="document.documentElement.setAttribute('data-bs-theme','light'); localStorage.setItem('theme','light');">
                            <i class="fa-solid fa-sun me-1"></i> Light
                        </button>
                        <button class="btn btn-sm flex-fill" style="border:1px solid #e2e8f0;border-radius:10px;" onclick="document.documentElement.setAttribute('data-bs-theme','dark'); localStorage.setItem('theme','dark');">
                            <i class="fa-solid fa-moon me-1"></i> Dark
                        </button>
                    </div>
                </div>
                <div class="mb-0">
                    <label style="font-size:12px;font-weight:600;color:#64748b;">Toast Notifications</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="toastToggle" checked onchange="localStorage.setItem('toastEnabled', this.checked)">
                        <label class="form-check-label" for="toastToggle" style="font-size:12px;color:#64748b;">Show toast alerts on danger events</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Danger Zone --}}
    <div class="col-lg-6">
        <div class="card" style="border:1px solid #fecaca;">
            <div class="card-header" style="background:rgba(239,68,68,0.04);">
                <h6 class="fw-bold mb-0 text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Danger Zone</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#1e293b;">Clear All Readings</div>
                        <div style="font-size:11px;color:#94a3b8;">Delete all sensor data from the database.</div>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" style="border-radius:10px;" onclick="if(confirm('Are you sure? This cannot be undone.')) { /* clear data action */ }">
                        <i class="fa-solid fa-trash me-1"></i> Clear
                    </button>
                </div>
                <hr style="border-color:#fecaca;">
                <div class="d-flex align-items-center justify-content-between mb-0">
                    <div>
                        <div style="font-size:13px;font-weight:600;color:#1e293b;">Reset to Defaults</div>
                        <div style="font-size:11px;color:#94a3b8;">Reset all preferences to default values.</div>
                    </div>
                    <button class="btn btn-sm btn-outline-warning" style="border-radius:10px;" onclick="localStorage.clear(); location.reload();">
                        <i class="fa-solid fa-rotate-left me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
    function updateRefreshInterval(ms) {
        localStorage.setItem('refreshInterval', ms);
    }
    document.addEventListener('DOMContentLoaded', function() {
        const saved = localStorage.getItem('refreshInterval');
        if (saved) document.getElementById('refreshInterval').value = saved;
        const toastEnabled = localStorage.getItem('toastEnabled');
        if (toastEnabled !== null) document.getElementById('toastToggle').checked = toastEnabled === 'true';
    });
</script>
@endpush
