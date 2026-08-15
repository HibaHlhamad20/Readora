<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable,Notifiable,HasApiTokens;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
       // 'interests',
        'user_image',
        'wallet',
        'fcm_token'
    ];
    // protected $appends = ['level'];
    // public function getLevelAttribute(){
    //     $points=$this->points;
    //     if($points >=300){
    //         return 'diamond';
    //     }
    //     elseif($points >=200){
    //         return 'gold';
    //     }elseif($points >=100){
    //         return 'bronze';
    //     }else
    //     return null;
    // }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
           // 'interests'=>'array'
        ];
    }
    public function categories(){
        return $this->belongsToMany(Category::class);
    }
    public function favouriteBooks(){
        return $this->belongsToMany(Book::class,'favourites');
    }


    public function purchases(){
        return $this->hasMany(Purchase::class);
    }

    public function borrowings(){
        return $this->hasMany(Borrowing::class);
    }

    public function completedBooks()
{
    return $this->belongsToMany(Book::class,'book_user')->withTimestamps();
}
}
