<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up(): void
{
    Schema::create('skills', function (Blueprint $table) {
        $table->id();
        $table->string('name', 120);
        $table->string('color', 30)->nullable();     // es: #00c897
        $table->string('fa_icon', 80)->nullable();   // es: fa-solid fa-code
        $table->text('description')->nullable();
        $table->unsignedInteger('sort')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
