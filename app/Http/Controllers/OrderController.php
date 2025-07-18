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

        if ($request->has('search') && $request->search != '') {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }
        $order = $query->paginate(10);
        
        $customers = Customer::all();
        $plants = Plant::all();
        $deliverers = User::where('role', 'delivery ')->get();
        $statuses = StatusCategory::all(); 

        return view('dashboard.orders.index', compact('order', 'user', 'customers', 'deliverers', 'plants', 'statuses'));
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
        ], [
            'customer_id.required' => 'Pilih customer.',
            'new_customer_name.string' => 'Nama customer harus berupa string.',
            'new_customer_contact.string' => 'Nomor kontak harus berupa string.',
            'new_customer_email.email' => 'Email tidak valid.',
            'order_date.required' => 'Tanggal order wajib diisi.',
            'end_date.required' => 'Tanggal akhir wajib diisi.',
            'delivery_address.required' => 'Alamat pengiriman wajib diisi.',
            'payment_proof.image' => 'Bukti pembayaran harus berupa gambar.',
            'plants.required' => 'Tanaman wajib dipilih.',
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

                if ($plant->stock < $plantData['quantity']) {
                    throw new \Exception("Stok tanaman {$plant->name} tidak mencukupi.");
                }
            }

            foreach ($validatedData['plants'] as $plantData) {
                $plant = Plant::findOrFail($plantData['plant_id']);

                $plant->stock -= $plantData['quantity'];
                $plant->save();

                $totalPrice += $plant->price * $plantData['quantity'];
            }

            $startDate = Carbon::parse($validatedData['order_date']);
            $endDate = Carbon::parse($validatedData['end_date']);
            $daysDiff = $startDate->diffInDays($endDate);

            $fullMonths = intdiv($daysDiff, 29); 
            $monthMultiplier = max(1, $fullMonths);
            $totalPrice *= $monthMultiplier;
            
            $paymentStatus = 'unpaid';
            if ($request->hasFile('payment_proof')) {
                $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
                $validatedData['payment_proof'] = $paymentProofPath;
                $paymentStatus = 'paid';
            }


            $order = Order::create([
                'customer_id' => $customerId,
                'order_date' => $validatedData['order_date'],
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
        $order = Order::with([
            'customer', // Relasi ke customer
            'orderItems', // Relasi ke item order
            'orderItems.plant', // Relasi ke item order dan tanaman
            'status.status_category', // Relasi ke status order dan kategori status
            'deliverer', // Relasi ke pengantar
            'deliverer.user', // Relasi ke user pengantar
        ])->findOrFail($id);

        $orderStatuses = $order->status()->with('status_category')->get();

        $deliveries = $order->deliverer()->get();

        $lastBatchNumber = $order->orderItems->max('replacement_batch');
        $pendingBatchDeliverer = $order->deliverer
            ->where('delivery_batch', $lastBatchNumber)
            ->where('status', 'Mengganti')
            ->whereNull('delivery_photo')
            ->first();

        $pendingPickupDeliverer = $order->deliverer
            ->where('status', 'Ambil Kembali')
            ->whereNull('delivery_photo')
            ->first();

        $invoices = $order->invoices()->get();

        return view('dashboard.orders.detail', compact('user', 'order', 'orderStatuses', 'deliveries', 'invoices', 'plants', 'deliverers', 'pendingBatchDeliverer', 'lastBatchNumber', 'pendingPickupDeliverer'));
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
        ], [
            'items.required' => 'Tanaman wajib dipilih.',
            'items.*.plant_id.required' => 'ID tanaman wajib diisi.',
            'items.*.plant_id.exists' => 'Tanaman tidak ditemukan.',
            'items.*.quantity.required' => 'Jumlah tanaman wajib diisi.',
            'items.*.quantity.integer' => 'Jumlah tanaman harus berupa angka.',
            'items.*.quantity.min' => 'Jumlah tanaman minimal 1.',
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
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048', 
        ], [
            'payment_proof.required' => 'Bukti pembayaran wajib diunggah.',
            'payment_proof.image' => 'File yang diunggah harus berupa gambar.',
            'payment_proof.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg.',
        ]);

        try {
            DB::beginTransaction();

            if ($order->payment_proof && Storage::exists('public/' . $order->payment_proof)) {
                Storage::delete('public/' . $order->payment_proof);
            }

            $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
            $order->payment_proof = $paymentProofPath;
            $order->save();

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
                $plant = Plant::findOrFail($item->plant_id);
                $plant->stock += $item->quantity;
                $plant->save();

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
            'status_id' => 5, 
            'created_at' => Carbon::now(),
        ]);

        $lastItemBatch = $order->orderItems()->max('replacement_batch');
        $lastBatch = $order->orderItems()->where('replacement_batch', $lastItemBatch)->get();
        foreach ($lastBatch as $item) {
            $plant = Plant::findOrFail($item->plant_id);
            $plant->stock += $item->quantity;
            $plant->save();
        }

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

        $lastBatch = $order->orderItems()->max('replacement_batch');
        $lastBatch = $order->orderItems()->where('replacement_batch', $lastBatch)->get();
        foreach ($lastBatch as $item) {
            $plant = Plant::findOrFail($item->plant_id);
            $plant->stock += $item->quantity;
            $plant->save();
        }

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
        ], [
            'plants.required' => 'Tanaman wajib dipilih.',
            'plants.*.plant_id.required' => 'ID tanaman wajib diisi.',
            'plants.*.plant_id.exists' => 'Tanaman tidak ditemukan.',
            'plants.*.quantity.required' => 'Jumlah tanaman wajib diisi.',
            'plants.*.quantity.integer' => 'Jumlah tanaman harus berupa angka.',
            'plants.*.quantity.min' => 'Jumlah tanaman minimal 1.',
            'deliverer_id.required' => 'Pengantar wajib dipilih.',
        ]);

        try {
            DB::beginTransaction();
            $order = Order::findOrFail($id);

            foreach ($validated['plants'] as $plantData) {
                $plant = Plant::findOrFail($plantData['plant_id']);

                if ($plant->stock < $plantData['quantity']) {
                    throw new \Exception("Stok tanaman {$plant->name} tidak mencukupi.");
                }

                $plant->stock -= $plantData['quantity'];
                $plant->save();
            }

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
        ], [
            'deliverer_id.required' => 'Pengantar wajib dipilih.',
            'deliverer_id.exists' => 'Pengantar tidak ditemukan.',
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
            $startDate = \Carbon\Carbon::parse($order->order_date);
            $endDate = \Carbon\Carbon::parse($order->end_date);
            $daysDiff = $startDate->diffInDays($endDate);
            $fullMonths = intdiv($daysDiff, 29);
            $monthMultiplier = max(1, $fullMonths);

            foreach ($order->orderItems->where('replacement_batch', 0) as $item) {
                $category = $item->plant->category ?? 'Uncategorized';
                $unitPrice = $item->plant->price;
                $quantity = $item->quantity;
                $amount = $quantity * $unitPrice * $monthMultiplier;

                if ($category === 'kecil') {
                    $groupedItems['tanaman kecil']['quantity'] += $quantity;
                    $groupedItems['tanaman kecil']['amount'] += $amount;
                } elseif ($category === 'sedang') {
                    $groupedItems['tanaman sedang']['quantity'] += $quantity;
                    $groupedItems['tanaman sedang']['amount'] += $amount;
                } elseif ($category === 'besar') {
                    $groupedItems['tanaman besar']['quantity'] += $quantity;
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

    public function editTanamanBatchBaru(Request $request, $orderId, $batch)
    {
        $validated = $request->validate([
            'plants' => 'required|array',
            'plants.*.plant_id' => 'required|exists:plants,id',
            'plants.*.quantity' => 'required|integer|min:1',
            'deliverer_id' => 'required|exists:users,id',
        ], [
            'plants.required' => 'Tanaman wajib dipilih.',
            'plants.*.plant_id.required' => 'ID tanaman wajib diisi.',
            'plants.*.plant_id.exists' => 'Tanaman tidak ditemukan.',
            'plants.*.quantity.required' => 'Jumlah tanaman wajib diisi.',
            'plants.*.quantity.integer' => 'Jumlah tanaman harus berupa angka.',
            'plants.*.quantity.min' => 'Jumlah tanaman minimal 1.',
            'deliverer_id.required' => 'Pengantar wajib dipilih.',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::findOrFail($orderId);

            // Pastikan batch belum dikonfirmasi delivery
            $batchDeliverer = $order->deliverer()
                ->where('delivery_batch', $batch)
                ->where('status', 'Mengganti')
                ->whereNull('delivery_photo')
                ->first();

            if (!$batchDeliverer) {
                return redirect()->back()->with('error', 'Batch sudah dikonfirmasi, tidak dapat diubah.');
            }

            // Hapus orderItems batch ini
           $orderItemsBatch = $order->orderItems()->where('replacement_batch', $batch)->get();
            foreach ($orderItemsBatch as $item) {
                $plant = Plant::findOrFail($item->plant_id);
                $plant->stock += $item->quantity;
                $plant->save();
                $item->delete();
            }

            // Tambahkan orderItems baru
            foreach ($validated['plants'] as $plantData) {
                $plant = Plant::findOrFail($plantData['plant_id']);
                if ($plant->stock < $plantData['quantity']) {
                    throw new \Exception("Stok tanaman {$plant->name} tidak mencukupi.");
                }
                $plant->stock -= $plantData['quantity'];
                $plant->save();

                $order->orderItems()->create([
                    'plant_id' => $plantData['plant_id'],
                    'quantity' => $plantData['quantity'],
                    'replacement_batch' => $batch,
                ]);
            }

            // Update deliverer batch
            $batchDeliverer->user_id = $validated['deliverer_id'];
            $batchDeliverer->save();

            DB::commit();
            return redirect()->route('dashboard.kelola.order.detail', $order->id)->with('success', 'Batch baru berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengubah batch baru: ' . $e->getMessage());
        }
    }
}
