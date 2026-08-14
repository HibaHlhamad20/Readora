<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
     use HasFactory;

     protected $fillable = [
        'book_id',
        'question_text',
        'options',
        'correct_answer'
     ];

     protected $casts = [
        'options'=>'array',
     ];

     public function book(){
        return $this->belongsTo(Book::class);
     }
}
