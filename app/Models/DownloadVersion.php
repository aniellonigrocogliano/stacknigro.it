<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadVersion extends Model
{
    protected $table = 'download_versions';

    protected $fillable = [
        'download_id',
        'version',
        'changelog',
        'released_at',
        'is_active',
        'is_latest',
    ];

    protected $casts = [
        'download_id' => 'integer',
        'released_at' => 'datetime',
        'is_active'   => 'boolean',
        'is_latest'   => 'boolean',
    ];

    public function download()
    {
        return $this->belongsTo(Download::class, 'download_id');
    }

    public function assets()
    {
        return $this->hasMany(DownloadAsset::class, 'version_id');
    }
}
