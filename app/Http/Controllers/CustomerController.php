<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class CustomerController extends Controller
{
    public function tampilkanDataCustomer()
    {
        $user = Auth::user();
        $query = Customer::with('orders')
        ->withCount('orders')
        ->withSum('orders', 'total_price');

        if (request()->has('search') && request()->search != '') {
            $query->where('name', 'like', '%' . request()->search . '%');
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('dashboard.customers.index', compact('customers', 'user'));
    }

    public function tambahCustomer(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:15',
            'secondary_contact_no' => 'nullable|string|max:15',
            'email' => 'nullable|email|unique:customers,email',
        ]);

        DB::beginTransaction();

        try {
            Customer::create($validatedData);

            DB::commit();

            return redirect()->route('dashboard.kelola.customer')->with('success', 'Customer Berhasil Ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Gagal Menambahkan Customer: ' . $e->getMessage());
        }
    }

    public function editCustomer(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:15',
            'secondary_contact_no' => 'nullable|string|max:15',
            'email' => [
                'required',
                'email',
                Rule::unique('customers', 'email')->ignore($customer->id),
            ],
        ]);

        DB::beginTransaction();

        try {
            $customer->update($validatedData);

            DB::commit();

            return redirect()->route('dashboard.kelola.customer')->with('success', 'Customer Berhasil Diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal Memperbarui Customer: ' . $e->getMessage());
        }
    }

    public function hapusCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        DB::beginTransaction();

        try {
            $customer->delete();

            DB::commit();

            return redirect()->route('dashboard.kelola.customer')->with('success', 'Customer Berhasil Dihapus.');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Gagal Menghapus Customer: ' . $e->getMessage());
        }
    }
}
