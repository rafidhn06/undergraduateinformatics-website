<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    /**
     * Display a login form.
     */
    public function loginForm()
    {
        return view('auth.login');
    }

    /**
     * Attempt login
     */
    public function login(LoginRequest $request) {
        // Return back with error if credentials are invalid
        if (auth()->attempt($request->validated()) == false) {
            return back()->withError('Email atau kata sandi salah, silakan coba lagi');
        }

        $request->session()->regenerate();

        // Redirect to admin dashboard if credentials are valid
        $request->session()->flash('success', 'Berhasil masuk!');
        return redirect()->to(route('admin.datasets.index'));
    }

    /**
     * Function for logging out
     */
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->flash('success', 'Berhasil keluar!');
        return redirect(route('home'));
    }
}
