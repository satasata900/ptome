<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $connection = 'mysql2';
    public $dates = ['registration_Time'];

    public function wallets(){
        return $this->belongsToMany(Wallet::class,'users_wallets');
    }

    public function providers(){
        return $this->hasMany(Provider::class);
    }

    public function messages(){
        return $this->hasMany(ContactUs::class);
    }

}
