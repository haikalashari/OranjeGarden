<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    public function tampilkanLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard.index');
        } else {
            dd('Authentication failed', $credentials, \App\Models\User::where('email', $credentials['email'])->first());
        }
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

        return redirect('auth.login')->with('success', 'Registrasi berhasil. Silakan login.');
    }
}
