<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.admin.login');
    }
    public function login(Request $r)
    {
        $cred = $r->validate(['email' => 'required|email', 'password' => 'required']);
        if (Auth::guard('admin')->attempt($cred, $r->boolean('remember'))) {
            $admin = Auth::guard('admin')->user();
            if (!$admin->is_active) {
                Auth::guard('admin')->logout();
                return back()->withErrors(['email' => 'Account disabled']);
            }
            $r->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }
    public function logout(Request $r)
    {
        Auth::guard('admin')->logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
