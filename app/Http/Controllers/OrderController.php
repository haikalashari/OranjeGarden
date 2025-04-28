<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Plant;
use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function tampilkanDataOrder()
    {
        $user = Auth::user();
        $order = Order::all();
        $customers = Customer::all();
        $plants = Plant::all();
        $deliverers = User::where('role', 'delivery ')->get();
        return view('dashboard.orders.index', compact('order', 'user', 'customers', 'deliverers', 'plants'));
    } 

    public function tambahOrder(Request $request)
    {
        $validatedData = $request->validate([
            'customer_id' => [
                'required',
                Rule::in(array_merge(['new'], Customer::pluck('id')->toArray())),
            ],
            'new_customer_name' => 'nullable|string|max:255',
            'new_customer_contact' => 'nullable|string|max:20',
            'new_customer_email' => 'nullable|email|max:255',
            'rental_duration' => 'required|integer|min:1',
            'delivery_address' => 'required|string',
            'payment_status' => 'required|in:paid,unpaid', 
            'assigned_deliverer_id' => 'nullable|exists:users,id',
            'plants' => 'required|array|min:1',
            'plants.*.plant_id' => 'required|exists:plants,id',
            'plants.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {

            if ($validatedData['customer_id'] === 'new') {
                $customer = Customer::create([
                    'name' => $validatedData['new_customer_name'],
                    'contact_no' => $validatedData['new_customer_contact'],
                    'email' => $validatedData['new_customer_email'],
                ]);
                $customerId = $customer->id;
            } else {
                $customerId = $validatedData['customer_id'];
            }
    
            $totalPrice = 0;
    
            foreach ($validatedData['plants'] as $plantData) {
                $plant = Plant::findOrFail($plantData['plant_id']);
                $totalPrice += $plant->price * $plantData['quantity'];
            }
    
            $order = Order::create([
                'customer_id' => $customerId,
                'order_date' => Carbon::now(),
                'rental_duration' => $validatedData['rental_duration'],
                'delivery_address' => $validatedData['delivery_address'],
                'total_price' => $totalPrice,
                'payment_status' => $validatedData['payment_status'],
                'assigned_deliverer_id' => $validatedData['assigned_deliverer_id'],
            ]);
    
            foreach ($validatedData['plants'] as $plantData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'plant_id' => $plantData['plant_id'],
                    'quantity' => $plantData['quantity'],
                ]);
            }

            DB::commit();
        return redirect()->route('dashboard.kelola.order')->with('success', 'Order berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menambahkan order: ' . $e->getMessage());
        }
    
    }
    

    public function editOrder(Request $request, $id)
    {
    
    }

    public function hapusOrder($id)
    {
        $order = Order::findOrFail($id);

        DB::beginTransaction();
        try {
            $order->delete();

            $orderItems = OrderItem::where('order_id', $id)->get();
            foreach ($orderItems as $item) {
                $item->delete();
            }
    
            return redirect()->route('dashboard.kelola.order')->with('success', 'Order Berhasil Dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus order: ' . $e->getMessage());
        }
    }
}
