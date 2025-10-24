<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestCache extends Model
{
    protected $fillable = [
        'token',
        'fingerprint',
        'content',
    ];
}
