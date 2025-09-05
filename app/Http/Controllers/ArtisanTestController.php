<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ArtisanTestController extends Controller
{


    public function index(){
        Artisan::call('db:seed PostSeeder');
    }
}
