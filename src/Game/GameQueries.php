<?php

declare(strict_types=1);

namespace Genesis\Game;

use Genesis\Game\Data\Catalog;

final class GameQueries
{
    public static function analyzedCreatures(array $state): array
    {
        $list = [];
        $active = $state['creature'] ?? [];
        if (!empty($active['analyzed'])) {
            $list[(string) ($active['id'] ?? 'c1')] = $active;
        }
        foreach ($state['bestiary'] ?? [] as $id => $creature) {
            if (!empty($creature['analyzed'])) {
                $list[(string) $id] = $creature;
            }
        }

        return $list;
    }

    /**
     * Signaux vivants contactés mais pas encore capturés (reprise possible plus tard).
     *
     * @return array<string, array>
     */
    public static function pendingSignals(array $state): array
    {
        $list = [];
        $active = $state['creature'] ?? [];
        if (!empty($active['discovered']) && empty($active['analyzed'])) {
            $list[(string) ($active['id'] ?? 'pending')] = $active;
        }
        foreach ($state['bestiary'] ?? [] as $id => $creature) {
            if (!is_array($creature)) {
                continue;
            }
            if (!empty($creature['discovered']) && empty($creature['analyzed'])) {
                $list[(string) $id] = $creature;
            }
        }

        return $list;
    }

    public static function hasPendingSignal(array $state): bool
    {
        return self::pendingSignals($state) !== [];
    }

    public static function distinctAnalyzedSpecies(array $state): array
    {
        $bySpecies = [];
        foreach (self::analyzedCreatures($state) as $id => $creature) {
            $species = (string) ($creature['species'] ?? '');
            if ($species === '' || $species === '—') {
                continue;
            }
            if (!isset($bySpecies[$species])) {
                $bySpecies[$species] = $creature;
            }
        }

        return $bySpecies;
    }

    /** Au moins 2 espèces distinctes capturées (parents possibles). */
    public static function hasCrossParents(array $state): bool
    {
        return count(self::distinctAnalyzedSpecies($state)) >= 2;
    }

    /** Peut lancer le croisement maintenant (parents + organique). */
    public static function canCross(array $state): bool
    {
        return self::hasCrossParents($state)
            && (int) ($state['resources']['organic'] ?? 0) >= Economy::crossCost();
    }

    public static function needsRecovery(array $creature): bool
    {
        return in_array((string) ($creature['status'] ?? 'prête'), ['blessée', 'épuisée'], true);
    }

    public static function recoverCost(array $creature): int
    {
        return Economy::recoverCost((string) ($creature['status'] ?? 'prête'));
    }

    /** @return list<string> */
    public static function discoveredSynergyIds(array $state): array
    {
        $ids = [];
        foreach ($state['heritage']['synergies'] ?? [] as $syn) {
            if (!empty($syn['id'])) {
                $ids[] = (string) $syn['id'];
            }
        }

        return array_values(array_unique($ids));
    }

    public static function isExhibited(array $state, array $creature): bool
    {
        $id = $creature['id'] ?? null;
        foreach ($state['museum']['exhibits'] ?? [] as $exhibit) {
            if (is_array($exhibit) && ($exhibit['creature_id'] ?? null) === $id) {
                return true;
            }
        }

        return false;
    }

    public static function unexploredZones(array $state): array
    {
        $out = [];
        foreach ($state['world']['zones'] ?? [] as $id => $zone) {
            if (empty($zone['explored'])) {
                $out[$id] = $zone;
            }
        }

        return $out;
    }

    public static function resourceLabel(array $resources): string
    {
        return sprintf(
            'Organique %d · Biomasse %d · Prélèvements %d · Logistique %d',
            (int) ($resources['organic'] ?? 0),
            (int) ($resources['biomass'] ?? 0),
            (int) ($resources['samples'] ?? 0),
            (int) ($resources['logistics'] ?? 0)
        );
    }

