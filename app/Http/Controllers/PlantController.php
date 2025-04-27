<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\RendererStyle\RendererStyle; 
use BaconQrCode\Renderer\Image\SvgImageBackEnd;    
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Writer;

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

        // if (!Storage::disk('public')->exists('plants')) {
        //     Storage::disk('public')->makeDirectory('plants');
        // }
        
        // if (!Storage::disk('public')->exists('qrcodes')) {
        //     Storage::disk('public')->makeDirectory('qrcodes');
        // }

        // if ($request->hasFile('photo')) {
        //     $photoPath = $request->file('photo')->store('plants', 'public');
        //     $validatedData['photo'] = $photoPath;
        // }

        $plant = Plant::create($validatedData);

        // $rendererStyle = new RendererStyle(300); // Size in pixels
        // $imageBackEnd = new SvgImageBackEnd();
        // $renderer = new ImageRenderer($rendererStyle, $imageBackEnd);
        // $writer = new Writer($renderer);
        // $qrImage = $writer->writeString($plant->id);
        // $qrCodePath = 'qrcodes/' . $plant->id . '.svg';

        // Storage::disk('public')->put($qrCodePath, $qrImage);
        // $plant->update(['qr_code' => $qrCodePath]);

        return redirect()->route('dashboard.kelola.plant')->with('success', 'Plant added successfully.');
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

        // if (Storage::disk('public')->exists($plant->photo)) {
        //     Storage::disk('public')->delete($plant->photo);
        // }

        return redirect()->route('dashboard.kelola.plant')->with('success', 'Plant deleted successfully.');
    }
}
