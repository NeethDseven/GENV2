<?php

declare(strict_types=1);

namespace Genesis\Game;

use Genesis\Game\Data\Catalog;

final class StateFactory
{
    public static function initial(): array
    {
        $zones = [];
        foreach (Catalog::zones() as $id => $zone) {
            $starter = !empty($zone['starter']);
            $zones[$id] = array_merge($zone, [
                'explored' => false,
                'scan' => 0,
                'layer' => 'natal',
                'unlocked' => $starter,
                'locked' => !$starter,
                'species_found' => [],
            ]);
        }

        // Orbital — fermé jusqu’au spatio-port ; 1ʳᵉ seule ouverte à l’activation
        foreach (Catalog::orbitalZones() as $id => $def) {
            $zones[$id] = Phase2::zoneStateFromDef($def);
        }

        $assist = Tutorial::assistant([
            'tutorial' => Tutorial::defaultState(),
        ]);

        return [
            'screen' => 'institute',
            'world' => [
                'name' => 'Aster-0',
                'stable' => true,
                'risk' => 'inconnu',
                'known_zones' => [],
                'signal' => null,
                'zones' => $zones,
                'selected_zone' => 'plaine',
                'last_report' => null,
            ],
            'creature' => CreatureFactory::blank('c1'),
            'bestiary' => [],
            'resources' => [
                'organic' => 3,
                'samples' => 0,
                'logistics' => 3,
                'biomass' => 1,
            ],
            'drones' => [
                'fleet' => [
                    ['id' => 'dr1', 'type' => DroneYard::TYPE_RECON, 'label' => DroneYard::label(DroneYard::TYPE_RECON), 'status' => 'ready'],
                    ['id' => 'dr2', 'type' => DroneYard::TYPE_CAPTURE, 'label' => DroneYard::label(DroneYard::TYPE_CAPTURE), 'status' => 'ready'],
                ],
            ],
            'expedition' => [
                'drone_type' => DroneYard::TYPE_RECON,
                'escort_creature_id' => null,
                'capture_escorts' => [],
                'drone_count' => 1,
                'capture_count' => 1,
                'study_count' => 0,
            ],
            'tasks' => [],
            'tutorial' => Tutorial::defaultState(),
            'campus' => [
                'bureau' => ['label' => 'Bureau des expéditions', 'status' => 'actif'],
            ],
            'museum' => [
                'exhibits' => [],
            ],
            'heritage' => [
                'memories' => [],
                'synergies' => [],
            ],
            'freeplay' => FreePlayGoals::defaultState(),
            'export' => [
                'departed' => false,
                'departed_at' => null,
                'manifest' => null,
                'baie_unlocked' => false,
                'deep_space_signal' => false,
                'deep_space_at' => null,
            ],
            'ui' => [
                'latest_event' => [
                    'label' => 'Assistant — ' . $assist['title'],
                    'message' => $assist['message'],
                ],
                'genesis' => [
                    'marker' => $assist['marker'],
                    'message' => $assist['message'],
                ],
                'force_mission_outcome' => null,
                'next_creature_seq' => 2,
                'next_task_seq' => 0,
                'next_drone_seq' => 3,
                'loop' => 1,
                'last_save_id' => null,
                'tips_dismissed' => true, // guide UI remplacé par assistant
            ],
        ];
    }
}
