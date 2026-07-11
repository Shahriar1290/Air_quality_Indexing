<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('pages.settings', [
            'appName' => config('app.name', 'AirWatch'),
            'appUrl' => config('app.url'),
            'appEnv' => config('app.env'),
            'appDebug' => config('app.debug'),
        ]);
    }
}
