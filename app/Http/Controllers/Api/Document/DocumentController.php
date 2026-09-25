<?php

namespace App\Http\Controllers\Api\Document;

use App\Domain\Document\Services\DocumentService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService
    ) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $document = $this->documentService->upload(
            $request->file('file')
        );

        return response()->json([
            'message' => 'Document uploaded successfully.',
            'data' => $document,
        ], 201);
    }
}
