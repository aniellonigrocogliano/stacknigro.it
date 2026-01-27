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
    Schema::create('ga_snapshots', function (Blueprint $table) {
        $table->id();
        $table->string('key')->unique();      // es: dashboard
        $table->json('payload');
        $table->timestamp('fetched_at');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ga_snapshots');
    }
};
