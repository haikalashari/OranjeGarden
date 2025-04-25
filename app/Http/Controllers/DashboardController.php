<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function tampilkanDashboard()
    {
        $user = Auth::user();
        return view('dashboard.index', compact('user'));
    }
}
