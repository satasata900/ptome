<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactUs extends Model
{
    protected $table = 'user_tickets';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $connection = 'mysql2';

    public $dates = ['creationTime'];

    public function user(){
        return $this->belongsTo(User::class);
    }

}
