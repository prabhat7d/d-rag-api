<?php

namespace App\Domain\Document\Services;

use OpenAI\Client;

class RagService
{
    public function __construct(
        private readonly Client $client
    ) {}

    public function generate(string $question, array $matches): string
    {
        $context = collect($matches)
            ->map(function (array $match) {
                return $match['content'] ?? '';
            })
            ->filter()
            ->implode("\n\n---\n\n");

        if ($context === '') {
            return 'I could not find relevant information in the uploaded documents.';
        }

        $response = $this->client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => <<<PROMPT
You are a document question-answering assistant.

Answer the user's question using only the provided document context.

Rules:
- Do not use outside knowledge.
- If the context does not contain the answer, say that the information is not available in the provided documents.
- Keep the answer clear and concise.
PROMPT,
                ],
                [
                    'role' => 'user',
                    'content' => "Document context:\n\n{$context}\n\nQuestion:\n{$question}",
                ],
            ],
        ]);

        return $response->choices[0]->message->content;
    }
}
