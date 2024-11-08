<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWallet extends Model
{
    use HasFactory;

    protected $table = 'users_wallets';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $connection = 'mysql2';

}
