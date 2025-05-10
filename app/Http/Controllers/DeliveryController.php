<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Models\OrderDeliverers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function tampilkanDataDelivery()
    {
        $user = Auth::user();
        if($user->role == 'admin')
        {
            $orders = Order::whereHas('latestStatus.status_category', function ($query) {
                $query->whereIn('status', [
                    'Proses Pengantaran',
                    'Proses Penggantian Tanaman',
                    'Proses Pengambilan Kembali',
                ]);
            })->whereHas('deliverer', function ($query) {
                $query->whereNull('delivery_photo');
            })->with(['customer', 'latestStatus.status_category'])->get();
        } else {
            $orders = Order::whereHas('latestStatus.status_category', function ($query) {
                $query->whereIn('status', [
                    'Proses Pengantaran',
                    'Proses Penggantian Tanaman',
                    'Proses Pengambilan Kembali',
                ]);
            })->whereHas('deliverer', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->whereNull('delivery_photo');
            })->with(['customer', 'latestStatus.status_category', 'deliverer'])->get();
        }
        return view('dashboard.delivery.index', compact('orders', 'user'));
    }

    public function tampilkanDetailDelivery($id)
    {
        $user = Auth::user();
        $order = Order::where('id', $id)
            ->with([
            'customer', // Relasi ke customer
            'orderItems', // Relasi ke item order
            'orderItems.plant', // Relasi ke item order dan tanaman
            'status.status_category', // Relasi ke status order dan kategori status
            'deliverer', // Relasi ke pengantar
            'deliverer.user', // Relasi ke user pengantar
            ])
            ->firstOrFail();

        return view('dashboard.delivery.detail', compact('order', 'user'));
    }


    public function konfirmasiDelivery(Request $request, $id)
    {

        $request->validate([
            'delivery_photo' => 'required|image|mimes:jpg,jpeg,png',
        ]);
    
        $user = Auth::user();
        $order = Order::findOrFail($id);
    
        try {
            DB::beginTransaction();
    
            // ambil data order_deliverer
            $deliverer = OrderDeliverers::where('order_id', $id)
                ->where('user_id', $user->id)
                ->latest('created_at')
                ->first();

            // Simpan foto
            $photoPath = $request->file('delivery_photo')->store('delivery_photos', 'public');
            $deliverer->delivery_photo = $photoPath;
            $deliverer->update();
    
            // Update status berdasarkan status terakhir
            $latestStatus = $order->latestStatus; 
            $status = $latestStatus->status_category->status ?? null;
    
            if ($status === 'Proses Pengantaran' || $status === 'Proses Penggantian Tanaman') {
                OrderStatus::create([
                    'order_id' => $order->id,
                    'status_id' => 2, // Order Dalam Masa Sewa
                ]);
            } elseif ($status === 'Proses Pengambilan Kembali') {
                OrderStatus::create([
                    'order_id' => $order->id,
                    'status_id' => 5, // Order Selesai
                ]);
            }
    
            DB::commit();
    
            return redirect()->route('dashboard.kelola.delivery')->with('success', 'Delivery confirmed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengkonfirmasi delivery: ' . $e->getMessage());
        }
    }
}
