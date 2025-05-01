<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Plant;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function tampilkanDataOrder()
    {
        $user = Auth::user();
        $order = Order::with('LatestStatus')->get();
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
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'assigned_deliverer_id' => 'required|exists:users,id',
            'plants' => 'required|array|min:1',
            'plants.*.plant_id' => 'required|exists:plants,id',
            'plants.*.quantity' => 'required|integer|min:1',
        ]);
        
        try {
            DB::beginTransaction();
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
            
            $paymentStatus = 'unpaid';
            if ($request->hasFile('payment_proof')) {
                $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
                $validatedData['payment_proof'] = $paymentProofPath;
                $paymentStatus = 'paid';
            }

            $order = Order::create([
                'customer_id' => $customerId,
                'order_date' => Carbon::now(),
                'rental_duration' => $validatedData['rental_duration'],
                'delivery_address' => $validatedData['delivery_address'],
                'total_price' => $totalPrice,
                'payment_status' => $paymentStatus,
                'payment_proof' => $validatedData['payment_proof'] ?? null,
                'assigned_deliverer_id' => $validatedData['assigned_deliverer_id'],
            ]);
    
            foreach ($validatedData['plants'] as $plantData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'plant_id' => $plantData['plant_id'],
                    'quantity' => $plantData['quantity'],
                ]);
            }

            $orderStatus = new OrderStatus();
            $status_id = 0;
            if ($paymentStatus == 'paid' && $validatedData['payment_proof'] !== null) {
                $status_id = 2; 
            } else {
                $status_id = 1; 
            }

            $orderStatus = OrderStatus::create([
                'order_id' => $order->id,
                'status_id' => $status_id, 
                'created_at' => Carbon::now(),
            ]);

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

        
        try {
            DB::beginTransaction();

            $orderItems = OrderItem::where('order_id', $id)->get();
            foreach ($orderItems as $item) {
                $item->delete();
            }

            $orderStatus = OrderStatus::where('order_id', $id)->get();
            foreach ($orderStatus as $status) {
                $status->delete();
            }

            $order->delete();

            DB::commit();
    
            return redirect()->route('dashboard.kelola.order')->with('success', 'Order Berhasil Dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus order: ' . $e->getMessage());
        }
    }
}
