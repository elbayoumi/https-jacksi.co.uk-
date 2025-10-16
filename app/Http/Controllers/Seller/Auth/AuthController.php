<?php

namespace App\Http\Controllers\Seller\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.seller.login');
    }
    public function login(Request $r)
    {
        $cred = $r->validate(['email' => 'required|email', 'password' => 'required']);
        if (Auth::guard('seller')->attempt($cred, $r->boolean('remember'))) {
            $seller = Auth::guard('seller')->user();
            if (!$seller->is_active) {
                Auth::guard('seller')->logout();
                return back()->withErrors(['email' => 'Account disabled']);
            }
            $r->session()->regenerate();
            return redirect()->intended('/seller/dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }
    public function logout(Request $r)
    {
        Auth::guard('seller')->logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('seller.login');
    }
}
