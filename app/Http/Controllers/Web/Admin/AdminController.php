<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    /**
     * Display a login form.
     */
    public function loginForm()
    {
        return view('LoginPage');
    }

    /**
     * Attempt login
     */
    public function login(Request $request) {
        // Return back with error if credentials are invalid
        if (auth()->attempt(request(['email', 'password'])) == false) {
            return back()->withError('Email atau password salah, silahkan coba lagi');
        }

        // Redirect to admin dashboard if credentials are valid
        $request->session()->flash('success', 'Berhasil log in!');
        return redirect()->to(route('admin.datasets.index'));
    }

    /**
     * Function for logging out
     */
    public function logout() {
        Session::flush();
        Auth::logout();
        request()->session()->flash('success', 'Berhasil log out!');
        return redirect(route('home'));
    }
}
