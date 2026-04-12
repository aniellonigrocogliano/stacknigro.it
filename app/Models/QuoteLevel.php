<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteLevel extends Model
{
    protected $table = 'quote_levels';

    protected $fillable = [
        'level',
        'name',           // <--- CORRETTO: Deve essere 'name' per far passare il dato!
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
        // Assicurati che questi nomi (is_required, is_hidden_by_default)
        // siano identici a quelli nella tua migrazione della tabella quote_level_option
        return $this->belongsToMany(QuoteOption::class, 'quote_level_option', 'quote_level_id', 'quote_option_id')
            ->withPivot(['is_required', 'is_hidden_by_default', 'sort_order'])
            ->withTimestamps();
    }

    public function targetRules(): HasMany
    {
        return $this->hasMany(QuoteRule::class, 'target_level_id');
    }
}
