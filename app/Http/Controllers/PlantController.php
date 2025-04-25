<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlantController extends Controller
{
    public function tampilkanDataPlant()
    {
        $user = Auth::user();
        $plants = Plant::all();
        return view('dashboard.plants.index', compact('plants', 'user'));
    }

    public function tambahPlant(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        Plant::create($validatedData);

        return redirect()->route('dashboard.kelola.plant')->with('success', 'Plant added successfully.');
    }

    public function detailPlant($id)
    {
        $plant = Plant::findOrFail($id);
        return view('dashboard.kelola.plant-detail', compact('plant'));
    }

    public function editPlant(Request $request, $id)
    {
        $plant = Plant::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $plant->update($validatedData);
        return redirect()->route('dashboard.kelola.plant')->with('success', 'Plant updated successfully.');
    }

    public function hapusPlant($id)
    {
        $plant = Plant::findOrFail($id);
        $plant->delete();

        return redirect()->route('dashboard.kelola.plant')->with('success', 'Plant deleted successfully.');
    }
}
