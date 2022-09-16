<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function authentic(Request $request)
    {
        if (Auth::guard('divisi')->attempt(['nama_divisi' => $request->divisi, 'password' => $request->password])) {
            if (Auth::user()->role_id == 1) {
                return redirect('/home/admin');
            } else {
                return redirect('/home');
            }
            
        }
        // $request->session()->flash('status', 'failed');
        // $request->session()->flash('message', 'login wrong!');
        
        return redirect('/login');
    }

    public function logout(Request $request){
        Auth::guard('divisi')->logout();
        
        $request->session()->invalidate();
 
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
