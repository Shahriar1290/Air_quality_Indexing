<p align="center">
    <h1 align="center">AirWatch - Smart Air Quality Monitoring System</h1>
</p>

<p align="center">
    A modern real-time air quality monitoring dashboard built with Laravel 12, designed for ESP32-based embedded systems projects.
</p>

---

## About

**AirWatch** is a full-stack web application that receives environmental sensor data from an ESP32 microcontroller and displays it on a modern, responsive dashboard. The ESP32 measures temperature, humidity, gas levels (MQ2/MQ5), dust concentration, and estimated AQI — all visualized in real-time with auto-refreshing charts and alerts.

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12, PHP 8.2+, Eloquent ORM |
| Database | MySQL / SQLite |
| Frontend | Blade Templates, Bootstrap 5 |
| Charts | Chart.js 4 |
| Icons | Font Awesome 6 |
| API | REST (JSON) |
| Microcontroller | ESP32 + Sensors |

## Features

### Dashboard
- **6 Summary Cards** — Temperature, Humidity, MQ2, MQ5, Dust, AQI with color-coded status
- **Alert System** — Danger gas, poor air quality, high dust, extreme temperature, low humidity
- **Live Status Panel** — Real-time sensor values with last updated timestamp
- **6 Chart.js Graphs** — Temperature, Humidity, AQI, MQ2, MQ5, Dust trends
- **Auto-Refresh** — AJAX polling every 5 seconds
- **Toast Notifications** — Pop-up alerts when danger gas is detected

### Sensor History
- Paginated data table with all readings
- **Date Filters** — Today, Yesterday, Last 7 Days, Last Month, Custom Range
- Color-coded values (green/yellow/red)
- Gas status and Air quality badges

### Analytics
- Multi-sensor overlay chart (50 readings)
- **AQI Distribution** — Doughnut chart (Good / Moderate / Unhealthy / Hazardous)
- Individual sensor trend charts

### Reports
- Summary statistics (avg, min, max) for all sensors
- Air quality distribution with progress bars
- Gas danger event counts

### Settings
- System information (env, debug, Laravel version)
- **ESP32 API Endpoint** documentation with copy-to-clipboard
- JSON payload example and field validation ranges
- Theme toggle (Light/Dark mode)
- Auto-refresh interval control
- Toast notification toggle

### UI/UX
- **Responsive** — Desktop, Tablet, Mobile
- **Dark Mode** — Toggle with localStorage persistence
- **Animated Cards** — Hover effects and smooth transitions
- **Loading Skeletons** — Animated placeholders
- **Modern Typography** — Inter font family
- **Soft Shadows** — Rounded corners, clean card design

## API Endpoint

### POST `/api/sensor-data`

The ESP32 sends sensor readings as JSON:

```json
{
    "temperature": 30.4,
    "humidity": 68,
    "mq2": 420,
    "mq5": 890,
    "dust": 250,
    "estimated_aqi": 72,
    "gas_status": "SAFE",
    "air_status": "Good"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Sensor data stored successfully.",
    "data": {
        "id": 1,
        "created_at": "2026-07-16T12:00:00.000000Z"
    }
}
```

### Validation Rules

| Field | Type | Range |
|-------|------|-------|
| temperature | decimal | -40 to 80 |
| humidity | decimal | 0 to 100 |
| mq2 | integer | 0 to 4095 |
| mq5 | integer | 0 to 4095 |
| dust | integer | 0 to 1000 |
| estimated_aqi | integer | 0 to 500 |
| gas_status | string | SAFE / DANGER |
| air_status | string | Good / Moderate / Unhealthy / Hazardous |

## ESP32 Integration

### Requirements
- ESP32 development board
- MQ2 Gas Sensor
- MQ5 Gas Sensor
- Dust Sensor (MAX30105 / SEN-16474)
- DHT22 or similar (Temperature + Humidity)
- WiFi connection

### Arduino Libraries
- `ArduinoJson` (by Benoit Blanchon)
- `WiFi` (built-in)
- `HTTPClient` (built-in)

### Quick Start
1. Clone this repo
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure database
4. Run `php artisan migrate --seed`
5. Start the server: `php artisan serve --host=0.0.0.0`
6. Upload the ESP32 code with your WiFi credentials and server IP
7. Open the dashboard at `http://localhost:8000`

## Installation

```bash
# Clone the repository
git clone https://github.com/your-username/air_quality.git

# Install dependencies
composer install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate --seed

# Start server
php artisan serve --host=0.0.0.0
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/SensorDataController.php
│   │   ├── DashboardController.php
│   │   ├── ReportController.php
│   │   └── SettingsController.php
│   └── Requests/
│       └── StoreSensorDataRequest.php
├── Models/
│   └── SensorReading.php
└── Services/
    └── SensorService.php

database/
├── migrations/
│   └── 2026_07_16_000001_create_sensor_readings_table.php
└── seeders/
    └── SensorReadingSeeder.php

resources/views/
├── layouts/
│   └── app.blade.php
└── pages/
    ├── dashboard.blade.php
    ├── history.blade.php
    ├── analytics.blade.php
    ├── reports.blade.php
    └── settings.blade.php

routes/
├── web.php
└── api.php
```

## Dashboard Pages

| Page | URL | Description |
|------|-----|-------------|
| Dashboard | `/` | Live overview with cards, alerts, charts |
| History | `/history` | Paginated sensor readings with filters |
| Analytics | `/analytics` | Detailed trend charts and AQI distribution |
| Reports | `/reports` | Summary statistics and air quality breakdown |
| Settings | `/settings` | System info, API docs, preferences |

## License

This project is open-sourced software licensed under the MIT license.
