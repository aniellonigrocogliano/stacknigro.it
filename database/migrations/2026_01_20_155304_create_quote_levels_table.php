<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('quote_levels', function (Blueprint $table) {
      $table->id();

      $table->unsignedInteger('level')->default(1); // 1,2,3...
      $table->string('title');                      // nome del gruppo
      $table->enum('selection_type', ['single','multi'])->default('multi');

      $table->boolean('is_required')->default(false); // obbligatorio selezionare almeno una?
      $table->boolean('is_active')->default(true);

      $table->unsignedInteger('sort_order')->default(0);

      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('quote_levels');
  }
};

