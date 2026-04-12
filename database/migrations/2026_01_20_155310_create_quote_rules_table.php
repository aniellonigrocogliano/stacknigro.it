<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('quote_rules', function (Blueprint $table) {
      $table->id();

      // Trigger: se è selezionata questa opzione (in quel livello)
      $table->foreignId('trigger_level_id')->constrained('quote_levels')->cascadeOnDelete();
      $table->foreignId('trigger_option_id')->constrained('quote_options')->cascadeOnDelete();

      // Azione
      $table->enum('action_type', [
        'show_level', 'hide_level',
        'require_option', 'auto_select_option',
        'add_hours', 'add_price',
        'set_hours', 'set_price',
      ]);

      // Target (dipende dal tipo azione)
      $table->foreignId('target_level_id')->nullable()->constrained('quote_levels')->nullOnDelete();
      $table->foreignId('target_option_id')->nullable()->constrained('quote_options')->nullOnDelete();

      // Valori (per ore/prezzo)
      $table->integer('value_min')->nullable();
      $table->integer('value_max')->nullable();

      $table->boolean('is_active')->default(true);
      $table->unsignedInteger('sort_order')->default(0);

      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('quote_rules');
  }
};

