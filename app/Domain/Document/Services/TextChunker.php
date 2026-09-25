<?php

namespace App\Domain\Document\Services;

class TextChunker
{
    public function chunk(
        string $text,
        int $chunkSize = 1000,
        int $overlap = 200
    ): array {
        $text = trim($text);

        if ($text === '') {
            return [];
        }

        $chunks = [];
        $start = 0;
        $length = strlen($text);

        while ($start < $length) {
            $chunk = substr($text, $start, $chunkSize);

            if (trim($chunk) !== '') {
                $chunks[] = trim($chunk);
            }

            $start += $chunkSize - $overlap;
        }

        return $chunks;
    }
}
