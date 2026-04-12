<?php

// app/Models/Project.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
  protected $fillable = ['title','slug','excerpt','body','is_published','sort_order'];

  public function images(): HasMany
  {
    return $this->hasMany(ProjectImage::class)->orderBy('is_cover', 'desc')->orderBy('sort_order');
  }

  public function cover()
  {
    return $this->hasOne(ProjectImage::class)->where('is_cover', true);
  }
}
