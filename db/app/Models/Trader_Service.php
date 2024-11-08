<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
class Trader_Service extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'traders_services';
    protected $primaryKey = 'id';
    protected $connection = 'mysql2';

    protected $guarded = [];
    public $dates = ['creationTime'];

}
