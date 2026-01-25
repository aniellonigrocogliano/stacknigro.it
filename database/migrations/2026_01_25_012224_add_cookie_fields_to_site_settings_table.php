<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            // Banner
            $table->boolean('cookie_banner_enabled')->default(true)->after('favicon_path');
            $table->unsignedSmallInteger('cookie_consent_days')->default(180)->after('cookie_banner_enabled');

            // Modal/Banner contenuto (se lo vuoi in TinyMCE lo gestiamo qui)
            $table->longText('cookie_banner_html')->nullable()->after('cookie_consent_days');

            // Analytics (in futuro)
            $table->string('analytics_provider', 30)->nullable()->after('cookie_banner_html'); // es: google_analytics
            $table->string('analytics_measurement_id', 50)->nullable()->after('analytics_provider'); // es: G-XXXX
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'cookie_banner_enabled',
                'cookie_consent_days',
                'cookie_banner_html',
                'analytics_provider',
                'analytics_measurement_id',
            ]);
        });
    }
};
