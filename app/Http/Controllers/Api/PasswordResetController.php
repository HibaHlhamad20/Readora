<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    
    public function sendOtp(Request $request)
    {
        
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

       
        $otp = rand(100000, 999999);

        
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $otp, 
                'created_at' => now()
            ]
        );

     
        Mail::to($request->email)->send(new OtpMail($otp));

        return response()->json(['message' => 'تم إرسال الرمز بنجاح إلى بريدك الإلكتروني']);
    }

   
    public function resetPassword(Request $request)
    {
       
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric',
            'password' => 'required|min:8|confirmed', 
        ]);

        
        $resetData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        
        if (!$resetData || Carbon::parse($resetData->created_at)->addMinutes(15)->isPast()) {
            return response()->json(['message' => 'الرمز غير صحيح أو انتهت صلاحيته!'], 422);
        }

        
        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'تم تغيير كلمة السر بنجاح، يمكنك تسجيل الدخول الآن.']);
    }
}
