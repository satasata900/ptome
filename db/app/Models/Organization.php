<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\User;
class Organization extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'organizations';
    protected $primaryKey = 'id';
    protected $connection = 'mysql2';

    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

}
