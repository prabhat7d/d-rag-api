<?php

namespace App\Domain\Document\Services;

use OpenAI\Client;

class EmbeddingService
{
    public function __construct(
        private readonly Client $client
    ) {}

    public function generate(string $text): array
    {
        $response = $this->client->embeddings()->create([
            'model' => 'text-embedding-3-small',
            'input' => $text,
        ]);

        return $response->embeddings[0]->embedding;
    }
}
