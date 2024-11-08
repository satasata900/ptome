<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'app_settings';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $connection = 'mysql2';

    protected $guarded = [];
}
