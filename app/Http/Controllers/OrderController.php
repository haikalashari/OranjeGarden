<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function tampilkanDataOrder()
    {
        $order = Order::all();
        return view('dashboard.order');
    }   
}
