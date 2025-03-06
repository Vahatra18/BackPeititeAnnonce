<?php

namespace App\Http\Controllers\Api;

use App\Services\LocationService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    /**
     * Display a listing of all available cities (districts).
     */
    public function index()
    {
        $cities = LocationService::getAllCities();
        return response()->json(['data' => $cities], 200);
    }
}
