<?php

namespace App\Services;

use GuzzleHttp\Client;

class AIModerationService
{
    public function moderate(string $text): array
    {
        $driver = config('ai.driver', 'local');
        if ($driver === 'openai' && config('ai.openai.key')) {
            $client = new Client(['base_uri' => config('ai.openai.base_uri')]);
            $resp = $client->post('/moderations', [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('ai.openai.key'),
                    'Content-Type' => 'application/json',
                ],
                'json' => ['input' => $text, 'model' => config('ai.openai.moderation_model')]
            ]);
            $body = json_decode((string)$resp->getBody(), true);
            return $body;
        }

        // Local simple heuristic (bad words detection)
        $badWords = ['merde','salaud','connard','con','putain','fdp','enculé','salope','ta mère'];
        $found = [];
        $lower = mb_strtolower($text, 'UTF-8');
        foreach ($badWords as $w) {
            if (preg_match('/\\b'.preg_quote($w,'/').'\\b/iu', $lower)) {
                $found[] = $w;
            }
        }
        return ['local' => ['has_bad_words' => count($found) > 0, 'bad_words' => $found]];
    }
}
