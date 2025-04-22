<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlantController extends Controller
{
    public function tampilkanDataPlant()
    {
        $plants = Plant::all();
        return view('dashboard.plant');
    }
}
