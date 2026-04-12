<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadLog extends Model
{
    protected $table = 'download_logs';

    public $timestamps = false;

    protected $fillable = [
        'asset_id',
        'ip_address',
        'user_agent',
        'referer',
        'created_at',
    ];

    protected $casts = [
        'asset_id'   => 'integer',
        'created_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(DownloadAsset::class, 'asset_id');
    }
}
