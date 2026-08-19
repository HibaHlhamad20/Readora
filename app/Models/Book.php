<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $guarded = [];
    
    public function authors ()
    {
        return $this->belongsToMany(Author::class,'author_book');
    }

    public function categories ()
    {
        return $this->belongsToMany(Category::class,'category_book');
    }

    public function favouriteByUser(){
        return $this->belongsToMany(User::class,'favourites');
        }

    public function purchases(){
        return $this->hasMany(Purchase::class);
    }
    
    public function borrowings(){
        return $this->hasMany(Borrowing::class);
    }

    public function questions(){
        return $this->hasMany(Question::class);
    }

    public function completeByUsers(){
        return $this->belongsToMany(User::class,'book_user')->withPivot('points','created_at');
    }


    public function comments(){
        return $this->hasMany(Comment::class);
    }

}
