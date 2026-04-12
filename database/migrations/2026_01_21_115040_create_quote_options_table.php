<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('quote_level_option', function (Blueprint $table) {
      $table->id();

      $table->foreignId('quote_level_id')->constrained('quote_levels')->cascadeOnDelete();
      $table->foreignId('quote_option_id')->constrained('quote_options')->cascadeOnDelete();

      $table->boolean('is_required')->default(false);
      $table->boolean('is_hidden_by_default')->default(false);

      $table->unsignedSmallInteger('sort_order')->default(0);

      $table->timestamps();

      $table->unique(['quote_level_id', 'quote_option_id']);
      $table->index(['quote_level_id', 'sort_order']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('quote_level_option');
  }
};
