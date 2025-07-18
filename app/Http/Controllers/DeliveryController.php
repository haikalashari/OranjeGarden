<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Plant;
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
        if($user->role == 'admin' || $user->role == 'super admin')
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
        ], [
            'delivery_photo.required' => 'Foto pengantaran wajib diunggah.',
            'delivery_photo.image' => 'File yang diunggah harus berupa gambar.',
            'delivery_photo.mimes' => 'Format gambar yang diperbolehkan: jpg, jpeg, png.',
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
    
            if ($status === 'Proses Pengantaran') {
                OrderStatus::create([
                    'order_id' => $order->id,
                    'status_id' => 2, // Order Dalam Masa Sewa
                ]);
            } elseif ($status === 'Proses Penggantian Tanaman') {
                $maxBatch = $order->orderItems()->max('replacement_batch');
                $olderBatch = $order->orderItems()
                    ->where('replacement_batch', $maxBatch - 1)
                    ->get();
                foreach ($olderBatch as $item) {
                    $plant = Plant::findOrFail($item->plant_id);
                    $plant->stock += $item->quantity;
                    $plant->save();
                }

                OrderStatus::create([
                    'order_id' => $order->id,
                    'status_id' => 2, // Order Proses Pengambilan Kembali
                ]);
            } elseif ($status === 'Proses Pengambilan Kembali') {
                $lastBatch = $order->orderItems()->where('replacement_batch', $order->orderItems()->max('replacement_batch'))->get();
                foreach ($lastBatch as $item) {
                    $plant = Plant::findOrFail($item->plant_id);
                    $plant->stock += $item->quantity;
                    $plant->save();
                }

                OrderStatus::create([
                    'order_id' => $order->id,
                    'status_id' => 5, // Order Selesai
                ]);
            }
    
            DB::commit();
    
            return redirect()->route('dashboard.kelola.delivery')->with('success', 'Konfirmasi Pengantaran Berhasil.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal Konfirmasi Pengantaran: ' . $e->getMessage());
        }
    }
}
