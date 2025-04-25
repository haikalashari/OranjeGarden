<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function tampilkanDataCustomer()
    {
        $user = Auth::user();
        $customers = Customer::all();
        return view('dashboard.customers.index', compact('customers', 'user'));
    }

    public function tambahCustomer(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'required|string|max:15',
        ]);

        Customer::create($validatedData);

        return redirect()->route('dashboard.kelola.customer')->with('success', 'Customer added successfully.');
    }

    public function editCustomer(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:15',
        ]);

        $customer->update($validatedData);
        return redirect()->route('dashboard.kelola.customer')->with('success', 'Customer updated successfully.');
    }

    public function hapusCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('dashboard.kelola.customer')->with('success', 'Customer deleted successfully.');
    }
}
