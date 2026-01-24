<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('quote_level_option', function (Blueprint $table) {
      $table->id();

      $table->foreignId('quote_level_id')
        ->constrained('quote_levels')
        ->cascadeOnDelete();

      $table->foreignId('quote_option_id')
        ->constrained('quote_options')
        ->cascadeOnDelete();

      // obbligo STATICO nel livello (sempre vero)
      $table->boolean('is_required')->default(false);

      // se vuoi tenerle nascoste finché non scatta una regola show_option
      $table->boolean('is_hidden_by_default')->default(false);

      $table->unsignedTinyInteger('sort_order')->default(0);

      $table->timestamps();

      $table->unique(['quote_level_id', 'quote_option_id'], 'quote_level_option_unique');
      $table->index(['quote_level_id', 'sort_order'], 'quote_level_option_sort_idx');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('quote_level_option');
  }
};

