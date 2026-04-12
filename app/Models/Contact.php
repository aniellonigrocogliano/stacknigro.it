<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'fa_icon',
        'value',        // testo visibile all’utente
        'href',         // link reale (mailto:, tel:, https://, wa.me...)
        'target_blank', // 0/1
        'sort',
    ];

    protected $casts = [
        'target_blank' => 'boolean',
        'sort' => 'integer',
    ];
}
