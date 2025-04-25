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
        $order = Order::all();
        return view('dashboard.delivery.index', compact('order', 'user'));
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
