<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController
{
    public function login(){
        return view('login');
    }
    public function doLogin(LoginRequest $request){
        $user = User::where('mobile',$request->mobile)->first();
        if(filled($user) && Hash::check($request->validated('pin'), $user->pin)){
            auth()->login($user);
            return redirect()->route('dashboard');
        }
        return redirect()->route('login');
    }


    public function logout(){
        auth()->logout();
        return redirect()->route('login');
    }
}
