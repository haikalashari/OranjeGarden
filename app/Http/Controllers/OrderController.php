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
use App\Models\StatusCategory;
use App\Models\OrderDeliverers;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function tampilkanDataOrder(Request $request)
    {
        $user = Auth::user();
        $query = Order::with(['customer', 'latestStatus.status_category']);

        // Check if a search query exists
        if ($request->has('search') && $request->search != '') {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }
        $order = $query->paginate(10);
        
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
            'new_secondary_customer_contact' => 'nullable|string|max:20',
            'new_customer_email' => 'nullable|email|max:255',
            'order_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:order_date',
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
                    'secondary_contact_no' => $validatedData['new_secondary_customer_contact'],
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
                'end_date' => $validatedData['end_date'],
                'delivery_address' => $validatedData['delivery_address'],
                'payment_status' => $paymentStatus,
                'payment_proof' => $validatedData['payment_proof'] ?? null,
                'total_price' => $totalPrice,
            ]);

            if($validatedData['assigned_deliverer_id']) {
                OrderDeliverers::create([
                    'order_id' => $order->id,
                    'user_id' => $validatedData['assigned_deliverer_id'],
                    'delivery_batch' => 0,
                    'delivery_photo' => null,
                    'status' => 'Mengantar',
                ]);
            }
    
            foreach ($validatedData['plants'] as $plantData) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'plant_id' => $plantData['plant_id'],
                    'quantity' => $plantData['quantity'],
                    'replacement_batch' => 0,
                ]);
            }

            $orderStatus = new OrderStatus();
            $status_id = 1; 
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

    public function tampilkanDetailOrder($id)
    {
        $user = Auth::user();
        $deliverers = User::where('role', 'delivery')->get();

        $plants = Plant::all();
        // Ambil data order beserta relasi yang diperlukan
        $order = Order::with([
            'customer', // Relasi ke customer
            'orderItems', // Relasi ke item order
            'orderItems.plant', // Relasi ke item order dan tanaman
            'status.status_category', // Relasi ke status order dan kategori status
            'deliverer', // Relasi ke pengantar
            'deliverer.user', // Relasi ke user pengantar
        ])->findOrFail($id);

        // Ambil riwayat status order
        $orderStatuses = $order->status()->with('status_category')->get();

        // Ambil riwayat pengiriman
        $deliveries = $order->deliverer()->get();

        // Ambil data invoice jika ada
        $invoices = $order->invoices()->get();

        return view('dashboard.orders.detail', compact('user', 'order', 'orderStatuses', 'deliveries', 'invoices', 'plants', 'deliverers'));
    }
    
    public function tampilkanEditOrder($id)
    {
        $user = Auth::user();
        $order = Order::with('LatestStatus')->findOrFail($id);
        $customers = Customer::all();
        $plants = Plant::all();
        $statuses = StatusCategory::all();
        $deliverers = User::where('role', 'delivery')->get();
        return view('dashboard.orders.edit', compact('order', 'user', 'customers', 'deliverers', 'plants', 'statuses'));
    }

    public function editOrder(Request $request, $id)
    {
        $validatedData = $request->validate([
            'items' => 'required|array',
            'items.*.plant_id' => 'required|exists:plants,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $order = Order::findOrFail($id);
        
        try {
            DB::beginTransaction();
    
        $order->orderItems()->delete(); // Hapus semua item lama
        foreach ($validatedData['items'] as $item) {
            $order->orderItems()->create([
                'plant_id' => $item['plant_id'],
                'quantity' => $item['quantity'],
                'replacement_batch' => 0,
            ]);
        }

        DB::commit();
        return redirect()->route('dashboard.kelola.order.detail', $order->id)->with('success', 'Order berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal memperbarui order: ' . $e->getMessage());
        }
    }

    public function editPaymentProof(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validatedData = $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Validasi file gambar
        ]);

        try {
            DB::beginTransaction();

            // Hapus bukti pembayaran lama jika ada
            if ($order->payment_proof && Storage::exists('public/' . $order->payment_proof)) {
                Storage::delete('public/' . $order->payment_proof);
            }

            // Simpan bukti pembayaran baru
            $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
            $order->payment_proof = $paymentProofPath;
            $order->save();

            // Update status pembayaran
            $order->payment_status = 'paid';
            $order->save();

            DB::commit();
            return redirect()->back()->with('success', 'Bukti pembayaran berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui bukti pembayaran: ' . $e->getMessage());
        }
    }

    public function deletePaymentProof($id)
    {
        $order = Order::findOrFail($id);

        try {
            if ($order->payment_proof && Storage::exists('public/' . $order->payment_proof)) {
                Storage::delete('public/' . $order->payment_proof);
            }

            $order->payment_proof = null;
            $order->save();

            $order->payment_status = 'unpaid';
            $order->save();

            return redirect()->back()->with('success', 'Bukti pembayaran berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus bukti pembayaran: ' . $e->getMessage());
        }
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

            $orderDeliverers = OrderDeliverers::where('order_id', $id)->get();
            foreach ($orderDeliverers as $deliverer) {
                $deliverer->delete();
            }

            $order->delete();

            DB::commit();
    
            return redirect()->route('dashboard.kelola.order')->with('success', 'Order Berhasil Dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menghapus order: ' . $e->getMessage());
        }
    }

    public function orderSelesai($id)
    {
        $order = Order::findOrFail($id);

        try {
            DB::beginTransaction();

        $latestStatus = $order->status()->latest()->first();
        if ($latestStatus && $latestStatus->status_category->status === 'Order Selesai') {
            return redirect()->route('dashboard.kelola.order')->with('error', 'Order sudah diselesaikan sebelumnya.');
        }
        if ($latestStatus && $latestStatus->status_category->status === 'Order Dibatalkan') {
            return redirect()->route('dashboard.kelola.order')->with('error', 'Order sudah dibatalkan sebelumnya.');
        }

        OrderStatus::create([
            'order_id' => $id,
            'status_id' => 5, // ID untuk status "Order Selesai"
            'created_at' => Carbon::now(),
        ]);

        $orderDeliverers = OrderDeliverers::where('order_id', $id)->get();
        foreach ($orderDeliverers as $deliverer) {
            $deliverer->update([
                'delivery_photo' => 'Admin Konfirm',
            ]);
        }

        DB::commit();
        return redirect()->route('dashboard.kelola.order')->with('success', 'Order berhasil diselesaikan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menyelesaikan order: ' . $e->getMessage());
        }
    }

    public function orderDibatalkan($id)
    {
        $order = Order::findOrFail($id);

        try {
        DB::beginTransaction();
        $latestStatus = $order->status()->latest()->first();
        if ($latestStatus && $latestStatus->status_category->status === 'Order Selesai') {
            return redirect()->route('dashboard.kelola.order')->with('error', 'Order sudah diselesaikan sebelumnya.');
        }
        if ($latestStatus && $latestStatus->status_category->status === 'Order Dibatalkan') {
            return redirect()->route('dashboard.kelola.order')->with('error', 'Order sudah dibatalkan sebelumnya.');
        }

        OrderStatus::create([
            'order_id' => $id,
            'status_id' => 6, // ID untuk status "Order Dibatalkan"
            'created_at' => Carbon::now(),
        ]);

        $orderDeliverers = OrderDeliverers::where('order_id', $id)->get();
        foreach ($orderDeliverers as $deliverer) {
            $deliverer->update([
                'delivery_photo' => 'Admin Konfirm',
            ]);
        }

        DB::commit();
        return redirect()->route('dashboard.kelola.order')->with('success', 'Order berhasil dibatalkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal membatalkan order: ' . $e->getMessage());
        }
    }

    public function tambahTanamanBatchBaru(Request $request, $id)
    {
        $validated = $request->validate([
            'plants' => 'required|array',
            'plants.*.plant_id' => 'required|exists:plants,id',
            'plants.*.quantity' => 'required|integer|min:1',
            'deliverer_id' => 'required|exists:users,id',
        ]);

        try {
            DB::beginTransaction();
            $order = Order::findOrFail($id);

            $replacement_batch = $order->orderItems()->max('replacement_batch') + 1; // Ambil batch terakhir dan tambahkan 1

            foreach ($validated['plants'] as $plant) {
                $order->orderItems()->create([
                    'plant_id' => $plant['plant_id'],
                    'quantity' => $plant['quantity'],
                    'replacement_batch' => $replacement_batch, // Tambahkan batch baru
                ]);
            }

            $batchDeliverer = OrderDeliverers::where('order_id', $id)
                ->latest('created_at')
                ->first();

            $orderDeliverers = new OrderDeliverers();
            $derliverersBatchBaru = $orderDeliverers->create([
                'order_id' => $id,
                'user_id' => $validated['deliverer_id'],
                'delivery_batch' => $batchDeliverer->delivery_batch + 1, // Batch baru
                'delivery_photo' => null,
                'status' => 'Mengganti',
            ]);
            

            DB::commit();
            return redirect()->route('dashboard.kelola.order.detail', $order->id)->with('success', 'Tanaman batch baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menambahkan batch tanaman baru: ' . $e->getMessage());
        }
    }

    public function assignDelivererPengambilanKembali(Request $request, $id)
    {
        $validated = $request->validate([
            'deliverer_id' => 'required|exists:users,id',
        ]);

        try {
            DB::beginTransaction();
            $order = Order::findOrFail($id);

            $batchDeliverer = OrderDeliverers::where('order_id', $id)
                ->latest('created_at')
                ->first();


            $orderDeliverers = new OrderDeliverers();
            $orderDeliverers->create([
                'order_id' => $id,
                'user_id' => $validated['deliverer_id'],
                'delivery_batch' => $batchDeliverer->delivery_batch + 1, // Batch baru
                'delivery_photo' => null,
                'status' => 'Ambil Kembali',
            ]);

            DB::commit();
            return redirect()->route('dashboard.kelola.order.detail', $order->id)->with('success', 'Pengantar pengambilan kembali berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal menambahkan pengantar pengambilan kembali: ' . $e->getMessage());
        }
    }

    public function generateInvoice($id)
    {
        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Ambil data order berdasarkan ID
            $order = Order::with(['customer', 'orderItems.plant'])->findOrFail($id);

            // Buat array untuk menyimpan data order items yang dikelompokkan
            $groupedItems = [
                'tanaman kecil' => ['quantity' => 0, 'amount' => 0],
                'tanaman sedang' => ['quantity' => 0, 'amount' => 0],
                'tanaman besar' => ['quantity' => 0, 'amount' => 0],
            ];

            $totalAmount = 0;

            // Kelompokkan order items berdasarkan kategori dan hitung total quantity dan amount
            foreach ($order->orderItems->where('replacement_batch', 0) as $item) { // Hanya batch 0
                $category = $item->plant->category ?? 'Uncategorized';
                $amount = $item->quantity * $item->plant->price;

                if ($category === 'kecil') {
                    $groupedItems['tanaman kecil']['quantity'] += $item->quantity;
                    $groupedItems['tanaman kecil']['amount'] += $amount;
                } elseif ($category === 'sedang') {
                    $groupedItems['tanaman sedang']['quantity'] += $item->quantity;
                    $groupedItems['tanaman sedang']['amount'] += $amount;
                } elseif ($category === 'besar') {
                    $groupedItems['tanaman besar']['quantity'] += $item->quantity;
                    $groupedItems['tanaman besar']['amount'] += $amount;
                }

                $totalAmount += $amount;
            }

            // Cek apakah total amount dari perhitungan sama dengan total_price dari tabel orders
            if ($totalAmount != $order->total_price) {
                return redirect()->back()->with('error', 'Total amount tidak sesuai dengan total_price dari order.');
            }

            // Siapkan biaya instalasi dan keterangan
            $installationFee = request('installation_fee', 0);
            $invoiceNote = request('invoice_note', ''); // Ambil keterangan dari form
            $grandTotal = $totalAmount + $installationFee;

            // Generate nomor invoice
            $lastInvoice = DB::table('invoices')->latest('id')->first();
            $invoiceNumber = 'INV-' . str_pad(($lastInvoice->id ?? 0) + 1, 6, '0', STR_PAD_LEFT);

            // Cek apakah invoice untuk order ini sudah ada
            $existingInvoice = DB::table('invoices')->where('order_id', $order->id)->first();
            if ($existingInvoice) {
                // Hapus file PDF lama dari storage
                if (Storage::exists('public/' . $existingInvoice->invoice_pdf_path)) {
                    Storage::delete('public/' . $existingInvoice->invoice_pdf_path);
                }

                // Hapus data invoice lama dari database
                DB::table('invoices')->where('id', $existingInvoice->id)->delete();
            }

            // Data untuk dikirim ke view
            $data = [
                'order' => $order,
                'groupedItems' => $groupedItems,
                'totalAmount' => $totalAmount,
                'installationFee' => $installationFee,
                'grandTotal' => $grandTotal,
                'invoiceNumber' => $invoiceNumber,
                'invoiceNote' => $invoiceNote,
            ];

            // Generate PDF menggunakan view
            $pdf = Pdf::loadView('invoices.template', $data)
            ->setPaper('a4', 'portrait'); 
            
            // Simpan PDF ke storage
            $fileName = 'invoices/invoice-' . $order->id . '.pdf';
            Storage::put('public/' . $fileName, $pdf->output());

            // Simpan data invoice ke database
            DB::table('invoices')->insert([
                'order_id' => $order->id,
                'invoice_number' => $invoiceNumber,
                'invoice_pdf_path' => $fileName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Commit transaksi database
            DB::commit();

            // Download PDF
            return $pdf->download('invoice-' . $order->id . '.pdf');
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat invoice: ' . $e->getMessage());
        }
    }
}
