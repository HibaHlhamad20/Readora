<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];
    
    // public function authors ()
    // {
    //     return $this->belongsToMany(Author::class,'category_author');
    // }

//     public function books ()
//     {
//         return $this->belongsToMany(Book::class,'category_book');
//     }
public function users(){
    return $this->belongsToMany(User::class);
}


 }
