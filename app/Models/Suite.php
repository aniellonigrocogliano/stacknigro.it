<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suite extends Model
{
    protected $table = 'suites';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function downloads()
    {
        return $this->hasMany(Download::class, 'suite_id');
    }
}
