<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('quote_levels', function (Blueprint $table) {
      $table->id();

      // es: 1..10 (fisso, ma puoi cambiare nome e attivo)
      $table->unsignedTinyInteger('level')->unique();

      $table->string('name')->default('');
      $table->boolean('is_active')->default(false);

      // se vuoi un ordine diverso dal numero livello
      $table->unsignedSmallInteger('sort_order')->default(0);

      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('quote_levels');
  }
};
