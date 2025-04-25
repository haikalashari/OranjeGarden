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
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->route('dashboard.index');
        }

        return back()->withErrors(['Email atau Password Salah']);
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/login');
    }

    public function tampilkanRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,delivery'],
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']); 

        User::create($validatedData);

        return redirect('/login')->with('success', 'Registrasi berhasil. Silakan login.');
    }
}
