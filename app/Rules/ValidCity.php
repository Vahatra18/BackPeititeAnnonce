<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Services\LocationService;

class ValidCity implements Rule
{
    private $message = ''; // Déclare la propriété $message comme private
    public function passes($attribute, $value)
    {
        $cities = LocationService::getAllCities();
        if (!in_array($value, $cities)) {
            $this->message = 'La ville sélectionnée n’est pas valide.';
            return false;
        }
        return true;
    }

    public function message()
    {
        return $this->message;
    }
}
