<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Plant;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function tampilkanDashboard()
    {
        $user = Auth::user();

        $totalOrders = Order::count();
        $activeOrders = Order::whereHas('latestStatus.status_category', function ($query) {
            $query->where('status', 'Dalam Masa Sewa');
        })->count();
        $totalCustomers = Customer::count();
        $totalPlants = Plant::count();

        // Buat array tanggal 30 hari terakhir (Y-m-d)
        $dates = collect(range(0, 29))->map(function ($i) {
            return Carbon::today()->subDays($i)->format('Y-m-d');
        })->reverse();

        // Ambil jumlah order per tanggal
        $orderData = Order::whereDate('created_at', '>=', Carbon::today()->subDays(29))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        // Siapkan data chart
        $orderChartLabels = $dates->map(fn($date) => Carbon::parse($date)->translatedFormat('d M'))->values();
        $orderChartData = $dates->map(fn($date) => $orderData[$date] ?? 0)->values();

        $lowStockPlants = Plant::where('stock', '<', 10)->orderBy('stock', 'asc')->get();

        $activeOrdersList = Order::whereHas('latestStatus.status_category', function ($query) {
            $query->where('status', 'Dalam Masa Sewa');
        })->with(['customer', 'orderItems.plant', 'latestStatus.status_category'])->paginate(3);

        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_price');

        return view('dashboard.index', compact(
            'user',
            'totalOrders',
            'activeOrders',
            'totalCustomers',
            'totalPlants',
            'orderChartLabels',
            'orderChartData',
            'lowStockPlants',
            'activeOrdersList',
            'totalRevenue'
        ));
    }
}
