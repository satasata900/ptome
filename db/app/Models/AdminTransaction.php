<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminTransaction extends Model
{
    protected $table = 'admin_transactions';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = [];
    protected $connection = 'mysql2';
}
