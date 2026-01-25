<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InboxConversation;

class InboxConversationSeeder extends Seeder
{
    public function run(): void
    {
        // 2 contatti: 1 non letto, 1 letto
        InboxConversation::factory()->contact()->unread()->create([
            'name' => 'Mario Rossi',
            'email' => 'mario.rossi@example.com',
            'subject' => 'Info su collaborazione',
            'user_message' => "Ciao Aniello,\nmi piacerebbe avere info su tempi e costi.\nGrazie!",
            'how_found' => 'google',
        ]);

        InboxConversation::factory()->contact()->read()->create([
            'name' => 'Giulia Bianchi',
            'email' => 'giulia.bianchi@example.com',
            'subject' => 'Domanda sul portfolio',
            'user_message' => "Ciao,\nho visto i tuoi lavori, possiamo sentirci?\n",
            'how_found' => 'social',
        ]);

        // 2 preventivi: 1 archiviato, 1 risposto (chiuso)
        InboxConversation::factory()->quote()->archived()->create([
            'name' => 'Studio Alfa',
            'email' => 'studioalfa@example.com',
            'user_message' => "Note: vorremmo una landing + blog. Budget flessibile.",
            'how_found' => 'passaparola',
        ]);

        InboxConversation::factory()->quote()->replied()->create([
            'name' => 'Luca Verdi',
            'email' => 'luca.verdi@example.com',
            'user_message' => "Note: sito vetrina con 5 pagine, contatti e SEO base.",
            'how_found' => 'altro',
            'reply_to_email' => 'luca.verdi@example.com',
        ]);
    }
}
