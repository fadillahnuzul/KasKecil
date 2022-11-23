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
        if (Auth::guard('web')->attempt(['username' => $request->username, 'password' => $request->password])) {
            if (Auth::user()->kk_access == '1') {
                return redirect('/home_admin');
            } else if (Auth::user()->kk_access == '2') {
                return redirect('/home');
            } else if (Auth::user()->kk_access == '3') {
                return redirect('/home_bank');
            }
            
        }
        // $request->session()->flash('status', 'failed');
        // $request->session()->flash('message', 'login wrong!');
        
        return redirect('/login');
    }

    public function logout(Request $request){
        Auth::guard('web')->logout();
        
        $request->session()->invalidate();
 
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
