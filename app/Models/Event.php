<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
   protected $guarded = []; 

   public function users () {
    return $this->belongsToMany(User::class,'event_user');
   }

   public function books () {
    return $this->belongsToMany(Book::class,'event_book','event_id','book_id');
   }

   public function participations(){
        return $this->hasMany(Participation::class);
   } 

}
