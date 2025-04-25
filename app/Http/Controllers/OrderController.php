<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function tampilkanDataOrder()
    {
        $user = Auth::user();
        $order = Order::all();
        return view('dashboard.orders.index', compact('order', 'user'));
    } 

    public function tambahOrder(Request $request)
    {
        $validatedData = $request->validate([
            'plant_id' => 'required|exists:plants,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
        ]);

        Order::create($validatedData);

        return redirect()->route('dashboard.kelola.order')->with('success', 'Order added successfully.');
    }

    public function editOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validatedData = $request->validate([
            'plant_id' => 'required|exists:plants,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $order->update($validatedData);
        return redirect()->route('dashboard.kelola.order')->with('success', 'Order updated successfully.');
    }

    public function hapusOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('dashboard.kelola.order')->with('success', 'Order deleted successfully.');
    }
}
