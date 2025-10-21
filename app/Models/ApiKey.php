<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $fillable = [
        'email',
        'secret',
        'token',
        'last_seen_at',
        'enabled',
    ];

    protected $casts = [

    ];
}
