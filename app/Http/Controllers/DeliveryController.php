<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function tampilkanDataDelivery()
    {
        return view('dashboard.delivery');
    }
}
