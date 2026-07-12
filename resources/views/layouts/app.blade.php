<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - AirWatch</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed: 72px;
            --navbar-height: 64px;
            --color-good: #22c55e;
            --color-moderate: #eab308;
            --color-unhealthy: #f97316;
            --color-hazardous: #ef4444;
            --color-primary: #6366f1;
            --color-primary-light: #818cf8;
            --sidebar-bg: #1e1e2d;
            --sidebar-active: rgba(99, 102, 241, 0.15);
            --sidebar-hover: rgba(255, 255, 255, 0.06);
            --sidebar-text: rgba(255, 255, 255, 0.65);
            --sidebar-text-active: #fff;
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            background: #f5f6fa;
            min-height: 100vh;
        }

        [data-bs-theme="dark"] {
            --sidebar-bg: #111827;
            --sidebar-active: rgba(99, 102, 241, 0.2);
            --sidebar-hover: rgba(255, 255, 255, 0.05);
        }

        [data-bs-theme="dark"] body {
            background: #0f172a;
        }

        /* ============ SIDEBAR ============ */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            z-index: 1040;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .sidebar-brand .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
            flex-shrink: 0;
        }

        .sidebar-brand .brand-text {
            color: #fff;
            font-weight: 700;
            font-size: 17px;
            letter-spacing: -0.3px;
        }

        .sidebar-brand .brand-sub {
            color: var(--sidebar-text);
            font-size: 11px;
            font-weight: 400;
        }

        .sidebar-nav {
            padding: 16px 12px;
        }

        .sidebar-nav .nav-section {
            color: rgba(255, 255, 255, 0.3);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            padding: 8px 12px 8px;
            margin-top: 8px;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            color: var(--sidebar-text);
            font-size: 13.5px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            margin-bottom: 2px;
        }

        .sidebar-nav .nav-link:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .sidebar-nav .nav-link.active {
            background: var(--sidebar-active);
            color: var(--sidebar-text-active);
        }

        .sidebar-nav .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 15px;
        }

        /* ============ MAIN CONTENT ============ */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ============ TOP NAVBAR ============ */
        .top-navbar {
            height: var(--navbar-height);
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        [data-bs-theme="dark"] .top-navbar {
            background: #1e293b;
            border-color: #334155;
        }

        .top-navbar .page-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        [data-bs-theme="dark"] .top-navbar .page-title {
            color: #f1f5f9;
        }

        .top-navbar .page-subtitle {
            font-size: 12px;
            color: #94a3b8;
            margin: 0;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-actions .btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #64748b;
            transition: all 0.2s;
        }

        [data-bs-theme="dark"] .navbar-actions .btn {
            background: #334155;
            border-color: #475569;
            color: #94a3b8;
        }

        .navbar-actions .btn:hover {
            background: #f8fafc;
            color: var(--color-primary);
            border-color: var(--color-primary);
        }

        .live-dot {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            display: inline-block;
            animation: pulse-live 2s infinite;
        }

        @keyframes pulse-live {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }

        /* ============ PAGE CONTENT ============ */
        .page-content {
            padding: 28px;
        }

        /* ============ CARDS ============ */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: all 0.2s;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 16px 20px;
        }

        [data-bs-theme="dark"] .card {
            background: #1e293b;
            border-color: #334155;
        }

        [data-bs-theme="dark"] .card-header {
            border-color: #334155;
        }

        /* ============ SENSOR CARDS ============ */
        .sensor-card {
            border-radius: 16px;
            padding: 20px;
            background: #fff;
            border: 1px solid #f1f5f9;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }

        .sensor-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        [data-bs-theme="dark"] .sensor-card {
            background: #1e293b;
            border-color: #334155;
        }

        .sensor-card .sensor-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 14px;
        }

        .sensor-card .sensor-label {
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .sensor-card .sensor-value {
            font-size: 28px;
            font-weight: 800;
            color: #1e293b;
            line-height: 1.2;
        }

        [data-bs-theme="dark"] .sensor-card .sensor-value {
            color: #f1f5f9;
        }

        .sensor-card .sensor-unit {
            font-size: 14px;
            font-weight: 500;
            color: #94a3b8;
        }

        .sensor-card .sensor-meta {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 4px;
        }

        /* ============ ALERTS ============ */
        .alert-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 10px;
            border-left: 4px solid;
            transition: all 0.2s;
        }

        .alert-item:hover { transform: translateX(4px); }

        .alert-item.alert-danger {
            background: rgba(239, 68, 68, 0.06);
            border-color: #ef4444;
        }

        .alert-item.alert-warning {
            background: rgba(234, 179, 8, 0.06);
            border-color: #eab308;
        }

        .alert-item.alert-orange {
            background: rgba(249, 115, 22, 0.06);
            border-color: #f97316;
        }

        .alert-item.alert-info {
            background: rgba(99, 102, 241, 0.06);
            border-color: #6366f1;
        }

        .alert-item .alert-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 14px;
        }

        .alert-item .alert-title {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .alert-item .alert-msg {
            font-size: 12px;
            color: #64748b;
            margin: 0;
        }

        .alert-item .alert-time {
            font-size: 11px;
            color: #94a3b8;
            white-space: nowrap;
        }

        /* ============ LIVE PANEL ============ */
        .live-panel .live-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .live-panel .live-item:last-child { border-bottom: none; }

        .live-panel .live-label {
            font-size: 13px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .live-panel .live-value {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
        }

        [data-bs-theme="dark"] .live-panel .live-value {
            color: #f1f5f9;
        }

        /* ============ BADGES ============ */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.good { background: rgba(34, 197, 94, 0.1); color: #16a34a; }
        .status-badge.moderate { background: rgba(234, 179, 8, 0.1); color: #ca8a04; }
        .status-badge.unhealthy { background: rgba(249, 115, 22, 0.1); color: #ea580c; }
        .status-badge.hazardous { background: rgba(239, 68, 68, 0.1); color: #dc2626; }
        .status-badge.safe { background: rgba(34, 197, 94, 0.1); color: #16a34a; }
        .status-badge.danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; }

        /* ============ TABLE ============ */
        .table {
            border-color: #f1f5f9;
        }

        .table thead th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
        }

        [data-bs-theme="dark"] .table thead th {
            background: #1e293b;
            border-color: #334155;
            color: #94a3b8;
        }

        .table tbody td {
            padding: 12px 16px;
            font-size: 13px;
            color: #475569;
            vertical-align: middle;
        }

        [data-bs-theme="dark"] .table tbody td {
            color: #cbd5e1;
        }

        /* ============ LOADING SKELETON ============ */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
            border-radius: 8px;
        }

        [data-bs-theme="dark"] .skeleton {
            background: linear-gradient(90deg, #334155 25%, #475569 50%, #334155 75%);
            background-size: 200% 100%;
        }

        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* ============ TOAST ============ */
        .toast-container {
            position: fixed;
            top: 80px;
            right: 24px;
            z-index: 9999;
        }

        .toast {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
            min-width: 320px;
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1035;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }

        /* ============ FILTER PILLS ============ */
        .filter-pills .btn {
            border-radius: 100px;
            padding: 8px 18px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            color: #64748b;
            background: #fff;
        }

        [data-bs-theme="dark"] .filter-pills .btn {
            background: #1e293b;
            border-color: #334155;
            color: #94a3b8;
        }

        .filter-pills .btn.active,
        .filter-pills .btn:hover {
            background: var(--color-primary);
            color: #fff;
            border-color: var(--color-primary);
        }

        /* ============ CHART CONTAINER ============ */
        .chart-container {
            position: relative;
            height: 280px;
        }

        /* ============ SCROLLBAR ============ */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i class="fa-solid fa-wind"></i>
            </div>
            <div>
                <div class="brand-text">AirWatch</div>
                <div class="brand-sub">Air Quality Monitor</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">Main</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-grid-2"></i>
                Dashboard
            </a>

            <div class="nav-section">Monitoring</div>
            <a href="#" class="nav-link" onclick="scrollToSection('live-status'); return false;">
                <i class="fa-solid fa-signal"></i>
                Live Monitoring
            </a>
            <a href="{{ route('history') }}" class="nav-link {{ request()->routeIs('history') ? 'active' : '' }}">
                <i class="fa-solid fa-clock-rotate-left"></i>
                Sensor History
            </a>
            <a href="{{ route('analytics') }}" class="nav-link {{ request()->routeIs('analytics') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i>
                Analytics
            </a>

            <div class="nav-section">System</div>
            <a href="{{ route('reports') }}" class="nav-link {{ request()->routeIs('reports') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines"></i>
                Reports
            </a>
            <a href="{{ route('settings') }}" class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i>
                Settings
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div>
                <button class="btn d-lg-none me-2" onclick="toggleSidebar()" style="width:36px;height:36px;border:1px solid #e5e7eb;background:#fff;border-radius:10px;">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1 class="page-title d-inline">@yield('title', 'Dashboard')</h1>
                <p class="page-subtitle mt-0">@yield('subtitle', 'Real-time air quality overview')</p>
            </div>
            <div class="navbar-actions">
                <span class="d-flex align-items-center gap-2 text-success" style="font-size:12px;font-weight:600;">
                    <span class="live-dot"></span> LIVE
                </span>
                <button class="btn" onclick="toggleDarkMode()" title="Toggle Dark Mode">
                    <i class="fa-solid fa-moon" id="themeIcon"></i>
                </button>
                <button class="btn" onclick="refreshAll()" title="Refresh Data">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>
        </header>

        <!-- Page Content -->
        <div class="page-content">
            @yield('content')
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        // CSRF Token
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        // Dark Mode Toggle
        function toggleDarkMode() {
            const html = document.documentElement;
            const isDark = html.getAttribute('data-bs-theme') === 'dark';
            html.setAttribute('data-bs-theme', isDark ? 'light' : 'dark');
            document.getElementById('themeIcon').className = isDark ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
            localStorage.setItem('theme', isDark ? 'light' : 'dark');
        }

        // Load saved theme
        (function() {
            const saved = localStorage.getItem('theme');
            if (saved) {
                document.documentElement.setAttribute('data-bs-theme', saved);
                if (saved === 'dark') {
                    document.getElementById('themeIcon').className = 'fa-solid fa-sun';
                }
            }
        })();

        // Toast Notification
        function showToast(message, type = 'danger') {
            const container = document.getElementById('toastContainer');
            const toastId = 'toast-' + Date.now();
            const iconMap = {
                danger: 'fa-solid fa-triangle-exclamation',
                warning: 'fa-solid fa-exclamation-circle',
                info: 'fa-solid fa-info-circle',
                success: 'fa-solid fa-check-circle'
            };
            const bgMap = {
                danger: '#fef2f2',
                warning: '#fffbeb',
                info: '#eff6ff',
                success: '#f0fdf4'
            };

            container.insertAdjacentHTML('beforeend', `
                <div id="${toastId}" class="toast show" role="alert" style="background:${bgMap[type] || bgMap.danger}">
                    <div class="toast-body d-flex align-items-center gap-3">
                        <i class="${iconMap[type] || iconMap.danger}" style="color:${type === 'danger' ? '#ef4444' : type === 'warning' ? '#eab308' : '#6366f1'}; font-size:18px;"></i>
                        <div>
                            <div style="font-size:13px;font-weight:600;color:#1e293b;">${message}</div>
                        </div>
                        <button type="button" class="btn-close ms-auto" onclick="this.closest('.toast').remove()"></button>
                    </div>
                </div>
            `);

            setTimeout(() => {
                const toast = document.getElementById(toastId);
                if (toast) toast.remove();
            }, 6000);
        }

        // Refresh all data
        function refreshAll() {
            if (typeof fetchLiveData === 'function') fetchLiveData();
            if (typeof fetchChartData === 'function') fetchChartData();
        }

        // Scroll to section
        function scrollToSection(id) {
            const el = document.getElementById(id);
            if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Format time
        function formatTime(isoString) {
            const d = new Date(isoString);
            return d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        }

        // Update last updated time
        function updateLastUpdated() {
            const el = document.getElementById('lastUpdated');
            if (el) el.textContent = formatTime(new Date().toISOString());
        }
    </script>

    @stack('scripts')
</body>
</html>
