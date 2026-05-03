<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();
        
        $token=$request->user()->createToken('auth_token')->plainTextToken;

        //$request->session()->regenerate();

        return response()->json([
            'message'=>'success',
            'acceses token'=>$token,
            'user'=>$request->user(),
        ]);
        //->noContent();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
//3|7rgyjhIAwGBZFBky02cC6YhkVwdUkDE29OSjvP7qb56848e7//admin
