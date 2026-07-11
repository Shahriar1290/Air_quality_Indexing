<?php

namespace App\Http\Controllers;

use App\Services\SensorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        private SensorService $sensorService
    ) {}

    /**
     * Reports page with summary statistics
     */
    public function index(Request $request): View
    {
        $filter = $request->get('filter', 'week');
        $from = $request->get('from');
        $to = $request->get('to');

        $readings = $this->sensorService->getHistory($filter, $from, $to);

        // Compute summary statistics
        $count = $readings->count();
        $stats = [];
        if ($count > 0) {
            $stats = [
                'count' => $count,
                'avg_temperature' => round($readings->avg('temperature'), 1),
                'min_temperature' => round($readings->min('temperature'), 1),
                'max_temperature' => round($readings->max('temperature'), 1),
                'avg_humidity' => round($readings->avg('humidity'), 1),
                'min_humidity' => round($readings->min('humidity'), 1),
                'max_humidity' => round($readings->max('humidity'), 1),
                'avg_mq2' => round($readings->avg('mq2')),
                'max_mq2' => $readings->max('mq2'),
                'avg_mq5' => round($readings->avg('mq5')),
                'max_mq5' => $readings->max('mq5'),
                'avg_dust' => round($readings->avg('dust')),
                'max_dust' => $readings->max('dust'),
                'avg_aqi' => round($readings->avg('aqi') ?? $readings->avg('estimated_aqi')),
                'max_aqi' => $readings->max('estimated_aqi'),
                'danger_count' => $readings->where('gas_status', 'DANGER')->count(),
                'safe_count' => $readings->where('gas_status', 'SAFE')->count(),
                'good_air' => $readings->where('air_status', 'Good')->count(),
                'moderate_air' => $readings->where('air_status', 'Moderate')->count(),
                'unhealthy_air' => $readings->where('air_status', 'Unhealthy')->count(),
                'hazardous_air' => $readings->where('air_status', 'Hazardous')->count(),
            ];
        }

        return view('pages.reports', [
            'stats' => $stats,
            'hasData' => $count > 0,
            'currentFilter' => $filter,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
