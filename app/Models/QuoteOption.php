<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteOption extends Model
{
  protected $fillable = [
    'label','help_text','hours_min','hours_max','price_min','price_max','is_active'
  ];

  protected $casts = [
    'is_active' => 'boolean',
  ];

  public function levels(): BelongsToMany
  {
    return $this->belongsToMany(QuoteLevel::class, 'quote_level_option')
      ->withPivot(['sort_order','is_active'])
      ->withTimestamps();
  }

  public function rulesAsTrigger(): HasMany
  {
    return $this->hasMany(QuoteRule::class, 'trigger_option_id');
  }
}
