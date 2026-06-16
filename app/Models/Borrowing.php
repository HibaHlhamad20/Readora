<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'book_id',
        'price',
        'discount_type',
        'borrowed_at',
        'expires_at',
        'status',
    ];
    protected $casts = [
        'borrowed_at'=>'datetime',
        'expires_at'=>'datetime',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function book(){
        return $this->belongsTo(Book::class);
    }
}
