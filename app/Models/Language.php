<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'languages';

    protected $fillable = [
        'key',
        'label',
        'fa_icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function downloads()
    {
        return $this->belongsToMany(Download::class, 'download_language', 'language_id', 'download_id')
            ->withTimestamps();
    }
}
