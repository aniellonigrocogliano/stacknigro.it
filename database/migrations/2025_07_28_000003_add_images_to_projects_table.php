<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('image_1')->nullable()->after('description');
            $table->string('image_2')->nullable()->after('image_1');
            $table->string('image_3')->nullable()->after('image_2');
            $table->string('image_4')->nullable()->after('image_3');
            $table->string('image_5')->nullable()->after('image_4');
        });
    }

    public function down(): void {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['image_1', 'image_2', 'image_3', 'image_4', 'image_5']);
        });
    }
};