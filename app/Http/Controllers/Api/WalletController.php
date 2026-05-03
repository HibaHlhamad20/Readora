<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChargingRequest;
//use Illuminate\Container\Attributes\DB;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function sendChargingRequest(Request $request){
        $request->validate([
            'amount'=>'required|numeric|min:1000',
            'receipt_image'=>'required|image|mimes:jpg,jpeg,png|max:2048',

        ]);

        $imagepath=$request->file('receipt_image')->store('receipts','public');
        $chargingRequest=ChargingRequest::create([
            'user_id'=>$request->user()->id,
            'amount'=>$request->amount,
            'receipt_image'=>$imagepath,
            'status'=>'pending',
        ]);
        return response()->json([
            'message'=>'تم ارسال الطلب بنجاح ، بانتظار موافقة الادمن',
            'data'=>$chargingRequest
        ],201);

    }

    public function getPendingRequet(){
        $request=ChargingRequest::with('user:id,name,email')->where('status','pending')->get();
        return response()->json([
            'success'=>true,
            'data'=>$request,

        ]);
    }


    public function approveRequest(int $id){
        try{
            DB::beginTransaction();//هاد المفوم يعني يا التنين بتمو سوا يا ما بينضاف الرصيد
            $chargingRequest=ChargingRequest::findOrFail($id);
            if($chargingRequest->status !=='pending'){
                return response()->json([
                    'message'=>'هذا الطلب تمت معالجته مسبقا '
                ],400);
            }
            $chargingRequest->update([
                'status'=>'approved'
            ]);
            $user=$chargingRequest->user;
            $user->increment('wallet',$chargingRequest->amount);
            DB::commit();//هون يعني بقول للداتابيز اعتمدي التغييرات يلي صارت
            return response()->json([
                'message'=>'تمت الموافقة وشحن الرصيد بنجاح',
                'wallet'=>$user->wallet
            ]);
        }
        catch(\Exception $e){
            DB::rollback();
            return response()->json([
                'message'=>'حدث خطأ ما :' . $e->getMessage()
            ],500);
        }

    }

    public function rejectRequest (Request $request, int $id){
        $request->validate([
            'rejection_reason'=>'required|string|min:5'
        ]);
        $chargingRequest=ChargingRequest::findOrFail($id);
        if($chargingRequest->status !== 'pending'){
            return response()->json([
                'message'=>'هذا الطلب تمت معالجته مسبقا'
            ],400);
        }
        $chargingRequest->update([
            'status'=>'rejected',
            'rejection_reason'=>$request->rejection_reason,
        ]);
        return response()->json([
            'message'=>'تم رفض الطلب',
            'reason'=>$request->rejection_reason,
        ]);
    }


}
