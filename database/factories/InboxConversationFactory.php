<?php

namespace Database\Factories;

use App\Models\InboxConversation;
use Illuminate\Database\Eloquent\Factories\Factory;

class InboxConversationFactory extends Factory
{
    protected $model = InboxConversation::class;

    public function definition(): array
    {
        $source = $this->faker->randomElement(['contact', 'quote']);

        return [
            'source' => $source,

            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->boolean(40) ? $this->faker->phoneNumber() : null,

            'subject' => $source === 'contact'
                ? $this->faker->sentence(5)
                : 'Richiesta preventivo sito',

            'user_message' => $source === 'contact'
                ? $this->faker->paragraph(3)
                : ($this->faker->boolean(60) ? $this->faker->sentence(10) : 'Nessuna nota aggiuntiva.'),

            'how_found' => $this->faker->boolean(70)
                ? $this->faker->randomElement(['google', 'social', 'passaparola', 'altro'])
                : null,

            'privacy_accepted' => true,
            'privacy_accepted_at' => now()->subDays(rand(0, 30)),

            'quote_payload' => $source === 'quote'
                ? [
                    'total' => $this->faker->randomElement([399, 699, 999, 1299]),
                    'currency' => 'EUR',
                    'level' => $this->faker->randomElement(['base', 'pro', 'premium']),
                    'options' => [
                        ['key' => 'seo', 'label' => 'SEO base', 'price' => 150],
                        ['key' => 'blog', 'label' => 'Blog', 'price' => 200],
                    ],
                    'notes' => 'Payload demo preventivo',
                ]
                : null,

            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),

            'read_at' => null,
            'archived_at' => null,

            'replied_at' => null,
            'reply_subject' => null,
            'reply_body' => null,
            'reply_to_email' => null,
        ];
    }

    public function contact(): self
    {
        return $this->state(fn () => [
            'source' => 'contact',
            'quote_payload' => null,
        ]);
    }

    public function quote(): self
    {
        return $this->state(fn () => [
            'source' => 'quote',
            'subject' => 'Richiesta preventivo sito',
            'quote_payload' => [
                'total' => 899,
                'currency' => 'EUR',
                'level' => 'pro',
                'options' => [
                    ['key' => 'seo', 'label' => 'SEO base', 'price' => 150],
                    ['key' => 'analytics', 'label' => 'Analytics', 'price' => 80],
                ],
            ],
        ]);
    }

    public function unread(): self
    {
        return $this->state(fn () => ['read_at' => null]);
    }

    public function read(): self
    {
        return $this->state(fn () => ['read_at' => now()->subHours(rand(1, 72))]);
    }

    public function archived(): self
    {
        return $this->state(fn () => ['archived_at' => now()->subDays(rand(1, 20))]);
    }

    public function replied(): self
    {
        return $this->state(fn () => [
            'replied_at' => now()->subDays(rand(0, 10)),
            'reply_subject' => 'Re: aggiornamento richiesta',
            'reply_body' => "Ciao! Ti ringrazio per il messaggio.\n\nQui la mia risposta di test.\n\nA presto,\nAniello",
            'reply_to_email' => $this->faker->safeEmail(),
            'read_at' => now()->subDays(rand(0, 10)),
        ]);
    }
}
