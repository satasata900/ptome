<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    protected $table = 'services';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $connection = 'mysql2';

    protected $guarded = [];

    public $dates = ['creationTime'];

}

