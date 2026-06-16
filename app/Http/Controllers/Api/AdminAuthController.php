<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\user;

class AdminAuthController extends Controller
{
    public function login(Request $request){
        $request->validate([
            'email'=>'required|string|email',
            'password'=>'required|string|min:8'
        ]);
        if(!\Auth::attempt($request->only('email','password'))){
            return response()->json([
                'message'=>"البيانات غير صحيحة",
            ],401);
        }
        $user=Auth::user();
        if($user->role !=='admin'){
            Auth::logout();
            return response()->json([
                'message'=>"عذرا انت لا تملك صلاحية الدخول كادمن"
            ],403);
        }
        $token=$request->user()->createToken('auth_token')->plainTextToken;
        return response()->json([
            'mesagge'=>"تم تسجيل الدخول",
            'token'=>$token,
            'admin'=>$user
        ],200);
    }
    
}
