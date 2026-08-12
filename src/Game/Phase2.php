<?php

declare(strict_types=1);

namespace Genesis\Game;

use Genesis\Game\Data\Catalog;

/**
 * Phase 2 — spatio-port & orbites.
 * Pas encore galaxie multi : un signal lointain s’ouvre quand les orbites sont lues.
 */
final class Phase2
{
    public static function orbitalProgress(array $state): array
    {
        $total = 0;
        $done = 0;
        $unlocked = 0;
        foreach ($state['world']['zones'] ?? [] as $z) {
            if (($z['layer'] ?? '') !== 'orbital') {
                continue;
            }
            $total++;
            if (!empty($z['explored'])) {
                $done++;
            }
            if (!empty($z['unlocked']) && empty($z['locked'])) {
                $unlocked++;
            }
        }

        return [
            'done' => $done,
            'total' => $total,
            'unlocked' => $unlocked,
            'complete' => $total > 0 && $done >= $total,
            'baie' => !empty($state['export']['baie_unlocked']),
            'deep_signal' => !empty($state['export']['deep_space_signal']),
        ];
    }

    /**
     * Ouvre uniquement la 1ʳᵉ orbite (starter_orbital).
     */
    public static function unlockStarterOrbitals(array &$state): void
    {
        foreach (Catalog::orbitalZones() as $id => $def) {
            if (empty($def['starter_orbital'])) {
                continue;
            }
            if (!isset($state['world']['zones'][$id])) {
                $state['world']['zones'][$id] = self::zoneStateFromDef($def);
            }
            $state['world']['zones'][$id]['unlocked'] = true;
            $state['world']['zones'][$id]['locked'] = false;
            $state['world']['zones'][$id]['layer'] = 'orbital';
        }
    }

    /**
     * Assure que toutes les zones orbitales du catalogue existent dans l’état.
     */
    public static function ensureOrbitalZones(array &$state): void
    {
        foreach (Catalog::orbitalZones() as $id => $def) {
            if (!isset($state['world']['zones'][$id])) {
                $state['world']['zones'][$id] = self::zoneStateFromDef($def);
            } else {
                // merge defs (species pools) without wiping progress
                $prev = $state['world']['zones'][$id];
                $state['world']['zones'][$id] = array_merge(self::zoneStateFromDef($def), $prev);
                $state['world']['zones'][$id]['layer'] = 'orbital';
            }
        }
    }

    public static function zoneStateFromDef(array $def): array
    {
        $starter = !empty($def['starter_orbital']);

        return array_merge($def, [
            'explored' => false,
            'scan' => 0,
            'unlocked' => false,
            'locked' => true,
            'species_found' => [],
            'layer' => 'orbital',
        ]);
    }

    /**
     * Après recon orbitale : signal lointain si carte orbitale complète.
     */
    public static function afterOrbitalSuccess(array &$state): void
    {
        $prog = self::orbitalProgress($state);
        if (!$prog['complete'] || !empty($state['export']['deep_space_signal'])) {
            return;
        }
        $state['export']['deep_space_signal'] = true;
        $state['export']['deep_space_at'] = date('c');
        self::voice(
            $state,
            'Continuité',
            'Au-delà du seuil, un chœur de mondes. La galaxie n’est pas encore ouverte — mais elle répond.'
        );
        $state['ui']['latest_event'] = [
            'label' => 'Signal lointain',
            'message' => 'Les orbites d’Aster-0 sont lues. D’autres planètes attendent — plus tard.',
        ];
        $state['heritage']['memories'][] = 'Signal lointain : la phase galaxie se profile, hors du berceau.';
    }

    private static function voice(array &$state, string $marker, string $message): void
    {
        $state['ui']['genesis'] = ['marker' => $marker, 'message' => $message];
    }
}
