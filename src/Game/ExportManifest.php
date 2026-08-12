<?php

declare(strict_types=1);

namespace Genesis\Game;

/**
 * Manifest d’export — ce qui voyage avec l’Institut (pas les murs).
 */
final class ExportManifest
{
    public static function build(array $state, array $mve): array
    {
        $analyzed = GameQueries::analyzedCreatures($state);
        $species = [];
        $hybrids = [];
        $biomes = [];
        $missioned = 0;
        $totalStats = ['force' => 0, 'vitesse' => 0, 'resistance' => 0, 'intelligence' => 0];

        foreach ($analyzed as $c) {
            $sp = (string) ($c['species'] ?? '—');
            if ($sp !== '' && $sp !== '—') {
                $species[$sp] = [
                    'species' => $sp,
                    'rarity' => $c['rarity'] ?? '—',
                    'biome' => $c['biome'] ?? null,
                    'is_hybrid' => !empty($c['is_hybrid']),
                    'name' => $c['name'] ?? $sp,
                    'status' => $c['status'] ?? 'prête',
                    'purity' => (int) ($c['purity'] ?? 100),
                    'abilities' => $c['abilities'] ?? [],
                    'synergy_title' => $c['synergy_title'] ?? null,
                    'parents' => $c['parents'] ?? null,
                ];
            }
            if (!empty($c['is_hybrid'])) {
                $hybrids[] = [
                    'name' => $c['name'] ?? $sp,
                    'parents' => $c['parents'] ?? [],
                    'synergy' => $c['synergy_title'] ?? null,
                    'abilities' => $c['abilities'] ?? [],
                    'stats' => $c['stats'] ?? [],
                ];
            }
            if (!empty($c['biome'])) {
                $biomes[(string) $c['biome']] = true;
            }
            if (!empty($c['mission_done'])) {
                $missioned++;
            }
            foreach ($totalStats as $k => $_) {
                $totalStats[$k] += (int) ($c['stats'][$k] ?? 0);
            }
        }

        $exhibits = [];
        foreach ($state['museum']['exhibits'] ?? [] as $ex) {
            if (!is_array($ex)) {
                continue;
            }
            $exhibits[] = [
                'label' => $ex['label'] ?? '',
                'species' => $ex['species'] ?? '—',
                'rarity' => $ex['rarity'] ?? '—',
                'note' => $ex['note'] ?? '',
            ];
        }

        $synergies = $state['heritage']['synergies'] ?? [];
        $memories = $state['heritage']['memories'] ?? [];

        return [
            'world' => $state['world']['name'] ?? 'Aster-0',
            'known_zones' => $state['world']['known_zones'] ?? [],
            'species' => array_values($species),
            'species_names' => array_keys($species),
            'hybrids' => $hybrids,
            'exhibits' => $exhibits,
            'exhibit_count' => count($exhibits),
            'synergies' => $synergies,
            'biomes' => array_keys($biomes),
            'missions_closed' => $missioned,
            'memory_count' => count($memories),
            'memories_tail' => array_slice($memories, -5),
            'stat_sum' => $totalStats,
            'mve' => $mve['score'] ?? '—',
            'mve_checks' => $mve['checks'] ?? [],
            'phrase' => 'Je ne pars pas avec une base. Je pars avec mon bestiaire.',
            'genesis_farewell' => self::farewell($species, $hybrids, $synergies),
            'summary' => sprintf(
                '%d espèce(s) · %d hybride(s) · %d pièce(s) de musée · %d synergie(s) · MVE %s',
                count($species),
                count($hybrids),
                count($exhibits),
                count($synergies),
                $mve['score'] ?? '—'
            ),
        ];
    }

    private static function farewell(array $species, array $hybrids, array $synergies): string
    {
        if ($hybrids === []) {
            return 'Le berceau a été lu. Ce qui part n’est encore qu’une collection — la galaxie demandera plus d’audace.';
        }
        $syn = $synergies[array_key_last($synergies)]['title'] ?? null;
        if (is_string($syn) && $syn !== '') {
            return sprintf(
                'Parmi les formes emportées, l’écho de « %s » prouve que le croisement n’était pas un accident — c’était une méthode.',
                $syn
            );
        }

        return sprintf(
            '%d forme(s) hybride(s) quittent Aster-0. Ce n’est pas une armée : c’est une thèse vivante.',
            count($hybrids)
        );
    }
}
