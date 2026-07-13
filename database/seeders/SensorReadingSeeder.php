<?php

namespace Database\Seeders;

use App\Models\SensorReading;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SensorReadingSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Generate 200 readings over the past 7 days
        for ($i = 0; $i < 200; $i++) {
            $minutesBack = $i * 50; // roughly every 50 minutes
            $createdAt = $now->copy()->subMinutes($minutesBack);

            $temperature = round(rand(200, 420) / 10, 2);
            $humidity = round(rand(250, 850) / 10, 2);
            $mq2 = rand(200, 600);
            $mq5 = rand(300, 900);
            $dust = rand(50, 400);
            $estimatedAqi = rand(20, 200);

            // Determine statuses based on values
            $gasStatus = ($mq2 > 500 || $mq5 > 800) ? 'DANGER' : 'SAFE';

            if ($estimatedAqi <= 50) {
                $airStatus = 'Good';
            } elseif ($estimatedAqi <= 100) {
                $airStatus = 'Moderate';
            } elseif ($estimatedAqi <= 150) {
                $airStatus = 'Unhealthy';
            } else {
                $airStatus = 'Hazardous';
            }

            SensorReading::create([
                'temperature' => $temperature,
                'humidity' => $humidity,
                'mq2' => $mq2,
                'mq5' => $mq5,
                'dust' => $dust,
                'estimated_aqi' => $estimatedAqi,
                'gas_status' => $gasStatus,
                'air_status' => $airStatus,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
    }
}
