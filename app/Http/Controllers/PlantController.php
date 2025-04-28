<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use BaconQrCode\Renderer\ImageRenderer;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;    
use BaconQrCode\Renderer\RendererStyle\RendererStyle; 

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

        DB::beginTransaction();

        try{
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('plants', 'public');
                $validatedData['photo'] = $photoPath;
            }
    
            $plant = Plant::create($validatedData);

            DB::commit();

            return redirect()->route('dashboard.kelola.plant')->with('success', 'Tanaman Berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('dashboard.kelola.plant')->with('error', 'Gagal Menambahkan Tanaman' . $e->getMessage());
        }

        // if (!Storage::disk('public')->exists('plants')) {
        //     Storage::disk('public')->makeDirectory('plants');
        // }
        
        // if (!Storage::disk('public')->exists('qrcodes')) {
        //     Storage::disk('public')->makeDirectory('qrcodes');
        // }


        // $rendererStyle = new RendererStyle(300); // Size in pixels
        // $imageBackEnd = new SvgImageBackEnd();
        // $renderer = new ImageRenderer($rendererStyle, $imageBackEnd);
        // $writer = new Writer($renderer);
        // $qrImage = $writer->writeString($plant->id);
        // $qrCodePath = 'qrcodes/' . $plant->id . '.svg';

        // Storage::disk('public')->put($qrCodePath, $qrImage);
        // $plant->update(['qr_code' => $qrCodePath]);

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

        DB::beginTransaction();

        try {
            if ($request->hasFile('photo')) {
                if ($plant->photo && Storage::disk('public')->exists($plant->photo)) {
                    Storage::disk('public')->delete($plant->photo);
                }
                $photoPath = $request->file('photo')->store('plants', 'public');
                $validatedData['photo'] = $photoPath;
            }

            $plant->update($validatedData);

            DB::commit();

            return redirect()->route('dashboard.kelola.plant')->with('success', 'Tanaman Berhasil Diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Tanaman Gagal Diperbarui: ' . $e->getMessage());
        }
    }

    public function hapusPlant($id)
    {
        $plant = Plant::findOrFail($id);
        // if (Storage::disk('public')->exists($plant->photo)) {
        //     Storage::disk('public')->delete($plant->photo);
        // }
        DB::beginTransaction();

        try {
            if ($plant->photo && Storage::disk('public')->exists($plant->photo)) {
                Storage::disk('public')->delete($plant->photo);
            }

            $plant->delete();

            DB::commit();

            return redirect()->route('dashboard.kelola.plant')->with('success', 'Plant deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Failed to delete plant: ' . $e->getMessage());
        }
    }
}
