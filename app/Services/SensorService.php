<?php

namespace App\Services;

use App\Models\SensorReading;
use Illuminate\Support\Collection;

class SensorService
{
    /**
     * Get latest sensor reading
     */
    public function getLatest(): ?SensorReading
    {
        return SensorReading::getLatest();
    }

    /**
     * Get chart data for the last N readings
     */
    public function getChartData(int $limit = 24): array
    {
        $readings = SensorReading::getLatestReadings($limit);

        return [
            'labels' => $readings->map(fn($r) => $r->created_at->format('H:i'))->toArray(),
            'temperature' => $readings->pluck('temperature')->toArray(),
            'humidity' => $readings->pluck('humidity')->toArray(),
            'mq2' => $readings->pluck('mq2')->toArray(),
            'mq5' => $readings->pluck('mq5')->toArray(),
            'dust' => $readings->pluck('dust')->toArray(),
            'aqi' => $readings->pluck('estimated_aqi')->toArray(),
        ];
    }

    /**
     * Get sensor history with filters
     */
    public function getHistory(string $filter = 'today', ?string $from = null, ?string $to = null): Collection
    {
        $query = SensorReading::query();

        return match ($filter) {
            'today' => $query->today()->latest()->get(),
            'yesterday' => $query->yesterday()->latest()->get(),
            'week' => $query->lastDays(7)->latest()->get(),
            'month' => $query->lastMonth()->latest()->get(),
            'custom' => $query->betweenDates($from, $to)->latest()->get(),
            default => $query->latest()->get(),
        };
    }

    /**
     * Get paginated sensor history
     */
    public function getHistoryPaginated(string $filter = 'today', ?string $from = null, ?string $to = null, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = SensorReading::query();

        match ($filter) {
            'today' => $query->today(),
            'yesterday' => $query->yesterday(),
            'week' => $query->lastDays(7),
            'month' => $query->lastMonth(),
            'custom' => $query->betweenDates($from, $to),
            default => null,
        };

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get active alerts based on latest reading
     */
    public function getAlerts(): array
    {
        return SensorReading::getActiveAlerts();
    }

    /**
     * Get dashboard summary data
     */
    public function getDashboardSummary(): array
    {
        $latest = $this->getLatest();
        $alerts = $this->getAlerts();

        return [
            'latest' => $latest,
            'alerts' => $alerts,
            'has_alerts' => count($alerts) > 0,
            'alert_count' => count($alerts),
        ];
    }
}
