<?php

namespace App\Services;

use GuzzleHttp\Client;

class AIImageService
{
    /**
     * Generate a short description and hashtags for an image.
     */
    public function describe(string $imagePath, ?string $text = null): array
    {
        $driver = config('ai.driver', 'local');
        if ($driver === 'openai' && config('ai.openai.key')) {
            // This example uses OpenAI text API to generate a caption based on filename/text.
            $client = new Client(['base_uri' => config('ai.openai.base_uri')]);
            $prompt = "You are an assistant that writes a short (max 40 words) neutral description of an environmental event visible in an image. Do not invent facts not visible in the image. Use only information from filename or surrounding text: " . ($text ?? '') . "\nFilename: " . basename($imagePath);
            $resp = $client->post('/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('ai.openai.key'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => config('ai.openai.model'),
                    'messages' => [
                        ['role' => 'system', 'content' => 'You produce a JSON object with description and hashtags.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.2,
                    'max_tokens' => 200,
                ],
            ]);
            $body = json_decode((string)$resp->getBody(), true);
            // Try to extract content
            $content = $body['choices'][0]['message']['content'] ?? '';
            // Best-effort: parse JSON from assistant
            $json = @json_decode($content, true);
            if (is_array($json)) {
                return [
                    'event_type' => $json['event_type'] ?? null,
                    'description' => $json['description'] ?? $json['caption'] ?? null,
                    'hashtags' => $json['hashtags'] ?? [],
                ];
            }

            // Fallback: take the assistant text as description and produce simple tags
            $desc = trim($content);
            $hashtags = $this->buildHashtagsFromText($desc);
            return ['event_type' => null, 'description' => $desc, 'hashtags' => $hashtags];
        }

        // Local fallback: reuse ImageEventService heuristics
        $svc = new ImageEventService();
        $res = $svc->analyze($imagePath, $text);
        return [
            'event_type' => $res['event_type'],
            'description' => $res['description'],
            'hashtags' => $res['hashtags'],
        ];
    }

    protected function buildHashtagsFromText(string $text): array
    {
        $words = preg_split('/[^\p{L}0-9]+/u', strtolower($text), -1, PREG_SPLIT_NO_EMPTY);
        $candidates = array_unique(array_filter($words, function($w){ return mb_strlen($w) > 3; }));
        $tags = array_slice($candidates, 0, 5);
        return array_map(function($t){ return '#'.ucfirst($t); }, $tags);
    }
}