    public static function progress(array $state): array
    {
        $analyzedCount = count(self::analyzedCreatures($state));
        $canCross = $analyzedCount >= 2;
        $active = $state['creature'] ?? [];
        $hasHybrid = !empty($active['is_hybrid']) || self::hasAnyHybrid($state);
        $missionDone = !empty($active['mission_done']);
        $museum = !empty($state['museum']['exhibits']);

        $mve = MveEvaluator::evaluate($state);
        $stages = [
            ['key' => 'explorer', 'label' => 'Explorer', 'done' => !empty($state['world']['known_zones'])],
            ['key' => 'decouvrir', 'label' => 'Découvrir', 'done' => $analyzedCount >= 1 || !empty($active['discovered'])],
            ['key' => 'analyser', 'label' => 'Analyser', 'done' => $analyzedCount >= 1],
            ['key' => 'diversifier', 'label' => '2ᵉ espèce', 'done' => $analyzedCount >= 2],
            ['key' => 'croiser', 'label' => 'Croiser', 'done' => $hasHybrid],
            ['key' => 'mission', 'label' => 'Mission', 'done' => $missionDone || ($hasHybrid && $missionDone)],
            ['key' => 'musee', 'label' => 'Musée', 'done' => $museum],
            ['key' => 'export', 'label' => 'Projection', 'done' => !empty($state['export']['departed']) || $mve['complete']],
        ];

        // Mission done if any hybrid was missioned or active missioned after hybrid path
        if ($hasHybrid) {
            foreach (self::analyzedCreatures($state) as $c) {
                if (!empty($c['is_hybrid']) && !empty($c['mission_done'])) {
                    $stages[5]['done'] = true;
                    break;
                }
            }
            if (!empty($active['mission_done'])) {
                $stages[5]['done'] = true;
            }
        }

        $current = 'Boucle prête';
        $allDone = true;
        foreach ($stages as $stage) {
            if (!$stage['done']) {
                $current = $stage['label'];
                $allDone = false;
                break;
            }
        }
        if ($allDone) {
            $current = 'Patrimoine en croissance';
        }

        return [
            'stages' => $stages,
            'current' => $current,
            'completed' => count(array_filter($stages, static fn (array $s): bool => $s['done'])),
            'total' => count($stages),
            'analyzed_count' => $analyzedCount,
            'can_cross' => $canCross,
        ];
    }

    public static function hasAnyHybrid(array $state): bool
    {
        if (!empty($state['creature']['is_hybrid'])) {
            return true;
        }
        foreach ($state['bestiary'] ?? [] as $c) {
            if (!empty($c['is_hybrid'])) {
                return true;
            }
        }

        return false;
    }

    public static function mutationOptions(array $creature): array
    {
        $key = (string) ($creature['species_key'] ?? '');
        $species = $key !== '' ? Catalog::speciesByKey($key) : null;

        return is_array($species) ? ($species['mutations'] ?? []) : [];
    }

    public static function refreshCampus(array &$state): void
    {
        $creature = $state['creature'] ?? [];
        $status = $creature['status'] ?? 'prête';
        $known = !empty($state['world']['known_zones']);
        $analyzed = count(self::analyzedCreatures($state));
        $exhibits = $state['museum']['exhibits'] ?? [];
        $mve = MveEvaluator::evaluate($state);
        $features = Tutorial::features($state);

        // Bâtiments visibles selon progression (bureau → labo → vivarium → musée → baie)
        $campus = [];
        if (!empty($features['campus_bureau'])) {
            $campus['bureau'] = [
                'label' => 'Bureau des expéditions',
                'status' => $known ? 'exploration active' : 'actif',
            ];
        }
        if (!empty($features['campus_labo'])) {
            $campus['labo'] = [
                'label' => 'Laboratoire',
                'status' => match (true) {
                    self::canCross($state) && Tutorial::can($state, 'cross') => 'croisement possible',
                    $analyzed >= 1 => 'actif',
                    default => 'en attente de spécimen',
                },
            ];
        }
        if (!empty($features['campus_vivarium'])) {
            $campus['vivarium'] = [
                'label' => 'Vivarium',
                'status' => match ($status) {
                    'blessée' => 'soin requis',
                    'épuisée' => 'récupération',
                    default => 'actif',
                },
            ];
        }
        if (!empty($features['campus_musee'])) {
            $campus['musee'] = [
                'label' => 'Musée',
                'status' => !empty($exhibits) ? 'collection ouverte' : 'en préparation',
            ];
        }
        if (!empty($features['campus_baie'])) {
            $campus['baie'] = [
                'label' => 'Spatio-port',
                'status' => match (true) {
                    !empty($state['export']['departed']) || !empty($state['export']['baie_unlocked']) => 'actif (orbite)',
                    !empty($mve['complete']) => 'prêt',
                    default => 'en construction',
                },
            ];
        }
        $state['campus'] = $campus;
    }
}
