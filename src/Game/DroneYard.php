<?php

declare(strict_types=1);

namespace Genesis\Game;

/**
 * Flotte de drones typés (craftables).
 */
final class DroneYard
{
    public const TYPE_RECON = 'recon';
    public const TYPE_CAPTURE = 'capture';
    public const TYPE_STUDY = 'study';
    public const TYPE_ORBITAL = 'orbital';

    public static function recipes(): array
    {
        return [
            self::TYPE_RECON => [
                'type' => self::TYPE_RECON,
                'label' => 'Sonde d’exploration',
                'desc' => 'Cartographie et lecture de zone. Plus de sondes = meilleur scan / contact.',
                'biomass' => 3,
                'logistics' => 1,
                'duration' => 12,
                'role' => 'terrain',
                'unlock_step' => Tutorial::STEP_RECON,
            ],
            self::TYPE_CAPTURE => [
                'type' => self::TYPE_CAPTURE,
                'label' => 'Drone de capture',
                'desc' => 'Prise d’un signal vivant. Plus de drones = meilleure chance de capture.',
                'biomass' => 4,
                'logistics' => 1,
                'duration' => 14,
                'role' => 'prise',
                'unlock_step' => Tutorial::STEP_ANALYZE,
            ],
            self::TYPE_STUDY => [
                'type' => self::TYPE_STUDY,
                'label' => 'Drone d’analyse',
                'desc' => 'Stabilise mutation et croisement : réduit le risque de destruction.',
                'biomass' => 3,
                'logistics' => 1,
                'duration' => 12,
                'role' => 'labo',
                'unlock_step' => Tutorial::STEP_MUTATE,
            ],
            self::TYPE_ORBITAL => [
                'type' => self::TYPE_ORBITAL,
                'label' => 'Sonde extra-planétaire',
                'desc' => 'Exploration hors berceau (spatio-port requis).',
                'biomass' => 8,
                'logistics' => 2,
                'duration' => 22,
                'requires_baie' => true,
                'role' => 'orbite',
                'unlock_step' => Tutorial::STEP_FREE,
            ],
        ];
    }

    public static function label(string $type): string
    {
        return self::recipes()[$type]['label'] ?? $type;
    }

    public static function canCraft(array $state, string $type): bool
    {
        if (!isset(self::recipes()[$type])) {
            return false;
        }

        return Tutorial::canCraftType($state, $type);
    }

    /**
     * Catalogue craft avec statut de déblocage (suit le tutoriel).
     *
     * @return array<string, array<string, mixed>>
     */
    public static function recipesForState(array $state): array
    {
        $out = [];
        foreach (self::recipes() as $type => $recipe) {
            $unlocked = self::canCraft($state, $type);
            $step = (string) ($recipe['unlock_step'] ?? Tutorial::STEP_FREE);
            $recipe['unlocked'] = $unlocked;
            $recipe['unlock_step'] = $step;
            $recipe['unlock_label'] = Tutorial::stepLabel($step);
            $recipe['lock_hint'] = $unlocked
                ? ''
                : sprintf('Débloqué après : %s', Tutorial::stepLabel($step));
            $out[$type] = $recipe;
        }

        return $out;
    }

    /** Uniquement les recettes craftables maintenant. */
    public static function availableRecipes(array $state): array
    {
        return array_filter(
            self::recipesForState($state),
            static fn (array $r): bool => !empty($r['unlocked'])
        );
    }

    public static function countReady(array $state, string $type): int
    {
        $n = 0;
        foreach ($state['drones']['fleet'] ?? [] as $d) {
            if (($d['type'] ?? '') === $type && ($d['status'] ?? '') === 'ready') {
                $n++;
            }
        }

        return $n;
    }

    public static function countBusy(array $state, string $type): int
    {
        $n = 0;
        foreach ($state['drones']['fleet'] ?? [] as $d) {
            if (($d['type'] ?? '') === $type && ($d['status'] ?? '') === 'busy') {
                $n++;
            }
        }

        return $n;
    }

    public static function countTotal(array $state, string $type): int
    {
        $n = 0;
        foreach ($state['drones']['fleet'] ?? [] as $d) {
            if (($d['type'] ?? '') === $type) {
                $n++;
            }
        }

        return $n;
    }

    public static function reserve(array &$state, string $type): ?string
    {
        $ids = self::reserveMany($state, $type, 1);

        return $ids[0] ?? null;
    }

    /**
     * Réserve jusqu’à $count drones prêts du type donné.
     *
     * @return list<string>
     */
    public static function reserveMany(array &$state, string $type, int $count): array
    {
        $count = max(1, min(5, $count));
        $ids = [];
        foreach ($state['drones']['fleet'] ?? [] as $i => $d) {
            if (count($ids) >= $count) {
                break;
            }
            if (($d['type'] ?? '') === $type && ($d['status'] ?? '') === 'ready') {
                $state['drones']['fleet'][$i]['status'] = 'busy';
                $ids[] = (string) ($d['id'] ?? '');
            }
        }

        return $ids;
    }

    public static function release(array &$state, string $droneId, string $status = 'ready'): void
    {
        foreach ($state['drones']['fleet'] ?? [] as $i => $d) {
            if ((string) ($d['id'] ?? '') === $droneId) {
                $state['drones']['fleet'][$i]['status'] = $status;
                return;
            }
        }
    }

    /** @param list<string> $droneIds */
    public static function releaseMany(array &$state, array $droneIds, string $status = 'ready'): void
    {
        foreach ($droneIds as $id) {
            if ($id !== '') {
                self::release($state, (string) $id, $status);
            }
        }
    }

    public static function add(array &$state, string $type): array
    {
        $seq = (int) ($state['ui']['next_drone_seq'] ?? 1);
        $state['ui']['next_drone_seq'] = $seq + 1;
        $drone = [
            'id' => 'dr' . $seq,
            'type' => $type,
            'label' => self::label($type),
            'status' => 'ready',
        ];
        $state['drones']['fleet'][] = $drone;

        return $drone;
    }

    public static function summary(array $state): array
    {
        $summary = [];
        foreach (array_keys(self::recipes()) as $type) {
            $ready = 0;
            $busy = 0;
            $total = 0;
            foreach ($state['drones']['fleet'] ?? [] as $d) {
                if (($d['type'] ?? '') !== $type) {
                    continue;
                }
                $total++;
                if (($d['status'] ?? '') === 'ready') {
                    $ready++;
                } elseif (($d['status'] ?? '') === 'busy') {
                    $busy++;
                }
            }
            $summary[$type] = [
                'type' => $type,
                'label' => self::label($type),
                'ready' => $ready,
                'busy' => $busy,
                'total' => $total,
            ];
        }

        return $summary;
    }
}
