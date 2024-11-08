<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
class Transaction extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $connection = 'mysql2';

    protected $guarded = [];
    public $dates = ['creationTime'];

   // protected $casts = [
   //      'creationTime'  => 'date:Y-m-d',
   //  ];
}
