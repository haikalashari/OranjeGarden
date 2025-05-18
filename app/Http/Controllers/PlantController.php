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
        $query = Plant::query();

        if (request()->has('search') && request()->search != '') {
            $query->where('name', 'like', '%' . request()->search . '%');
        }
    
        $plants = $query->paginate(15);
        return view('dashboard.plants.index', compact('plants', 'user'));
    }

    public function tambahPlant(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock' => 'required|integer',
            'price' => 'required|numeric',
            'category' => 'required|string|in:kecil,sedang,besar',
        ],[
            'name.required' => 'Nama wajib diisi.',
            'photo.required' => 'Foto wajib diunggah.',
            'photo.image' => 'Foto harus berupa gambar.',
            'photo.mimes' => 'Foto harus berformat jpeg, png, jpg, atau gif.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.integer' => 'Stok harus berupa angka.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'category.required' => 'Kategori wajib diisi.',
            'category.in' => 'Kategori harus salah satu dari: kecil, sedang, besar.',
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
    }

    public function editPlant(Request $request, $id)
    {
        $plant = Plant::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock' => 'required|integer',
            'price' => 'required|numeric',
            'category' => 'required|string|in:kecil,besar',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'photo.image' => 'Foto harus berupa gambar.',
            'photo.mimes' => 'Foto harus berformat jpeg, png, jpg, atau gif.',
            'stock.required' => 'Stok wajib diisi.',
            'stock.integer' => 'Stok harus berupa angka.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'category.required' => 'Kategori wajib diisi.',
            'category.in' => 'Kategori harus salah satu dari: kecil, besar.',
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

            return redirect()->route('dashboard.kelola.plant')->with('success', 'Tanaman Berhasil Dihapus.');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with('error', 'Tanaman Gagal Dihapus: ' . $e->getMessage());
        }
    }
}
