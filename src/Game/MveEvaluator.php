<?php

declare(strict_types=1);

namespace Genesis\Game;

use Genesis\Game\Data\Catalog;

/**
 * Baie / Spatio-port : s’ouvre après cartographie natale + patrimoine digne.
 * Puis permet l’orbite (nouvelles planètes), pas une interruption de boucle.
 */
final class MveEvaluator
{
    public static function natalZonesExplored(array $state): array
    {
        $total = 0;
        $done = 0;
        foreach ($state['world']['zones'] ?? [] as $zone) {
            if (($zone['layer'] ?? 'natal') !== 'natal') {
                continue;
            }
            $total++;
            if (!empty($zone['explored'])) {
                $done++;
            }
        }
        // Fallback catalogue if zones not yet merged
        if ($total === 0) {
            $total = count(Catalog::zones());
        }

        return ['done' => $done, 'total' => $total, 'complete' => $total > 0 && $done >= $total];
    }

    public static function evaluate(array $state): array
    {
        $analyzed = GameQueries::analyzedCreatures($state);
        $speciesNames = [];
        $hybridCount = 0;
        $missionProofs = 0;
        $biomes = [];

        foreach ($analyzed as $c) {
            $speciesNames[(string) ($c['species'] ?? '')] = true;
            if (!empty($c['is_hybrid'])) {
                $hybridCount++;
            }
            if (!empty($c['mission_done'])) {
                $missionProofs++;
            }
            if (!empty($c['biome'])) {
                $biomes[(string) $c['biome']] = true;
            }
        }

        $exhibits = $state['museum']['exhibits'] ?? [];
        $memories = $state['heritage']['memories'] ?? [];
        $hasHybridExhibit = false;
        foreach ($exhibits as $ex) {
            if (is_array($ex) && (
                str_contains((string) ($ex['note'] ?? ''), 'Hybride')
                || str_contains((string) ($ex['rarity'] ?? ''), 'unique')
                || !empty($ex['parents'])
            )) {
                $hasHybridExhibit = true;
            }
        }

        $map = self::natalZonesExplored($state);

        $checks = [
            [
                'id' => 'natal_map',
                'label' => 'Cartographie natale complète (toutes les zones du berceau)',
                'done' => $map['complete'],
                'detail' => $map['done'] . '/' . $map['total'] . ' zones',
            ],
            [
                'id' => 'species_core',
                'label' => 'Noyau d’espèces analysées (≥ 3)',
                'done' => count($speciesNames) >= 3,
                'detail' => count($speciesNames) . ' espèce(s)',
            ],
            [
                'id' => 'hybrid',
                'label' => 'Au moins un hybride créé',
                'done' => $hybridCount >= 1 || GameQueries::hasAnyHybrid($state),
                'detail' => $hybridCount . ' hybride(s)',
            ],
            [
                'id' => 'mission',
                'label' => 'Au moins une mission de terrain',
                'done' => $missionProofs >= 1,
                'detail' => $missionProofs . ' mission(s)',
            ],
            [
                'id' => 'museum',
                'label' => 'Musée non vide',
                'done' => count($exhibits) >= 1,
                'detail' => count($exhibits) . ' pièce(s)',
            ],
            [
                'id' => 'hybrid_memory',
                'label' => 'Hybride exposé ou croisement mémorisé',
                'done' => $hasHybridExhibit || self::memoryMentionsCross($memories),
                'detail' => $hasHybridExhibit ? 'hybride au musée' : (self::memoryMentionsCross($memories) ? 'mémoire OK' : 'manquant'),
            ],
            [
                'id' => 'memory',
                'label' => 'Mémoire scientifique (≥ 3)',
                'done' => count($memories) >= 3,
                'detail' => count($memories) . ' entrée(s)',
            ],
        ];

        $doneCount = count(array_filter($checks, static fn (array $c): bool => $c['done']));
        $total = count($checks);
        $ready = $doneCount === $total;
        $exported = !empty($state['export']['departed']);
        $unlocked = $ready || !empty($state['export']['baie_unlocked']);

        $orbital = Phase2::orbitalProgress($state);

        return [
            'ready' => $ready && empty($state['export']['baie_unlocked']),
            'complete' => $ready,
            'unlocked' => $unlocked,
            'exported' => $exported,
            'checks' => $checks,
            'done' => $doneCount,
            'total' => $total,
            'score' => $doneCount . '/' . $total,
            'natal_map' => $map,
            'orbital' => $orbital,
            'phrase' => match (true) {
                !empty($state['export']['deep_space_signal']) => 'Orbites lues. Signal lointain : la galaxie attend (plus tard).',
                !empty($state['export']['baie_unlocked']) => sprintf(
                    'Spatio-port actif — orbites %d/%d lues.',
                    $orbital['done'],
                    $orbital['total']
                ),
                $ready => 'Berceau cartographié et patrimoine digne : le spatio-port peut s’ouvrir vers l’orbite.',
                default => 'Cartographiez tout Aster-0 et bâtissez un patrimoine digne avant le spatio-port.',
            },
        ];
    }

    private static function memoryMentionsCross(array $memories): bool
    {
        foreach ($memories as $line) {
            if (is_string($line) && (
                str_contains($line, 'Croisement')
                || str_contains($line, 'Synergie')
                || str_contains($line, 'hybride')
            )) {
                return true;
            }
        }

        return false;
    }
}
