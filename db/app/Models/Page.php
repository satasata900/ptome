<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    protected $table = 'pages';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $connection = 'mysql2';


}
