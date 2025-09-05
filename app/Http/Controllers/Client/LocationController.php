<?php

namespace App\Http\Controllers\Client;

use App\Models\InecLga;
use App\Models\InecState;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{


    public function getNewWards(Request $request, InecLga $lga){
        $response = ['success' => true, 'wards' => []];

        $wards = $lga->wards()->orderBy('name', 'ASC')->get();

        $response['wards'] = $wards;

        return $response;
    }

    public function getNewLgasAndWards(Request $request, InecState $state){
        $response = ['success' => true, 'cities' => [], 'wards' => []];

        $lgas = $state->lgas;
        $response['cities'] = $state->lgas()->orderBy('name', 'ASC')->get();
        // $response['wards'] = $lgas->first()->wards()->orderBy('name', 'ASC')->get();

        return $response;
    }
}
