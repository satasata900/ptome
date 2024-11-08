<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Field extends Model
{
    protected $table = 'fields';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $connection = 'mysql2';
}