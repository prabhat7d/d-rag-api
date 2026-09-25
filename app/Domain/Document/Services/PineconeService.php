<?php

namespace App\Domain\Document\Services;

use Illuminate\Support\Facades\Http;

class PineconeService
{
    public function upsert(
        string $id,
        array $vector,
        array $metadata = []
    ): void {
        Http::withHeaders([
            'Api-Key' => config('services.pinecone.api_key'),
            'Content-Type' => 'application/json',
        ])->post(
            config('services.pinecone.host') . '/vectors/upsert',
            [
                'vectors' => [
                    [
                        'id' => $id,
                        'values' => $vector,
                        'metadata' => $metadata,
                    ],
                ],
            ]
        )->throw();
    }

    public function search(array $vector, int $topK = 5): array
    {
        return Http::withHeaders([
            'Api-Key' => config('services.pinecone.api_key'),
            'Content-Type' => 'application/json',
        ])->post(
            config('services.pinecone.host') . '/query',
            [
                'vector' => $vector,
                'topK' => $topK,
                'includeMetadata' => true,
            ]
        )->throw()->json();
    }
}
