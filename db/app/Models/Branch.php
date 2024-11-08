<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    protected $table = 'branches';
    protected $primaryKey = 'id';
    protected $connection = 'mysql2';
}
