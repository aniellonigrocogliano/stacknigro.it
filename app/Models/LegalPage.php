<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalPage extends Model
{
    protected $fillable = [
        'type',
        'title',
        'content',
    ];

    // TinyMCE salva HTML: nessun cast speciale necessario
}
