<?php

namespace App\Domain\Document\Services;

use App\Domain\Document\Models\Document;
use Illuminate\Http\UploadedFile;
use App\Domain\Document\Models\DocumentChunk;

class DocumentService
{
    public function __construct(
        private readonly PdfTextExtractor $pdfTextExtractor,
        private readonly TextChunker $textChunker,
        private readonly EmbeddingService $embeddingService,
        private readonly PineconeService $pineconeService
    ) {}

    public function upload(UploadedFile $file): Document
    {
        $path = $file->store('documents');

        $document = Document::create([
            'name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        $text = $this->pdfTextExtractor->extract(
            storage_path('app/private/' . $path)
        );

        $chunks = $this->textChunker->chunk($text);

        foreach ($chunks as $index => $chunk) {
            $documentChunk = DocumentChunk::create([
                'document_id' => $document->id,
                'chunk_index' => $index,
                'content' => $chunk,
            ]);

            $embedding = $this->embeddingService->generate($chunk);

            $this->pineconeService->upsert(
                id: "document-{$document->id}-chunk-{$documentChunk->id}",
                vector: $embedding,
                metadata: [
                    'document_id' => $document->id,
                    'chunk_id' => $documentChunk->id,
                    'chunk_index' => $index,
                    'document_name' => $document->name,
                ]
            );
        }

        logger()->info('Document chunked', [
            'document_id' => $document->id,
            'chunk_count' => count($chunks),
        ]);

        return $document;
    }
}
