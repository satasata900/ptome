<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    protected $table = 'countries';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $connection = 'mysql2';
}
