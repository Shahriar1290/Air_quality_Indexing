<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'temperature',
        'humidity',
        'mq2',
        'mq5',
        'dust',
        'estimated_aqi',
        'gas_status',
        'air_status',
    ];

    protected $casts = [
        'temperature' => 'decimal:2',
        'humidity' => 'decimal:2',
        'mq2' => 'integer',
        'mq5' => 'integer',
        'dust' => 'integer',
        'estimated_aqi' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeYesterday(Builder $query): Builder
    {
        return $query->whereDate('created_at', yesterday());
    }

    public function scopeLastDays(Builder $query, int $days): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeLastMonth(Builder $query): Builder
    {
        return $query->where('created_at', '>=', now()->subMonth());
    }

    public function scopeBetweenDates(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    public function scopeDangerGas(Builder $query): Builder
    {
        return $query->where('gas_status', 'DANGER');
    }

    public function scopePoorAir(Builder $query): Builder
    {
        return $query->whereIn('air_status', ['Unhealthy', 'Hazardous']);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getTemperatureStatusAttribute(): string
    {
        if ($this->temperature >= 40) return 'danger';
        if ($this->temperature >= 35) return 'warning';
        if ($this->temperature >= 20) return 'success';
        return 'info';
    }

    public function getHumidityStatusAttribute(): string
    {
        if ($this->humidity >= 80) return 'warning';
        if ($this->humidity >= 30) return 'success';
        return 'danger';
    }

    public function getAqiColorAttribute(): string
    {
        if ($this->estimated_aqi <= 50) return 'success';
        if ($this->estimated_aqi <= 100) return 'warning';
        if ($this->estimated_aqi <= 150) return 'orange';
        return 'danger';
    }

    public function getAqiLabelAttribute(): string
    {
        if ($this->estimated_aqi <= 50) return 'Good';
        if ($this->estimated_aqi <= 100) return 'Moderate';
        if ($this->estimated_aqi <= 150) return 'Unhealthy';
        return 'Hazardous';
    }

    public function getGasStatusColorAttribute(): string
    {
        return $this->gas_status === 'DANGER' ? 'danger' : 'success';
    }

    public function getDustStatusAttribute(): string
    {
        if ($this->dust >= 300) return 'danger';
        if ($this->dust >= 150) return 'warning';
        return 'success';
    }

    /*
    |--------------------------------------------------------------------------
    | Static Helpers
    |--------------------------------------------------------------------------
    */

    public static function getLatest(): ?self
    {
        return static::latest()->first();
    }

    public static function getLatestReadings(int $limit = 24): \Illuminate\Database\Eloquent\Collection
    {
        return static::latest()->take($limit)->orderBy('created_at', 'asc')->get();
    }

    public static function getActiveAlerts(): array
    {
        $latest = static::getLatest();
        if (!$latest) return [];

        $alerts = [];

        if ($latest->gas_status === 'DANGER') {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'fa-solid fa-skull-crossbones',
                'title' => 'Danger Gas Detected',
                'message' => 'Hazardous gas levels detected. Immediate action required.',
                'time' => $latest->created_at->diffForHumans(),
            ];
        }

        if (in_array($latest->air_status, ['Unhealthy', 'Hazardous'])) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fa-solid fa-cloud',
                'title' => 'Poor Air Quality',
                'message' => "Air quality is {$latest->air_status}. Limit outdoor exposure.",
                'time' => $latest->created_at->diffForHumans(),
            ];
        }

        if ($latest->dust >= 300) {
            $alerts[] = [
                'type' => 'orange',
                'icon' => 'fa-solid fa-smog',
                'title' => 'Very High Dust',
                'message' => "Dust level at {$latest->dust} exceeds safe threshold.",
                'time' => $latest->created_at->diffForHumans(),
            ];
        }

        if ($latest->temperature >= 40) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'fa-solid fa-temperature-high',
                'title' => 'High Temperature',
                'message' => "Temperature at {$latest->temperature}°C is critically high.",
                'time' => $latest->created_at->diffForHumans(),
            ];
        }

        if ($latest->humidity < 30) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'fa-solid fa-droplet-slash',
                'title' => 'Low Humidity',
                'message' => "Humidity at {$latest->humidity}% is below comfortable range.",
                'time' => $latest->created_at->diffForHumans(),
            ];
        }

        return $alerts;
    }
}
