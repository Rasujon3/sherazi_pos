<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    protected $table = 'user_types';
    protected $fillable = [
        'name',
        'role',
        'project_id',
        'is_showing',
    ];
}
