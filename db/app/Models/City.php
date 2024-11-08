<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    protected $table = 'cities';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $connection = 'mysql2';
}
