<?php

namespace App\Http\Controllers\Api\Document;

use App\Domain\Document\Services\DocumentSearchService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        private readonly DocumentSearchService $searchService
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => ['required', 'string', 'min:2'],
            'top_k' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $results = $this->searchService->search(
            query: $validated['query'],
            topK: $validated['top_k'] ?? 5
        );

        return response()->json($results);
    }
}
