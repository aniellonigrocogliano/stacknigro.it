<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inbox_conversations', function (Blueprint $table) {
            $table->id();

            // origine
            $table->string('source', 20)->index(); // contact | quote

            // dati utente (campi del form contatto / preventivo)
            $table->string('name', 120);
            $table->string('email', 190)->index();
            $table->string('phone', 50)->nullable();

            $table->string('subject', 180)->nullable();      // Oggetto (solo contatto, ma nullable)
            $table->longText('user_message');               // Messaggio (o note preventivo)

            $table->string('how_found', 20)->nullable()->index(); // google|social|passaparola|altro
            $table->boolean('privacy_accepted')->default(false);
            $table->timestamp('privacy_accepted_at')->nullable();

            // preventivo: risultato calcolo (JSON)
            $table->json('quote_payload')->nullable();

            // log tecnico
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            // stato inbox (gmail style)
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamp('archived_at')->nullable()->index();

            // reply admin (chiude la conversazione)
            $table->timestamp('replied_at')->nullable()->index();
            $table->string('reply_subject', 180)->nullable();
            $table->longText('reply_body')->nullable();
            $table->string('reply_to_email', 190)->nullable();

            // cestino
            $table->softDeletes();
            $table->timestamps();

            $table->index(['source', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbox_conversations');
    }
};
