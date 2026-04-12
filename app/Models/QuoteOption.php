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
        'sort_order', // Aggiunto se presente in DB per l'ordinamento globale
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'hours' => 'integer',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relazione con i Livelli (Step)
     */
    public function levels(): BelongsToMany
    {
        return $this->belongsToMany(QuoteLevel::class, 'quote_level_option', 'quote_option_id', 'quote_level_id')
            ->withPivot(['is_required', 'is_hidden_by_default', 'sort_order'])
            ->withTimestamps();
    }

    /**
     * Regole scatenate da questa opzione
     */
    public function triggerRules(): HasMany
    {
        return $this->hasMany(QuoteRule::class, 'trigger_option_id');
    }

    // --- MODIFICHE AGGIUNTE DA QUI ---

    /**
     * Regole che hanno come obiettivo questa specifica opzione
     */
    public function targetRules(): HasMany
    {
        return $this->hasMany(QuoteRule::class, 'target_option_id');
    }

    /**
     * Relazione con i Pacchetti (Offerte)
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(QuotePackage::class, 'quote_package_option', 'quote_option_id', 'quote_package_id');
    }
}
