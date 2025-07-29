<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['title', 'description', 'published_at'];

    public function images()
    {
        return $this->hasMany(ProjectImage::class);
    }
}