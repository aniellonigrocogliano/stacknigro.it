<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteRule extends Model
{
  protected $fillable = [
    'trigger_level_id','trigger_option_id',
    'action_type','target_level_id','target_option_id',
    'value_min','value_max','is_active','sort_order'
  ];

  protected $casts = [
    'is_active' => 'boolean',
  ];

  public function triggerLevel(): BelongsTo
  {
    return $this->belongsTo(QuoteLevel::class, 'trigger_level_id');
  }

  public function triggerOption(): BelongsTo
  {
    return $this->belongsTo(QuoteOption::class, 'trigger_option_id');
  }

  public function targetLevel(): BelongsTo
  {
    return $this->belongsTo(QuoteLevel::class, 'target_level_id');
  }

  public function targetOption(): BelongsTo
  {
    return $this->belongsTo(QuoteOption::class, 'target_option_id');
  }
}
