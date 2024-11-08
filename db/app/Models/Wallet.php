<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Wallet extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'wallets';
    protected $primaryKey = 'id';
    protected $connection = 'mysql2';
    public $timestamps = false;

    protected $fillable = ['wallet_name','wallet_currency','default_wallet','price','test_wallet'];

    public function users(){
        return $this->belongsToMany(User::class,'users_wallets');
    }

}
