<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requests extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'created_user',
        'assigned_user',
        'expiration_date',
    ];
}
