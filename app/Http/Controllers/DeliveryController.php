<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function tampilkanDataDelivery()
    {
        $user = Auth::user();
        $orders = Order::with(['customer', 'status.status_category'])
        ->whereHas('status.status_category', function ($query) {
            $query->whereIn('status', [
                'Proses Pengantaran',
                'Proses Penggantian Tanaman',
                'Proses Pengambilan Kembali',
            ]);
        })
        ->get();
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
        $order = Order::findOrFail($id);

        // Validasi data yang diterima
        $validatedData = $request->validate([
            'delivery_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|string|max:255',
        ]);

        // Simpan foto pengantaran
        if ($request->hasFile('delivery_photo')) {
            $file = $request->file('delivery_photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/delivery'), $filename);
            $validatedData['delivery_photo'] = $filename;
        }

        // Update status order

        $order->update($validatedData);

        return redirect()->route('dashboard.kelola.delivery')->with('success', 'Delivery confirmed successfully.');
    }

    public function tambahDelivery(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'delivery_date' => 'required|date',
            'status' => 'required|string|max:255',
        ]);

        Delivery::create($validatedData);

        return redirect()->route('dashboard.kelola.delivery')->with('success', 'Delivery added successfully.');
    }

    public function editDelivery(Request $request, $id)
    {
        $delivery = Delivery::findOrFail($id);

        $validatedData = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'delivery_date' => 'required|date',
            'status' => 'required|string|max:255',
        ]);

        $delivery->update($validatedData);
        return redirect()->route('dashboard.kelola.delivery')->with('success', 'Delivery updated successfully.');
    }

    public function hapusDelivery($id)
    {
        $delivery = Delivery::findOrFail($id);
        $delivery->delete();

        return redirect()->route('dashboard.kelola.delivery')->with('success', 'Delivery deleted successfully.');
    }
}
