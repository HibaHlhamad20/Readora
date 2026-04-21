<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed','min:8','max:20', Rules\Password::default()],
            'interests'=>['required','array'],
            'interests.*'=>['exists:categories,id'],
            'user_image'=>['nullable','image','mimes:jpg,jpeg,png','max:2048'],
        ]);

        $path=null;
        if($request->hasFile('user_image')){
            $path=$request->file('user_image')->store('Profiles','public');
        }
          

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
           // 'interests'=>$request->interests,
            'user_image'=>$path,
            'role'=>'reader',
            'points'=>0

        ]);

        $user->categories()->attach($request->interests);
        $user->load('categories');
        
        event(new Registered($user));

        Auth::login($user);

        $token=$request->user()->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message'=>'register successfully',
            'acceses token'=>$token,
            'user'=>$user
        ],201);
        //->noContent();
    }


    public function updateProfile(Request $request)
{
    $user = $request->user(); 

    
    $request->validate([
        'name' => ['sometimes', 'string', 'max:255'],
        'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        'interests' => ['sometimes', 'array'], 
        'user_image' => ['sometimes', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
    ]);

   
    $user->name = $request->name;
    $user->email = $request->email;
    
    if ($request->has('interests')) {
        $user->interests = $request->interests;
    }

    
    if ($request->hasFile('user_image')) {
        
        $path = $request->file('user_image')->store('profiles', 'public');
        $user->user_image = $path;
    }

    $user->save();

    return response()->json([
        'message' => 'تم تحديث الملف الشخصي بنجاح',
        'user' => $user
    ]);
}
}
