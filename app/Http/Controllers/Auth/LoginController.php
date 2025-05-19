<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 


class LoginController extends Controller
{
    public function tampilkanLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate(); // regenerate session untuk keamanan
            $user = Auth::user();
    
            if ($user->role === 'admin' || $user->role === 'super admin') {
                return redirect()->route('dashboard.index');
            } elseif ($user->role === 'delivery') {
                return redirect()->route('dashboard.kelola.delivery');
            } else {
                Auth::logout();
                return redirect()->route('login')->withErrors(['email' => 'Role tidak dikenali.']);
            }
        }

        return back()->withErrors(['Email atau Password Salah']);
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/login');
    }

    public function tampilkanKelolaUser()
    {
        $user = Auth::user();
        if ($user->role == 'super admin')
        {
            $allUser = User::all();
        } else 
        {
            $allUser = User::where('role', 'delivery')->get();
        }
        return view('auth.user', compact('user', 'allUser'));
    }

    public function register(Request $request)
    {   
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,delivery'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role harus salah satu dari: admin, delivery.',
        ]);

        try {

        $validatedData['password'] = bcrypt($validatedData['password']); 

        User::create($validatedData);

        return redirect()->back()->with('success', 'Registrasi berhasil. Silakan login menggunakan akun tersebut.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.']);
        }
    }

    public function hapusUser($id)
    {
        try {
            $user = Auth::user();
            if ($user->id == $id) {
                return redirect()->back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
            }
            $user = User::findOrFail($id);
            $user->delete();
            return redirect()->back()->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memeriksa pengguna.']);
        }
    }
}
