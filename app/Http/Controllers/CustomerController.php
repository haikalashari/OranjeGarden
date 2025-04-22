<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function tampilkanDataCustomer()
    {
        $customers = Customer::all();
        return view('dashboard.customer');
    }
}
