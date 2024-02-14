<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;

class AdminLoginController extends Controller
{
    function login()
    {
        return view('admin.login');
    }

    function logData(Request $request)
    {
        $userName = $request->userName;
        $password = $request->password;

        $admin = Admin::where('user_name','=',$userName)
    					->where('password','=',$password)
    					->first();
        
        if($admin != null){
            $request->session()->put('admin', $admin);
            
            $session = session('admin');

            return redirect(route('dashboard'))
            ->with('success','You successfully Logged in');
        }
        else{
            return back()
            ->with('error','You have entered wrong username or password.');
        }
    }

    
    function logout(Request $request) {
        $request->session()->flush();
        return redirect(Route('adminLogin'));
    }
}
