<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GaSnapshot extends Model
{
    protected $fillable = ['key','payload','fetched_at'];
    protected $casts = [
        'payload' => 'array',
        'fetched_at' => 'datetime',
    ];
}
