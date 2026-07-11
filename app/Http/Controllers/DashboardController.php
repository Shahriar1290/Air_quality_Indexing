<?php

namespace App\Http\Controllers;

use App\Models\SensorReading;
use App\Services\SensorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private SensorService $sensorService
    ) {}

    /**
     * Main dashboard view
     */
    public function index(): View
    {
        $summary = $this->sensorService->getDashboardSummary();
        $chartData = $this->sensorService->getChartData(24);

        return view('pages.dashboard', [
            'latest' => $summary['latest'],
            'alerts' => $summary['alerts'],
            'hasAlerts' => $summary['has_alerts'],
            'alertCount' => $summary['alert_count'],
            'chartData' => $chartData,
        ]);
    }

    /**
     * Sensor history page
     */
    public function history(Request $request): View
    {
        $filter = $request->get('filter', 'today');
        $from = $request->get('from');
        $to = $request->get('to');

        $readings = $this->sensorService->getHistoryPaginated($filter, $from, $to, 15);

        return view('pages.history', [
            'readings' => $readings,
            'currentFilter' => $filter,
            'from' => $from,
            'to' => $to,
        ]);
    }

    /**
     * Analytics page
     */
    public function analytics(): View
    {
        $chartData = $this->sensorService->getChartData(50);

        return view('pages.analytics', [
            'chartData' => $chartData,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX API Endpoints
    |--------------------------------------------------------------------------
    */

    /**
     * Get live sensor data (AJAX polling)
     */
    public function apiLive(): JsonResponse
    {
        $latest = $this->sensorService->getLatest();
        $alerts = $this->sensorService->getAlerts();

        return response()->json([
            'success' => true,
            'data' => $latest ? [
                'id' => $latest->id,
                'temperature' => $latest->temperature,
                'humidity' => $latest->humidity,
                'mq2' => $latest->mq2,
                'mq5' => $latest->mq5,
                'dust' => $latest->dust,
                'estimated_aqi' => $latest->estimated_aqi,
                'gas_status' => $latest->gas_status,
                'air_status' => $latest->air_status,
                'temperature_status' => $latest->temperature_status,
                'humidity_status' => $latest->humidity_status,
                'aqi_color' => $latest->aqi_color,
                'aqi_label' => $latest->aqi_label,
                'gas_status_color' => $latest->gas_status_color,
                'dust_status' => $latest->dust_status,
                'created_at' => $latest->created_at->toISOString(),
                'updated_at' => $latest->updated_at->toISOString(),
            ] : null,
            'alerts' => $alerts,
            'alert_count' => count($alerts),
        ]);
    }

    /**
     * Get chart data (AJAX polling)
     */
    public function apiChart(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 24);
        $chartData = $this->sensorService->getChartData((int) $limit);

        return response()->json([
            'success' => true,
            'data' => $chartData,
        ]);
    }

    /**
     * Get history data (AJAX)
     */
    public function apiHistory(Request $request): JsonResponse
    {
        $filter = $request->get('filter', 'today');
        $from = $request->get('from');
        $to = $request->get('to');
        $page = $request->get('page', 1);

        $readings = $this->sensorService->getHistoryPaginated($filter, $from, $to, 15);

        return response()->json([
            'success' => true,
            'data' => $readings->items(),
            'pagination' => [
                'current_page' => $readings->currentPage(),
                'last_page' => $readings->lastPage(),
                'per_page' => $readings->perPage(),
                'total' => $readings->total(),
            ],
        ]);
    }
}
