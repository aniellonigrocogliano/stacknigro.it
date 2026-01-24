<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteOption extends Model
{
    protected $table = 'quote_options';

    protected $fillable = [
        'name',
        'description',
        'price',
        'hours',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'hours' => 'integer',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function levels(): BelongsToMany
    {
        return $this->belongsToMany(QuoteLevel::class, 'quote_level_option', 'quote_option_id', 'quote_level_id')
            ->withPivot(['is_required', 'is_hidden_by_default', 'sort_order'])
            ->withTimestamps();
    }

    public function triggerRules(): HasMany
    {
        return $this->hasMany(QuoteRule::class, 'trigger_option_id');
    }
}
