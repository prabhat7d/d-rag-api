<?php

namespace App\Domain\Document\Services;

use App\Domain\Document\Models\DocumentChunk;

class DocumentSearchService
{
    public function __construct(
        private readonly EmbeddingService $embeddingService,
        private readonly PineconeService $pineconeService
    ) {}

    public function search(string $query, int $topK = 5, float $threshold = 0.40): array
    {
        $vector = $this->embeddingService->generate($query);

        $response = $this->pineconeService->search(
            vector: $vector,
            topK: $topK
        );

        $matches = $response['matches'] ?? [];

        $matches = collect($matches)
            ->filter(function (array $match) use ($threshold) {
                return ($match['score'] ?? 0) >= $threshold;
            })
            ->values()
            ->all();

        return collect($matches)
            ->map(function (array $match) {
                $chunkId = $match['metadata']['chunk_id'] ?? null;

                $chunk = DocumentChunk::find($chunkId);

                if (!$chunk) {
                    return null;
                }

                return [
                    'score' => $match['score'] ?? null,
                    'chunk_id' => $chunk->id,
                    'chunk_index' => $chunk->chunk_index,
                    'document_id' => $chunk->document_id,
                    'document_name' => $chunk->document->name,
                    'content' => $chunk->content,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
