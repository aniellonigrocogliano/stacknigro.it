<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CaptchaSite extends Model
{
    protected $table = 'captcha_sites';

    protected $fillable = [
        'name',
        'domain',
        'domains_extra',
        'is_active',
        'rate_limit_5m',
        'rate_limit_day',
        'notes',
        'last_used_at',
        'last_ip',
        // site_key e secret NON li metto fillable: li gestiamo noi nel controller
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    // Non far uscire mai la secret se accidentalmente fai ->toArray()
    protected $hidden = [
        'secret',
    ];

    /**
     * domains_extra in DB è TEXT con JSON string (es: ["www.sito.it","staging.sito.it"])
     * Qui lo gestiamo come array.
     */
    public function getDomainsExtraAttribute($value): array
    {
        if (!$value) return [];
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function setDomainsExtraAttribute($value): void
    {
        // Accetta: array oppure stringa con righe
        if (is_string($value)) {
            $value = preg_split("/\\r\\n|\\r|\\n/", $value) ?: [];
        }

        if (is_array($value)) {
            $value = array_values(array_filter(array_map(function ($d) {
                $d = trim((string)$d);
                $d = preg_replace('#^https?://#i', '', $d);
                $d = preg_replace('#/$#', '', $d);
                return $d !== '' ? strtolower($d) : null;
            }, $value)));

            $this->attributes['domains_extra'] = $value ? json_encode($value, JSON_UNESCAPED_SLASHES) : null;
            return;
        }

        $this->attributes['domains_extra'] = null;
    }

    /**
     * Genera site_key e secret (usato dal controller).
     */
    public static function generateSiteKey(): string
    {
        return 'sn_' . Str::lower(Str::random(32));
    }

    public static function generateSecret(): string
    {
        // più lungo = meglio
        return Str::random(80);
    }
}
