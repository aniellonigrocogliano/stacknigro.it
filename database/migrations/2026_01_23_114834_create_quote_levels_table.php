<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('quote_levels', function (Blueprint $table) {
      $table->id();

      $table->unsignedTinyInteger('level');         // 1..10 (fisso)
      $table->string('name');
      $table->unsignedTinyInteger('sort_order')->default(0);

      // comportamento selezione (admin configurabile)
      $table->enum('selection_type', ['single', 'multi'])->default('single');
      $table->unsignedTinyInteger('min_select')->default(0);
      $table->unsignedTinyInteger('max_select')->nullable();

      $table->boolean('is_active')->default(true);

      $table->timestamps();

      $table->unique('level');
      $table->index(['is_active', 'sort_order']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('quote_levels');
  }
};

