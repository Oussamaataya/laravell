<?php

namespace App\Services;

use Illuminate\Support\Str;

class ImageEventService
{
    /**
     * Analyze an image (by path/filename) and optional surrounding text to
     * produce event_type, short description (<=40 words) and 3-5 hashtags.
     *
     * This is a conservative, local heuristic implementation. It does NOT
     * invent details not visible in the image. It can be replaced by an
     * external AI provider if configured.
     *
     * @param string $imagePath Path or filename (can be storage path)
     * @param string|null $text Optional publication text to help pick words
     * @return array ['event_type'=>string, 'description'=>string, 'hashtags'=>array]
     */
    public function analyze(string $imagePath, ?string $text = null): array
    {
        // If configured to use OpenAI, delegate to AIImageService for better descriptions
        if (config('ai.driver') === 'openai' && config('ai.openai.key')) {
            try {
                $svc = new AIImageService();
                return $svc->describe($imagePath, $text);
            } catch (\Exception $e) {
                // fallback to local heuristics below
            }
        }

        $filename = strtolower(pathinfo($imagePath, PATHINFO_BASENAME));
        $content = strtolower($text ?? '');

        // Keywords mapping to event types
        $map = [
            'plage' => 'Nettoyage de plage',
            'beach' => 'Nettoyage de plage',
            'arbre' => 'Plantation d\'arbres',
            'plantation' => 'Plantation d\'arbres',
            'plant' => 'Plantation d\'arbres',
            'recycl' => 'Collecte / Recyclage',
            'collecte' => 'Collecte / Recyclage',
            'vélo' => 'Vélo collectif',
            'velo' => 'Vélo collectif',
            'bicyc' => 'Vélo collectif',
            'conference' => 'Conférence / Atelier',
            'atelier' => 'Conférence / Atelier',
            'nettoy' => 'Nettoyage',
            'cleanup' => 'Nettoyage',
        ];

        $eventType = null;

        foreach ($map as $kw => $type) {
            if (Str::contains($filename, $kw) || Str::contains($content, $kw)) {
                $eventType = $type;
                break;
            }
        }

        if (!$eventType) {
            // Fallback: generic safe label
            $eventType = 'Événement écologique';
        }

        // Build a conservative description: must not invent unseen facts.
        // Keep it short (<=40 words). We'll use a safe template.
        $description = "Photo d'un(e) {$eventType}.";

        // Hashtags mapping per event type
        $tagsByType = [
            'Nettoyage de plage' => ['#NettoyagePlage', '#OcéanPropre', '#ZeroDechet', '#EcoAction', '#Volontaires'],
            'Plantation d\'arbres' => ['#PlantationArbres', '#Reforestation', '#Nature', '#GreenAction', '#Arbres'],
            'Collecte / Recyclage' => ['#Recyclage', '#ZeroDechet', '#Collecte', '#Tri', '#EcoAction'],
            'Vélo collectif' => ['#VeloCollectif', '#MobiliteDouce', '#Cyclisme', '#TransportVert', '#Communautaire'],
            'Conférence / Atelier' => ['#Conférence', '#Atelier', '#Education', '#Sustainability', '#EcoEvent'],
            'Nettoyage' => ['#Nettoyage', '#Communaute', '#EcoAction', '#ZeroDechet', '#Volontaires'],
            'Événement écologique' => ['#EcoEvent', '#ActionClimatique', '#Durabilité', '#Communauté', '#Environnement'],
        ];

        $hashtags = $tagsByType[$eventType] ?? $tagsByType['Événement écologique'];

        // Limit to 3-5 tags (prefer 4)
        $hashtags = array_slice($hashtags, 0, 5);

        return [
            'event_type' => $eventType,
            'description' => $description,
            'hashtags' => array_values($hashtags),
        ];
    }
}
