<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('quote_rules', function (Blueprint $table) {
      $table->id();

      // SE selezioni questa opzione...
      $table->foreignId('trigger_option_id')
        ->constrained('quote_options')
        ->cascadeOnDelete();

      // ...ALLORA fai questa azione
      $table->enum('action_type', [
        'show_level',
        'hide_level',
        'show_option',
        'hide_option',
        'require_option',
      ]);

      // target (uno dei due in base all'action_type)
      $table->foreignId('target_level_id')
        ->nullable()
        ->constrained('quote_levels')
        ->cascadeOnDelete();

      $table->foreignId('target_option_id')
        ->nullable()
        ->constrained('quote_options')
        ->cascadeOnDelete();

      $table->unsignedTinyInteger('sort_order')->default(0);

      $table->timestamps();

      $table->index(['trigger_option_id', 'sort_order'], 'quote_rules_trigger_sort_idx');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('quote_rules');
  }
};
