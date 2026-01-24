<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteRule extends Model
{
    protected $table = 'quote_rules';

    protected $fillable = [
        'trigger_option_id',
        'action_type',
        'target_level_id',
        'target_option_id',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

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
