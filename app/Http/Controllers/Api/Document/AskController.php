<?php

namespace App\Http\Controllers\Api\Document;

use App\Domain\Document\Services\DocumentSearchService;
use App\Domain\Document\Services\RagService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Document\AskRequest;

class AskController extends Controller
{
    public function __construct(
        private readonly DocumentSearchService $searchService,
        private readonly RagService $ragService
    ) {}

    public function __invoke(AskRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $query = $validated['query'];
        $topK = $validated['top_k'] ?? 5;

        $searchResults = $this->searchService->search(
            query: $query,
            topK: $topK
        );

        if (empty($searchResults)) {
            return response()->json([
                'answer' => 'I could not find relevant information in the uploaded documents.',
                'sources' => [],
            ]);
        }

        $matches = $searchResults ?? [];

        $answer = $this->ragService->generate(
            question: $query,
            matches: $matches
        );

        $sources = collect($matches)
            ->map(function (array $match) {
                return [
                    'score' => $match['score'] ?? null,
                    'document_id' => $match['document_id'] ?? null,
                    'document_name' => $match['document_name'],
                    'chunk_id' => $match['chunk_id'] ?? null,
                    'chunk_index' => $match['chunk_index'] ?? null,
                ];
            })
            ->values()
            ->all();

        return response()->json([
            'answer' => $answer,
            'sources' => $sources,
        ]);
    }
}
