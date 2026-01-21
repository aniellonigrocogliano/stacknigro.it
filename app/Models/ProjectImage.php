<?php

// app/Models/ProjectImage.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectImage extends Model
{
  protected $fillable = ['project_id','path','alt','sort_order','is_cover'];

  public function project(): BelongsTo
  {
    return $this->belongsTo(Project::class);
  }
}
