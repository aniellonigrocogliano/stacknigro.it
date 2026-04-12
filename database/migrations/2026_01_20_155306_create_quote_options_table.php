<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('quote_options', function (Blueprint $table) {
      $table->id();

      $table->string('label');
      $table->text('help_text')->nullable();

      // range ore / prezzi dell'opzione (min/max)
      $table->unsignedInteger('hours_min')->nullable();
      $table->unsignedInteger('hours_max')->nullable();

      $table->unsignedInteger('price_min')->nullable(); // euro interi
      $table->unsignedInteger('price_max')->nullable();

      $table->boolean('is_active')->default(true);

      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('quote_options');
  }
};

