<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('quote_options', function (Blueprint $table) {
      $table->id();

      $table->string('label'); // es: "Pagamento online"
      $table->text('description')->nullable();

      // range ore e prezzo (lasciamo integer per semplicità)
      $table->unsignedInteger('hours_min')->nullable();
      $table->unsignedInteger('hours_max')->nullable();

      // prezzo in centesimi per evitare float
      $table->unsignedInteger('price_min_cents')->nullable();
      $table->unsignedInteger('price_max_cents')->nullable();

      $table->boolean('is_active')->default(true);
      $table->boolean('is_default')->default(false);

      $table->unsignedSmallInteger('sort_order')->default(0);

      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('quote_options');
  }
};
