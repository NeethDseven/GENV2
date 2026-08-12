<?php

declare(strict_types=1);

namespace Genesis\Game;

/**
 * Mémoires scientifiques → entrées de journal lisibles (pas un wiki).
 */
final class MemoryJournal
{
    /**
     * @param list<string|array> $raw
     * @return list<array{text:string,kind:string,label:string,tone:string}>
     */
    public static function entries(array $raw, int $limit = 14): array
    {
        $out = [];
        foreach (array_reverse($raw) as $line) {
            $text = is_string($line) ? trim($line) : trim((string) ($line['text'] ?? $line['message'] ?? ''));
            if ($text === '') {
                continue;
            }
            $meta = self::classify($text);
            $out[] = [
                'text' => $text,
                'kind' => $meta['kind'],
                'label' => $meta['label'],
                'tone' => $meta['tone'],
            ];
            if (count($out) >= $limit) {
                break;
            }
        }

        return $out;
    }

    /**
     * @return array{kind:string,label:string,tone:string}
     */
    public static function classify(string $text): array
    {
        $t = mb_strtolower($text);

        return match (true) {
            str_contains($t, 'fil libre') => ['kind' => 'fil', 'label' => 'Fil libre', 'tone' => 'gold'],
            str_contains($t, 'croisement échoué') || str_contains($t, 'est mort') => ['kind' => 'loss', 'label' => 'Perte', 'tone' => 'danger'],
            str_contains($t, 'mutation fatale') || str_contains($t, 'perdu') && str_contains($t, 'mutation') => ['kind' => 'loss', 'label' => 'Perte', 'tone' => 'danger'],
            str_contains($t, 'capture échou') => ['kind' => 'loss', 'label' => 'Échec', 'tone' => 'danger'],
            str_contains($t, 'synergie') => ['kind' => 'synergy', 'label' => 'Synergie', 'tone' => 'flora'],
            str_contains($t, 'croisement') => ['kind' => 'cross', 'label' => 'Croisement', 'tone' => 'flora'],
            str_contains($t, 'mutation') => ['kind' => 'mutate', 'label' => 'Mutation', 'tone' => 'gold'],
            str_contains($t, 'mission : blessure') || str_contains($t, 'blessure') => ['kind' => 'mission', 'label' => 'Blessure', 'tone' => 'danger'],
            str_contains($t, 'mission : épuis') => ['kind' => 'mission', 'label' => 'Épuisement', 'tone' => 'warn'],
            str_contains($t, 'mission') => ['kind' => 'mission', 'label' => 'Mission', 'tone' => 'ok'],
            str_contains($t, 'capture') => ['kind' => 'capture', 'label' => 'Capture', 'tone' => 'ok'],
            str_contains($t, 'recon') || str_contains($t, 'contact') => ['kind' => 'recon', 'label' => 'Recon', 'tone' => 'teal'],
            str_contains($t, 'étude') => ['kind' => 'study', 'label' => 'Étude', 'tone' => 'teal'],
            str_contains($t, 'soin') => ['kind' => 'heal', 'label' => 'Soin', 'tone' => 'ok'],
            str_contains($t, 'musée') || str_contains($t, 'collection') => ['kind' => 'museum', 'label' => 'Musée', 'tone' => 'gold'],
            str_contains($t, 'assemblage') || str_contains($t, 'drone') => ['kind' => 'craft', 'label' => 'Hangar', 'tone' => 'teal'],
            str_contains($t, 'récolte') || str_contains($t, 'réappro') || str_contains($t, 'biomasse') => ['kind' => 'farm', 'label' => 'Terrain', 'tone' => 'ok'],
            str_contains($t, 'spatio-port') || str_contains($t, 'orbite') || str_contains($t, 'baie') => ['kind' => 'seuil', 'label' => 'Seuil', 'tone' => 'gold'],
            default => ['kind' => 'note', 'label' => 'Note', 'tone' => 'muted'],
        };
    }

    /**
     * Vue prête pour l’UI.
     *
     * @return array{total:int,entries:list<array>,highlight:?array}
     */
    public static function view(array $state, int $limit = 14): array
    {
        $raw = $state['heritage']['memories'] ?? [];
        if (!is_array($raw)) {
            $raw = [];
        }
        $entries = self::entries($raw, $limit);
        $highlight = $entries[0] ?? null;

        return [
            'total' => count($raw),
            'entries' => $entries,
            'highlight' => $highlight,
            'empty' => $entries === [],
        ];
    }
}
