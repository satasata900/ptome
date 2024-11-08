<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends Model
{
    protected $table = 'providers';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $connection = 'mysql2';

    public $dates = ['registeredAt'];
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

}

