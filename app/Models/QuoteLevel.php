<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteLevel extends Model
{
  protected $fillable = [
    'level','title','selection_type','is_required','is_active','sort_order'
  ];

  protected $casts = [
    'is_required' => 'boolean',
    'is_active' => 'boolean',
  ];

  public function options(): BelongsToMany
  {
    return $this->belongsToMany(QuoteOption::class, 'quote_level_option')
      ->withPivot(['sort_order','is_active'])
      ->withTimestamps()
      ->orderBy('quote_level_option.sort_order')
      ->orderBy('quote_level_option.id');
  }

  public function rulesAsTrigger(): HasMany
  {
    return $this->hasMany(QuoteRule::class, 'trigger_level_id');
  }
}
