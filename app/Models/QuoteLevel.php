<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class QuoteLevel extends Model
{
    protected $table = 'quote_levels';

    protected $fillable = [
        'level',
        'name',
        'sort_order',
        'selection_type',
        'min_select',
        'max_select',
        'is_active',
    ];

    protected $casts = [
        'level' => 'integer',
        'sort_order' => 'integer',
        'min_select' => 'integer',
        'max_select' => 'integer',
        'is_active' => 'boolean',
    ];

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(QuoteOption::class, 'quote_level_option', 'quote_level_id', 'quote_option_id')
            ->withPivot(['is_required', 'is_hidden_by_default', 'sort_order'])
            ->withTimestamps();
    }
}
