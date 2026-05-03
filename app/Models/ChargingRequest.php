<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargingRequest extends Model
{
      use HasFactory;
      protected $fillable = [
        'user_id',
        'amount',
        'receipt_image',
        'status',
        'rejection_reason'
      ];
      public function user(){
        return $this->belongsTo(User::class);
      }

}
