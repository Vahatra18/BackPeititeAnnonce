<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class LocationService
{
    public static function getAllCities()
    {
        $json = File::get(database_path('data/liste_ville_par_region.json'));
        $locations = json_decode($json, true);

        $cities = [];
        foreach ($locations as $region => $districts) {
            foreach ($districts as $district) {
                $cities[] = $district;
            }
        }

        return array_unique($cities); // Supprime les doublons si nécessaire
    }
}
