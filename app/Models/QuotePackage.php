<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class QuotePackage extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'custom_anchor_label',
        'promo_price',
        'real_value',
        'discount_percentage',
        'discount_type',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'promo_price' => 'float',
        'discount_percentage' => 'float',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Logica di avvio del modello
     */
    protected static function boot()
    {
        parent::boot();

        // Prima della creazione (INSERT)
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = static::generateUniqueSlug($model->name);
            }
        });

        // Prima dell'aggiornamento (UPDATE)
        static::updating(function ($model) {
            // Se lo slug è vuoto (magari cancellato per errore), lo rigeneriamo
            // Ma se esiste già, non lo tocchiamo per preservare i link esistenti
            if (empty($model->slug)) {
                $model->slug = static::generateUniqueSlug($model->name);
            }
        });
    }

    /**
     * Genera uno slug univoco per la tabella
     */
    private static function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        // Controlla se lo slug esiste già nel database
        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    /**
     * Usa lo 'slug' per il Route Model Binding.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Relazione Many-to-Many con le opzioni.
     */
    public function options(): BelongsToMany
    {
        return $this->belongsToMany(
            QuoteOption::class,
            'quote_package_option',
            'quote_package_id',
            'quote_option_id'
        );
    }

    /**
     * Accessor per il Valore Reale (somma prezzi opzioni).
     */
    public function getRealValueAttribute()
    {
        return $this->options->sum('price');
    }
}
