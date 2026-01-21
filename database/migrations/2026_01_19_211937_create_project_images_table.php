<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_project_images_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('project_images', function (Blueprint $table) {
      $table->id();
      $table->foreignId('project_id')->constrained()->cascadeOnDelete();
      $table->string('path');                 // storage path
      $table->string('alt')->nullable();
      $table->unsignedInteger('sort_order')->default(0);
      $table->boolean('is_cover')->default(false);
      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('project_images');
  }
};
