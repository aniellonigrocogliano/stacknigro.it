<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $table = 'downloads';

    protected $fillable = [
        'suite_id',
        'title',
        'slug',
        'description',
        'type',
        'platform',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'suite_id'   => 'integer',
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function suite()
    {
        return $this->belongsTo(Suite::class, 'suite_id');
    }

    public function languages()
    {
        return $this->belongsToMany(Language::class, 'download_language', 'download_id', 'language_id')
            ->withTimestamps();
    }

    public function versions()
    {
        return $this->hasMany(DownloadVersion::class, 'download_id');
    }

    public function latestVersion()
    {
        return $this->hasOne(DownloadVersion::class, 'download_id')
            ->where('is_latest', 1);
    }
}
