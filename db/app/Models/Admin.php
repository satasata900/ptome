<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use Notifiable, HasRoles;
    use SoftDeletes;

    protected $guard_name = 'web';

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $connection = 'mysql';

    protected $hidden = [
        'password'
    ];

    protected $fillable = ['avatar'];

}
