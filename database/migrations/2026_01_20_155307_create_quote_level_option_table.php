<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('quote_level_option', function (Blueprint $table) {
      $table->id();

      $table->foreignId('quote_level_id')->constrained('quote_levels')->cascadeOnDelete();
      $table->foreignId('quote_option_id')->constrained('quote_options')->cascadeOnDelete();

      $table->unsignedInteger('sort_order')->default(0);
      $table->boolean('is_active')->default(true);

      $table->unique(['quote_level_id','quote_option_id']); // evita doppioni
      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('quote_level_option');
  }
};
