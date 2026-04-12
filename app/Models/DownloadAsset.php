<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadAsset extends Model
{
    protected $table = 'download_assets';

    protected $fillable = [
        'version_id',
        'format',
        'original_name',
        'stored_path',
        'mime_type',
        'size_bytes',
        'sha256',
        'downloads_count',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'version_id'      => 'integer',
        'size_bytes'      => 'integer',
        'downloads_count' => 'integer',
        'is_active'       => 'boolean',
        'sort_order'      => 'integer',
    ];

    public function version()
    {
        return $this->belongsTo(DownloadVersion::class, 'version_id');
    }

    public function logs()
    {
        return $this->hasMany(DownloadLog::class, 'asset_id');
    }
}
