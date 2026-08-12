<?php

declare(strict_types=1);

/**
 * GENESIS REBORN — prototype state & loop
 * Design: 07 (créatures) · 14 (boucle) · 15 (backlog P0/P1)
 */

// ---------------------------------------------------------------------------
// Catalogs
// ---------------------------------------------------------------------------

function genesis_species_catalog(): array
{
    return [
        'thermidé' => [
            'species' => 'Thermidé',
            'rarity' => 'commune',
            'name' => 'Thermidé d’écho',
            'signal' => 'Trace thermique — espèce non cataloguée',
            'stats' => [
                'force' => 4,
                'vitesse' => 6,
                'resistance' => 3,
                'intelligence' => 5,
            ],
            'abilities' => ['Lecture thermique'],
            'mutations' => [
                'stats' => [
                    'label' => 'Charge musculaire',
                    'description' => 'Force +3 · Résistance +1 · pureté −10',
                    'stats' => ['force' => 3, 'resistance' => 1],
                    'ability' => null,
                    'purity_cost' => 10,
                    'name_suffix' => 'renforcé',
                    'rarity' => 'peu commune',
                ],
                'ability' => [
                    'label' => 'Peau de fardeau',
                    'description' => 'Capacité « Peau de fardeau » · Force +1 · pureté −15',
                    'stats' => ['force' => 1],
                    'ability' => 'Peau de fardeau',
                    'purity_cost' => 15,
                    'name_suffix' => 'de fardeau',
                    'rarity' => 'peu commune',
                ],
            ],
        ],
        'salinide' => [
            'species' => 'Salinide',
            'rarity' => 'peu commune',
            'name' => 'Salinide de crête',
            'signal' => 'Signature saline — carapace minérale',
            'stats' => [
                'force' => 5,
                'vitesse' => 3,
                'resistance' => 7,
                'intelligence' => 3,
            ],
            'abilities' => ['Coque saline'],
            'mutations' => [
                'stats' => [
                    'label' => 'Ossification',
                    'description' => 'Résistance +3 · Force +1 · pureté −10',
                    'stats' => ['resistance' => 3, 'force' => 1],
                    'ability' => null,
                    'purity_cost' => 10,
                    'name_suffix' => 'ossifié',
                    'rarity' => 'rare',
                ],
                'ability' => [
                    'label' => 'Marée figée',
                    'description' => 'Capacité « Marée figée » · Résistance +1 · pureté −15',
                    'stats' => ['resistance' => 1],
                    'ability' => 'Marée figée',
                    'purity_cost' => 15,
                    'name_suffix' => 'des marées',
                    'rarity' => 'rare',
                ],
            ],
        ],
        'cryopode' => [
            'species' => 'Cryopode',
            'rarity' => 'rare',
            'name' => 'Cryopode des brumes',
            'signal' => 'Écho glacé — mouvement sous la canopée',
            'stats' => [
                'force' => 3,
                'vitesse' => 7,
                'resistance' => 4,
                'intelligence' => 6,
            ],
            'abilities' => ['Voile de brume'],
            'mutations' => [
                'stats' => [
                    'label' => 'Sprints gelés',
                    'description' => 'Vitesse +3 · Intelligence +1 · pureté −10',
                    'stats' => ['vitesse' => 3, 'intelligence' => 1],
                    'ability' => null,
                    'purity_cost' => 10,
                    'name_suffix' => 'fulgurant',
                    'rarity' => 'rare',
                ],
                'ability' => [
                    'label' => 'Souffle d’hiver',
                    'description' => 'Capacité « Souffle d’hiver » · Intelligence +1 · pureté −15',
                    'stats' => ['intelligence' => 1],
                    'ability' => 'Souffle d’hiver',
                    'purity_cost' => 15,
                    'name_suffix' => 'd’hiver',
                    'rarity' => 'très rare',
                ],
            ],
        ],
    ];
}

function genesis_default_zones(): array
{
    return [
        'plaine' => [
            'id' => 'plaine',
            'name' => 'Plaine d’écho',
            'risk' => 'modéré',
            'species_key' => 'thermidé',
            'explored' => false,
        ],
        'crete' => [
            'id' => 'crete',
            'name' => 'Crêtes salines',
            'risk' => 'élevé',
            'species_key' => 'salinide',
            'explored' => false,
        ],
        'foret' => [
            'id' => 'foret',
            'name' => 'Forêt froide',
            'risk' => 'modéré',
            'species_key' => 'cryopode',
            'explored' => false,
        ],
    ];
}

function genesis_blank_creature(string $id = 'c1'): array
{
    return [
        'id' => $id,
        'name' => 'Spécimen d’Origine',
        'species' => 'Inconnue',
        'species_key' => null,
        'rarity' => 'inconnue',
        'stats' => [
            'force' => 0,
            'vitesse' => 0,
            'resistance' => 0,
            'intelligence' => 0,
        ],
        'abilities' => [],
        'purity' => 100,
        'discovered' => false,
        'analyzed' => false,
        'mutated' => false,
        'mission_done' => false,
        'status' => 'prête',
        'parents' => null,
        'zone_id' => null,
    ];
}

function genesis_default_state(): array
{
    return [
        'screen' => 'institute',
        'world' => [
            'name' => 'Aster-0',
            'stable' => true,
            'recon_done' => false,
            'risk' => 'inconnu',
            'biomes' => ['Plaine d’écho', 'Crêtes salines', 'Forêt froide'],
            'known_zones' => [],
            'signal' => null,
            'zones' => genesis_default_zones(),
            'selected_zone' => null,
        ],
        'creature' => genesis_blank_creature('c1'),
        'bestiary' => [],
        'resources' => [
            'organic' => 4,
            'samples' => 0,
            'logistics' => 3,
        ],
        'drones' => [
            'recon' => ['label' => 'Sonde de lecture', 'ready' => 2, 'deployed' => 0],
            'sample' => ['label' => 'Filet de prélèvement', 'ready' => 1, 'deployed' => 0],
        ],
        'campus' => [
            'bureau' => ['label' => 'Bureau des expéditions', 'status' => 'actif'],
            'labo' => ['label' => 'Laboratoire central', 'status' => 'en attente'],
            'vivarium' => ['label' => 'Vivarium', 'status' => 'actif'],
            'musee' => ['label' => 'Musée', 'status' => 'vide'],
            'baie' => ['label' => 'Baie de projection', 'status' => 'verrouillée'],
        ],
        'museum' => [
            'exhibits' => [],
        ],
        'heritage' => [
            'memories' => [],
        ],
        'ui' => [
            'latest_event' => [
                'label' => 'Arrivée',
                'message' => 'L’Institut se prépare à lire le monde natal.',
            ],
            'genesis' => [
                'marker' => 'Observation',
                'message' => 'Le berceau est encore opaque. Sans lecture, toute sortie reste une hypothèse.',
            ],
            'force_mission_outcome' => null,
            'next_creature_seq' => 2,
        ],
    ];
}

// ---------------------------------------------------------------------------
// State helpers
// ---------------------------------------------------------------------------

function genesis_is_list(array $array): bool
{
    if (function_exists('array_is_list')) {
        return array_is_list($array);
    }

    if ($array === []) {
        return true;
    }

    return array_keys($array) === range(0, count($array) - 1);
}

function genesis_merge_state(array $defaults, array $state): array
{
    foreach ($state as $key => $value) {
        if (isset($defaults[$key]) && is_array($defaults[$key]) && is_array($value)) {
            // Lists (numeric keys) replace wholesale when provided
            if ($defaults[$key] !== [] && genesis_is_list($defaults[$key]) && genesis_is_list($value)) {
                $defaults[$key] = $value;
                continue;
            }
            $defaults[$key] = genesis_merge_state($defaults[$key], $value);
            continue;
        }

        $defaults[$key] = $value;
    }

    return $defaults;
}

/**
 * Migrate legacy "branch / lignée" state into the creature model (Design 07).
 */
function genesis_migrate_legacy_creature(array $state): array
{
    if (!isset($state['creature']) || !is_array($state['creature'])) {
        if (isset($state['branch']) && is_array($state['branch'])) {
            $branch = $state['branch'];
            $knowledge = $branch['knowledge'] ?? 'inconnu';
            $analyzed = in_array($knowledge, ['lu', 'cartographié'], true) || !empty($branch['transformed']);
            $discovered = $analyzed || $knowledge !== 'inconnu' || !empty($branch['tested']);

            $abilities = [];
            if (!empty($branch['dons']) && is_array($branch['dons'])) {
                $abilities = array_values(array_slice($branch['dons'], 0, 2));
            }

            $status = 'prête';
            if (($branch['integrity'] ?? 'stable') === 'marquée' || !empty($branch['scar'])) {
                $status = 'blessée';
            }

            $state['creature'] = [
                'id' => 'c1',
                'name' => $branch['name'] ?? 'Spécimen d’Origine',
                'species' => $analyzed ? ($branch['nature'] ?? 'Thermidé') : 'Inconnue',
                'species_key' => $analyzed ? 'thermidé' : null,
                'rarity' => $analyzed ? 'commune' : 'inconnue',
                'stats' => $analyzed
                    ? ['force' => 4, 'vitesse' => 5, 'resistance' => 3, 'intelligence' => 4]
                    : ['force' => 0, 'vitesse' => 0, 'resistance' => 0, 'intelligence' => 0],
                'abilities' => $abilities,
                'purity' => !empty($branch['transformed']) ? 85 : 100,
                'discovered' => $discovered || !empty(($state['world']['recon_done'] ?? false)),
                'analyzed' => ($analyzed && $knowledge === 'cartographié') || !empty($branch['transformed']),
                'mutated' => (bool) ($branch['transformed'] ?? false),
                'mission_done' => (bool) ($branch['tested'] ?? false),
                'status' => $status,
                'parents' => null,
                'zone_id' => null,
            ];

            if ($knowledge === 'lu' && empty($branch['transformed'])) {
                $state['creature']['discovered'] = true;
                $state['creature']['analyzed'] = false;
                $state['creature']['species'] = 'Inconnue';
                $state['creature']['species_key'] = null;
                $state['creature']['rarity'] = 'inconnue';
                $state['creature']['stats'] = [
                    'force' => 0,
                    'vitesse' => 0,
                    'resistance' => 0,
                    'intelligence' => 0,
                ];
            }
        }
    }

    if (isset($state['creature']) && is_array($state['creature']) && !isset($state['creature']['id'])) {
        $state['creature']['id'] = 'c1';
    }

    if (isset($state['heritage']['inscriptions']) && is_array($state['heritage']['inscriptions'])) {
        $state['museum'] = $state['museum'] ?? ['exhibits' => []];
        foreach ($state['heritage']['inscriptions'] as $inscription) {
            if (is_string($inscription) && $inscription !== '') {
                $state['museum']['exhibits'][] = [
                    'label' => $inscription,
                    'species' => '—',
                    'note' => 'Inscription héritée',
                ];
            }
        }
        unset($state['heritage']['inscriptions']);
    }

    // Normalize string exhibits → structured pieces
    if (isset($state['museum']['exhibits']) && is_array($state['museum']['exhibits'])) {
        $normalized = [];
        foreach ($state['museum']['exhibits'] as $exhibit) {
            if (is_string($exhibit) && $exhibit !== '') {
                $normalized[] = [
                    'label' => $exhibit,
                    'species' => '—',
                    'note' => 'Pièce de collection',
                ];
            } elseif (is_array($exhibit) && isset($exhibit['label'])) {
                $normalized[] = $exhibit;
            }
        }
        $state['museum']['exhibits'] = $normalized;
    }

    if (isset($state['campus']['galerie']) && !isset($state['campus']['musee'])) {
        $state['campus']['musee'] = [
            'label' => 'Musée',
            'status' => $state['campus']['galerie']['status'] ?? 'vide',
        ];
        unset($state['campus']['galerie']);
    }

    if (isset($state['ui']['force_test_outcome']) && !isset($state['ui']['force_mission_outcome'])) {
        $map = ['success' => 'success', 'scar' => 'injured'];
        $forced = $state['ui']['force_test_outcome'];
        $state['ui']['force_mission_outcome'] = $map[$forced] ?? $forced;
    }

    // Ensure zones exist on older sessions
    if (!isset($state['world']['zones']) || !is_array($state['world']['zones']) || $state['world']['zones'] === []) {
        $state['world']['zones'] = genesis_default_zones();
    }

    if (!isset($state['bestiary']) || !is_array($state['bestiary'])) {
        $state['bestiary'] = [];
    }

    unset($state['branch']);

    return $state;
}

function genesis_normalize_state(array $state): array
{
    $state = genesis_migrate_legacy_creature($state);

    return genesis_merge_state(genesis_default_state(), $state);
}

function genesis_boot_state(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (!isset($_SESSION['genesis']) || !is_array($_SESSION['genesis'])) {
        $_SESSION['genesis'] = genesis_default_state();
        return;
    }

    $_SESSION['genesis'] = genesis_normalize_state($_SESSION['genesis']);
}

function genesis_state(): array
{
    genesis_boot_state();

    return $_SESSION['genesis'];
}

function genesis_save_state(array $state): void
{
    $_SESSION['genesis'] = genesis_normalize_state($state);
}

function genesis_next_creature_id(array &$state): string
{
    $seq = (int) ($state['ui']['next_creature_seq'] ?? 2);
    $state['ui']['next_creature_seq'] = $seq + 1;

    return 'c' . $seq;
}

function genesis_archive_active_if_ready(array &$state): void
{
    $creature = $state['creature'] ?? [];
    if (empty($creature['discovered']) && empty($creature['analyzed'])) {
        return;
    }

    $id = (string) ($creature['id'] ?? 'c1');
    $state['bestiary'][$id] = $creature;
}

function genesis_analyzed_creatures(array $state): array
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

function genesis_zone(array $state, ?string $zoneId): ?array
{
    if ($zoneId === null || $zoneId === '') {
        return null;
    }

    $zones = $state['world']['zones'] ?? [];

    return $zones[$zoneId] ?? null;
}

function genesis_species(string $key): ?array
{
    $catalog = genesis_species_catalog();

    return $catalog[$key] ?? null;
}

// ---------------------------------------------------------------------------
// UI labels & navigation
// ---------------------------------------------------------------------------

function genesis_navigation(): array
{
    return [
        'institute' => 'Institut',
        'world' => 'Monde',
        'expedition' => 'Expédition',
        'archives' => 'Musée',
    ];
}

function genesis_screen(string $requestedScreen): string
{
    $screens = array_keys(genesis_navigation());

    if (in_array($requestedScreen, $screens, true)) {
        return $requestedScreen;
    }

    return 'institute';
}

function genesis_resource_label(array $resources): string
{
    return sprintf(
        'Organique %d · Prélèvements %d · Logistique %d',
        (int) ($resources['organic'] ?? 0),
        (int) ($resources['samples'] ?? 0),
        (int) ($resources['logistics'] ?? 0)
    );
}

function genesis_drone_summary(array $drones): string
{
    $recon = $drones['recon'] ?? [];
    $sample = $drones['sample'] ?? [];

    return sprintf(
        '%s ×%d · %s ×%d',
        $recon['label'] ?? 'Sonde',
        (int) ($recon['ready'] ?? 0),
        $sample['label'] ?? 'Filet',
        (int) ($sample['ready'] ?? 0)
    );
}

function genesis_set_voice(array &$state, string $marker, string $message): void
{
    $state['ui']['genesis'] = [
        'marker' => $marker,
        'message' => $message,
    ];
}

function genesis_refresh_campus(array &$state): void
{
    $world = $state['world'] ?? [];
    $creature = $state['creature'] ?? [];
    $exhibits = $state['museum']['exhibits'] ?? [];
    $status = $creature['status'] ?? 'prête';
    $anyRecon = !empty($world['recon_done']) || !empty($world['known_zones']);

    $state['campus']['bureau']['status'] = $anyRecon ? 'mission accomplie' : 'actif';
    $state['campus']['labo']['status'] = $anyRecon
        ? ((bool) ($creature['mutated'] ?? false)
            ? 'mutation fixée'
            : ((bool) ($creature['analyzed'] ?? false) ? 'prêt' : 'analyse requise'))
        : 'en attente';
    $state['campus']['vivarium']['status'] = match ($status) {
        'blessée' => 'soin requis',
        'épuisée' => 'récupération',
        default => 'actif',
    };
    $state['campus']['musee']['status'] = !empty($exhibits) ? 'collection ouverte' : 'vide';
    $state['campus']['baie']['status'] = 'verrouillée';
}

function genesis_progress(array $state): array
{
    $creature = $state['creature'] ?? [];
    $world = $state['world'] ?? [];
    $analyzed = (bool) ($creature['analyzed'] ?? false);
    $mutated = (bool) ($creature['mutated'] ?? false);
    $missionDone = (bool) ($creature['mission_done'] ?? false);
    $discovered = (bool) ($creature['discovered'] ?? false) || (bool) ($world['recon_done'] ?? false);
    $exhibits = $state['museum']['exhibits'] ?? [];

    $stages = [
        ['key' => 'arrivee', 'label' => 'Arrivée', 'done' => true],
        ['key' => 'decouvrir', 'label' => 'Découvrir', 'done' => $discovered],
        ['key' => 'analyser', 'label' => 'Analyser', 'done' => $analyzed],
        ['key' => 'evoluer', 'label' => 'Évoluer', 'done' => $mutated],
        ['key' => 'mission', 'label' => 'Mission', 'done' => $missionDone],
        ['key' => 'musee', 'label' => 'Musée', 'done' => !empty($exhibits)],
    ];

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
        $current = 'Boucle complète — nouvelle exploration';
    }

    return [
        'stages' => $stages,
        'current' => $current,
        'completed' => count(array_filter($stages, static fn (array $stage): bool => $stage['done'])),
        'total' => count($stages),
        'loop_complete' => $allDone,
    ];
}

function genesis_mutation_options(array $creature): array
{
    $key = (string) ($creature['species_key'] ?? '');
    $species = $key !== '' ? genesis_species($key) : null;
    if ($species === null) {
        return [];
    }

    return $species['mutations'] ?? [];
}

function genesis_creature_needs_recovery(array $creature): bool
{
    return in_array((string) ($creature['status'] ?? 'prête'), ['blessée', 'épuisée'], true);
}

function genesis_recover_cost(array $creature): int
{
    return ((string) ($creature['status'] ?? '')) === 'blessée' ? 2 : 1;
}

function genesis_cycle_complete(array $state): bool
{
    $creature = $state['creature'] ?? [];

    return !empty($creature['mission_done'])
        && genesis_creature_is_exhibited($state, $creature);
}

/**
 * Secondary CTAs shown under the primary action (2e boucle, croisement, etc.).
 *
 * @return list<array<string, mixed>>
 */
function genesis_secondary_actions(array $state): array
{
    $actions = [];
    $creature = $state['creature'] ?? [];
    $resources = $state['resources'] ?? [];
    $organic = (int) ($resources['organic'] ?? 0);
    $logistics = (int) ($resources['logistics'] ?? 0);
    $analyzedCount = count(genesis_analyzed_creatures($state));
    $status = (string) ($creature['status'] ?? 'prête');

    if (genesis_creature_needs_recovery($creature) && !empty($creature['analyzed'])) {
        $cost = genesis_recover_cost($creature);
        $actions[] = [
            'type' => 'post',
            'action' => 'recover',
            'label' => sprintf(
                'Soigner au vivarium (−%d organique) · %s → prête',
                $cost,
                $status
            ),
            'screen' => 'expedition',
            'disabled' => $organic < $cost,
        ];
    }

    if (genesis_cycle_complete($state) || (!empty($creature['mission_done']) && !empty($creature['analyzed']))) {
        $actions[] = [
            'type' => 'post',
            'action' => 'new_exploration',
            'label' => 'Nouvelle exploration (+1 logistique, drones reconfig)',
            'screen' => 'world',
            'disabled' => false,
        ];
    }

    if ($analyzedCount >= 2 && $organic >= 1) {
        $actions[] = [
            'type' => 'post',
            'action' => 'cross',
            'label' => 'Croiser deux espèces analysées (−1 organique)',
            'screen' => 'expedition',
            'disabled' => false,
        ];
    }

    if (
        !empty($creature['analyzed'])
        && !empty($creature['mission_done'])
        && ($creature['status'] ?? '') === 'prête'
        && genesis_creature_is_exhibited($state, $creature)
    ) {
        $actions[] = [
            'type' => 'post',
            'action' => 'remission',
            'label' => 'Renvoyer en mission (risque)',
            'screen' => 'archives',
            'disabled' => false,
        ];
    }

    // Avoid duplicating the primary action
    $primary = genesis_primary_action($state);
    $primaryKey = ($primary['action'] ?? '') . '|' . ($primary['type'] ?? '');

    return array_values(array_filter(
        $actions,
        static function (array $action) use ($primaryKey): bool {
            $key = ($action['action'] ?? '') . '|' . ($action['type'] ?? '');

            return $key !== $primaryKey;
        }
    ));
}

function genesis_primary_action(array $state): array
{
    $creature = $state['creature'] ?? [];
    $world = $state['world'] ?? [];
    $resources = $state['resources'] ?? [];
    $analyzed = (bool) ($creature['analyzed'] ?? false);
    $mutated = (bool) ($creature['mutated'] ?? false);
    $missionDone = (bool) ($creature['mission_done'] ?? false);
    $discovered = (bool) ($creature['discovered'] ?? false);
    $logistics = (int) ($resources['logistics'] ?? 0);
    $organic = (int) ($resources['organic'] ?? 0);
    $selectedZone = $world['selected_zone'] ?? null;
    $analyzedCount = count(genesis_analyzed_creatures($state));
    $status = (string) ($creature['status'] ?? 'prête');

    // Need a zone before first recon of a new signal
    if (!$discovered) {
        if ($selectedZone === null || $selectedZone === '') {
            return [
                'type' => 'link',
                'href' => '?screen=world',
                'label' => 'Choisir une zone à explorer',
                'screen' => 'world',
                'hint' => 'Chaque zone cache une espèce différente.',
            ];
        }

        if ($logistics < 1) {
            return [
                'type' => 'link',
                'href' => '?screen=institute',
                'label' => 'Logistique insuffisante',
                'screen' => 'institute',
                'hint' => 'Exposez au musée ou terminez une boucle pour récupérer de la logistique.',
            ];
        }

        $zone = genesis_zone($state, (string) $selectedZone);

        return [
            'type' => 'post',
            'action' => 'recon',
            'label' => 'Vol de reconnaissance (−1 logistique)',
            'screen' => 'world',
            'hint' => 'Zone : ' . ($zone['name'] ?? $selectedZone) . ' · risque ' . ($zone['risk'] ?? '?'),
        ];
    }

    if (!$analyzed) {
        return [
            'type' => 'post',
            'action' => 'analyze',
            'label' => 'Analyser l’espèce au laboratoire',
            'screen' => 'expedition',
            'hint' => 'Débloque stats, mutations et missions.',
        ];
    }

    if (!$mutated) {
        if ($organic < 1) {
            return [
                'type' => 'link',
                'href' => '?screen=expedition',
                'label' => 'Organique insuffisant',
                'screen' => 'expedition',
                'hint' => 'Les mutations coûtent 1 organique.',
            ];
        }

        $speciesOpts = genesis_mutation_options($creature);
        if ($speciesOpts === []) {
            $speciesOpts = [
                'stats' => [
                    'label' => 'Stabilisation stats',
                    'description' => 'Force +2 · pureté −10',
                ],
                'ability' => [
                    'label' => 'Éveil de capacité',
                    'description' => 'Nouvelle capacité · pureté −15',
                ],
            ];
        }

        return [
            'type' => 'choices',
            'action' => 'mutate',
            'label' => 'Choisir une mutation (−1 organique)',
            'screen' => 'expedition',
            'choices' => [
                'stats' => $speciesOpts['stats']['label'] ?? 'Mutation stats',
                'ability' => $speciesOpts['ability']['label'] ?? 'Mutation capacité',
            ],
            'choice_param' => 'mutation_choice',
            'choice_meta' => $speciesOpts,
            'hint' => 'Stats = puissance brute · Capacité = signature unique. Pureté ↓ dans les deux cas.',
        ];
    }

    // Heal before (re)mission if not ready and mission not yet done this cycle
    if (!$missionDone && genesis_creature_needs_recovery($creature)) {
        $cost = genesis_recover_cost($creature);
        if ($organic < $cost) {
            return [
                'type' => 'link',
                'href' => '?screen=expedition',
                'label' => 'Organique insuffisant pour soigner',
                'screen' => 'expedition',
                'hint' => sprintf('Soin de « %s » : %d organique requis.', $status, $cost),
            ];
        }

        return [
            'type' => 'post',
            'action' => 'recover',
            'label' => sprintf('Soigner (−%d organique) avant mission', $cost),
            'screen' => 'expedition',
            'hint' => 'Blessée / épuisée : moins efficace, berceau fragile si blessure.',
        ];
    }

    if (!$missionDone) {
        $penalty = match ($status) {
            'blessée' => ' (pénalité forte)',
            'épuisée' => ' (pénalité légère)',
            default => '',
        };

        return [
            'type' => 'post',
            'action' => 'mission',
            'label' => 'Envoyer en mission (risque)' . $penalty,
            'screen' => 'archives',
            'hint' => 'Succès, épuisement ou blessure — les stats influencent les chances.',
        ];
    }

    if (!genesis_creature_is_exhibited($state, $creature)) {
        return [
            'type' => 'post',
            'action' => 'exhibit',
            'label' => 'Exposer au musée',
            'screen' => 'archives',
            'hint' => 'Ferme la boucle et rend +1 logistique pour la suite.',
        ];
    }

    // Heal after exhibit if still not ready
    if (genesis_creature_needs_recovery($creature)) {
        $cost = genesis_recover_cost($creature);
        if ($organic >= $cost) {
            return [
                'type' => 'post',
                'action' => 'recover',
                'label' => sprintf('Soigner au vivarium (−%d organique)', $cost),
                'screen' => 'expedition',
                'hint' => 'Retrouver le statut prête avant une nouvelle mission.',
            ];
        }
    }

    // Loop complete → start second loop cleanly
    return [
        'type' => 'post',
        'action' => 'new_exploration',
        'label' => 'Lancer une 2ᵉ exploration',
        'screen' => 'world',
        'hint' => $analyzedCount >= 2
            ? 'Ou croisez deux espèces analysées depuis les actions secondaires.'
            : 'Choisissez une autre zone pour enrichir le bestiaire.',
    ];
}

function genesis_creature_is_exhibited(array $state, array $creature): bool
{
    $name = (string) ($creature['name'] ?? '');
    foreach ($state['museum']['exhibits'] ?? [] as $exhibit) {
        if (is_array($exhibit) && ($exhibit['creature_id'] ?? null) === ($creature['id'] ?? null)) {
            return true;
        }
        if (is_array($exhibit) && str_contains((string) ($exhibit['label'] ?? ''), $name) && $name !== '') {
            return true;
        }
    }

    return false;
}

function genesis_screen_state(string $screen, array $state): array
{
    $creature = $state['creature'] ?? [];
    $world = $state['world'] ?? [];
    $resources = $state['resources'] ?? [];
    $drones = $state['drones'] ?? [];
    $primaryAction = genesis_primary_action($state);
    $analyzed = (bool) ($creature['analyzed'] ?? false);
    $mutated = (bool) ($creature['mutated'] ?? false);
    $missionDone = (bool) ($creature['mission_done'] ?? false);
    $exhibits = $state['museum']['exhibits'] ?? [];
    $selectedZone = genesis_zone($state, $world['selected_zone'] ?? null);
    $zones = $world['zones'] ?? [];

    $zoneFacts = [];
    foreach ($zones as $zone) {
        $mark = !empty($zone['explored']) ? '✓' : '·';
        $sel = ($world['selected_zone'] ?? null) === ($zone['id'] ?? '') ? ' ←' : '';
        $speciesHint = '';
        if (!empty($zone['explored'])) {
            $sk = (string) ($zone['species_key'] ?? '');
            $sp = $sk !== '' ? genesis_species($sk) : null;
            if ($sp !== null) {
                $speciesHint = ' · ' . ($sp['species'] ?? '');
            }
        }
        $zoneFacts[] = sprintf(
            '%s %s — risque %s%s%s',
            $mark,
            $zone['name'] ?? '?',
            $zone['risk'] ?? '?',
            $speciesHint,
            $sel
        );
    }

    $screens = [
        'institute' => [
            'title' => 'Institut',
            'subtitle' => 'La décision avant le système.',
            'primary' => 'Compose le regard : drones d’abord, murs ensuite.',
            'objective' => 'Préparer une reconnaissance pour découvrir de nouvelles espèces.',
            'facts' => [
                'Monde natal : ' . ($world['name'] ?? '—'),
                'Parc : ' . genesis_drone_summary($drones),
                'Actes : ' . genesis_resource_label($resources),
                'Musée : ' . (empty($exhibits) ? 'collection vide' : count($exhibits) . ' pièce(s)'),
                'Bestiaire : ' . count(genesis_analyzed_creatures($state)) . ' espèce(s) analysée(s)',
            ],
        ],
        'world' => [
            'title' => 'Monde natal',
            'subtitle' => 'Choisir où lire le vivant.',
            'primary' => $selectedZone !== null
                ? 'Zone sélectionnée : ' . ($selectedZone['name'] ?? '—') . ' — prête pour un Vol.'
                : 'Trois zones : quelle lecture en premier (ou ensuite) ?',
            'objective' => 'Décider où envoyer les sondes. Chaque zone cache une espèce différente. Les zones déjà explorées restent rejouables.',
            'facts' => array_merge(
                [
                    'Risque local : ' . ($world['risk'] ?? 'inconnu'),
                    'Signal vivant : ' . ($world['signal'] ?? 'non détecté'),
                    'Logistique dispo : ' . (int) ($resources['logistics'] ?? 0),
                ],
                $zoneFacts
            ),
        ],
        'expedition' => [
            'title' => 'Expédition',
            'subtitle' => 'Analyser, muter, croiser, préparer la mission.',
            'primary' => $mutated
                ? 'La créature a muté — prête pour le terrain.'
                : ($analyzed
                    ? 'Espèce analysée : choisis une mutation (stats ou capacité).'
                    : 'Un spécimen attend l’analyse au laboratoire.'),
            'objective' => 'Connaître les caractéristiques, tenter une évolution, éventuellement croiser deux analyses.',
            'facts' => [
                'Créature : ' . genesis_creature_label($creature),
                'Analyse : ' . ($analyzed ? 'complète' : 'requise'),
                'Mutation : ' . ($mutated ? 'appliquée' : 'à tenter'),
                'Mission : ' . ($missionDone ? 'réalisée' : 'à lancer'),
                'Organique : ' . (int) ($resources['organic'] ?? 0) . ' · Prélèvements : ' . (int) ($resources['samples'] ?? 0),
                'Analysées (croisement) : ' . count(genesis_analyzed_creatures($state)),
            ],
        ],
        'archives' => [
            'title' => 'Musée',
            'subtitle' => 'Éprouver en mission, exposer le remarquable.',
            'primary' => empty($exhibits)
                ? 'Aucune pièce encore — une mission peut justifier l’exposition.'
                : 'La collection s’enrichit : ' . count($exhibits) . ' pièce(s).',
            'objective' => 'Envoyer la créature en mission, puis juger ce qui mérite le musée.',
            'facts' => [
                'Statut : ' . ($creature['status'] ?? 'prête'),
                'Capacités : ' . (empty($creature['abilities']) ? 'aucune' : implode(' · ', $creature['abilities'])),
                'Pureté génétique : ' . (int) ($creature['purity'] ?? 100) . ' %',
                'Mémoire : ' . count($state['heritage']['memories'] ?? []) . ' entrée(s)',
                'Baie de projection : verrouillée',
            ],
        ],
    ];

    $screens[$screen]['cta'] = $primaryAction;
    $screens[$screen]['secondary'] = genesis_secondary_actions($state);
    $screens[$screen]['progress'] = genesis_progress($state);
    $screens[$screen]['hint'] = (string) ($primaryAction['hint'] ?? '');
    $screens[$screen]['creature'] = [
        'id' => $creature['id'] ?? 'c1',
        'name' => $creature['name'] ?? 'Sans nom',
        'species' => $creature['species'] ?? 'Inconnue',
        'rarity' => $creature['rarity'] ?? 'inconnue',
        'stats' => $creature['stats'] ?? [],
        'abilities' => $creature['abilities'] ?? [],
        'purity' => (int) ($creature['purity'] ?? 100),
        'status' => $creature['status'] ?? 'prête',
        'analyzed' => $analyzed,
        'mutated' => $mutated,
        'parents' => $creature['parents'] ?? null,
    ];
    $screens[$screen]['campus'] = array_values($state['campus'] ?? []);
    $screens[$screen]['resources'] = $resources;
    $screens[$screen]['drones'] = $drones;
    $screens[$screen]['zones'] = array_values($zones);
    $screens[$screen]['selected_zone'] = $world['selected_zone'] ?? null;
    $screens[$screen]['mutation_options'] = genesis_mutation_options($creature);
    $screens[$screen]['analyzed_count'] = count(genesis_analyzed_creatures($state));
    $screens[$screen]['exhibits'] = $exhibits;
    $screens[$screen]['bestiary'] = array_values($state['bestiary'] ?? []);
    $screens[$screen]['genesis'] = $state['ui']['genesis'] ?? [
        'marker' => 'Observation',
        'message' => 'Silence.',
    ];

    return $screens[$screen];
}

function genesis_creature_label(array $creature): string
{
    $abilities = empty($creature['abilities'])
        ? 'sans capacité'
        : implode(', ', $creature['abilities']);
    $stats = $creature['stats'] ?? [];
    $analyzed = (bool) ($creature['analyzed'] ?? false);

    if (!$analyzed) {
        return sprintf(
            '%s — espèce inconnue (non analysée)',
            $creature['name'] ?? 'Sans nom'
        );
    }

    $parents = '';
    if (!empty($creature['parents']) && is_array($creature['parents'])) {
        $parents = sprintf(
            ' · hybride (%s × %s)',
            $creature['parents'][0] ?? '?',
            $creature['parents'][1] ?? '?'
        );
    }

    return sprintf(
        '%s — %s · %s · F%d V%d R%d I%d · pureté %d%% · %s%s',
        $creature['name'] ?? 'Sans nom',
        $creature['species'] ?? '—',
        $creature['rarity'] ?? '—',
        (int) ($stats['force'] ?? 0),
        (int) ($stats['vitesse'] ?? 0),
        (int) ($stats['resistance'] ?? 0),
        (int) ($stats['intelligence'] ?? 0),
        (int) ($creature['purity'] ?? 100),
        $abilities,
        $parents
    );
}

/** @deprecated Use genesis_creature_label */
function genesis_branch_label(array $branchOrCreature): string
{
    return genesis_creature_label($branchOrCreature);
}

function genesis_roll_mission_outcome(array $state): string
{
    $forced = $state['ui']['force_mission_outcome'] ?? null;

    if (in_array($forced, ['success', 'injured', 'exhausted'], true)) {
        return $forced;
    }

    if ($forced === 'scar') {
        return 'injured';
    }

    $creature = $state['creature'] ?? [];
    $stats = $creature['stats'] ?? [];
    $power = (int) ($stats['force'] ?? 0)
        + (int) ($stats['vitesse'] ?? 0)
        + (int) ($stats['resistance'] ?? 0)
        + (int) ($stats['intelligence'] ?? 0);

    $status = $creature['status'] ?? 'prête';
    $penalty = match ($status) {
        'blessée' => 20,
        'épuisée' => 10,
        default => 0,
    };

    $roll = random_int(1, 100);
    $successChance = max(15, min(75, 35 + $power - $penalty));

    if ($roll <= $successChance) {
        return 'success';
    }

    return $roll <= $successChance + 20 ? 'exhausted' : 'injured';
}

// ---------------------------------------------------------------------------
// Actions
// ---------------------------------------------------------------------------

function genesis_apply_action(array &$state, string $action, array $payload = []): void
{
    switch ($action) {
        case 'select_zone':
            $zoneId = (string) ($payload['zone_id'] ?? '');
            $zone = genesis_zone($state, $zoneId);
            if ($zone === null) {
                $state['ui']['latest_event'] = [
                    'label' => 'Zone invalide',
                    'message' => 'Cette zone n’existe pas sur la carte du berceau.',
                ];
                break;
            }

            $state['world']['selected_zone'] = $zoneId;
            $state['world']['risk'] = $zone['risk'] ?? 'inconnu';
            $state['ui']['latest_event'] = [
                'label' => 'Zone choisie',
                'message' => sprintf(
                    '%s sélectionnée (risque %s). Les drones attendent l’ordre de recon.',
                    $zone['name'] ?? $zoneId,
                    $zone['risk'] ?? '?'
                ),
            ];
            genesis_set_voice(
                $state,
                'Observation',
                sprintf(
                    '%s : risque %s. Une lecture de zone décidera si le vivant mérite le labo.',
                    $zone['name'] ?? $zoneId,
                    $zone['risk'] ?? '?'
                )
            );
            $state['screen'] = 'world';
            break;

        case 'recon':
            if ((int) ($state['resources']['logistics'] ?? 0) < 1) {
                $state['ui']['latest_event'] = [
                    'label' => 'Vol impossible',
                    'message' => 'Sans logistique, les sondes restent au sol.',
                ];
                genesis_set_voice($state, 'Absence', 'La carte reste noire. Le coût d’une lecture n’a pas été payé.');
                $state['screen'] = 'world';
                break;
            }

            $zoneId = (string) ($state['world']['selected_zone'] ?? '');
            // Backward-compatible default: first zone if none selected
            if ($zoneId === '' || genesis_zone($state, $zoneId) === null) {
                $zoneId = 'plaine';
                $state['world']['selected_zone'] = $zoneId;
            }

            $zone = genesis_zone($state, $zoneId);
            $speciesKey = (string) ($zone['species_key'] ?? 'thermidé');
            $species = genesis_species($speciesKey);
            if ($species === null) {
                $speciesKey = 'thermidé';
                $species = genesis_species($speciesKey);
            }

            // Archive previous active specimen if we already had one discovered
            if (!empty($state['creature']['discovered']) || !empty($state['creature']['analyzed'])) {
                genesis_archive_active_if_ready($state);
                $newId = genesis_next_creature_id($state);
                $state['creature'] = genesis_blank_creature($newId);
            }

            $state['resources']['logistics'] = max(0, (int) $state['resources']['logistics'] - 1);
            $state['drones']['recon']['deployed'] = (int) ($state['drones']['recon']['deployed'] ?? 0) + 1;
            $state['drones']['recon']['ready'] = max(0, (int) ($state['drones']['recon']['ready'] ?? 0) - 1);

            $state['world']['recon_done'] = true;
            $state['world']['risk'] = $zone['risk'] ?? 'modéré';
            $zoneName = $zone['name'] ?? 'Zone';
            if (!in_array($zoneName, $state['world']['known_zones'] ?? [], true)) {
                $state['world']['known_zones'][] = $zoneName;
            }
            $state['world']['signal'] = $species['signal'] ?? 'Signal vivant';
            $state['world']['zones'][$zoneId]['explored'] = true;

            $state['creature']['discovered'] = true;
            $state['creature']['zone_id'] = $zoneId;
            $state['creature']['species_key'] = $speciesKey;
            $state['creature']['name'] = 'Spécimen — ' . ($species['species'] ?? 'Inconnu');
            $state['creature']['analyzed'] = false;
            $state['creature']['mutated'] = false;
            $state['creature']['mission_done'] = false;
            $state['creature']['status'] = 'prête';
            $state['creature']['purity'] = 100;
            $state['creature']['abilities'] = [];
            $state['creature']['parents'] = null;
            $state['creature']['species'] = 'Inconnue';
            $state['creature']['rarity'] = 'inconnue';
            $state['creature']['stats'] = [
                'force' => 0,
                'vitesse' => 0,
                'resistance' => 0,
                'intelligence' => 0,
            ];

            $state['ui']['latest_event'] = [
                'label' => 'Découverte',
                'message' => sprintf(
                    'Les sondes ont lu %s. Signal : %s. Une créature attend d’être analysée.',
                    $zoneName,
                    $species['signal'] ?? 'vivant'
                ),
            ];
            genesis_set_voice(
                $state,
                'Observation',
                sprintf(
                    '%s : risque %s. %s — ce n’est pas encore une fiche, c’est une invitation.',
                    $zoneName,
                    $zone['risk'] ?? '?',
                    $species['signal'] ?? 'Signal'
                )
            );
            $state['screen'] = 'expedition';
            break;

        case 'analyze':
            if (empty($state['creature']['discovered'])) {
                $state['ui']['latest_event'] = [
                    'label' => 'Analyse impossible',
                    'message' => 'Aucun signal à analyser. Lancez d’abord une reconnaissance.',
                ];
                genesis_set_voice($state, 'Absence', 'Le labo attend un spécimen. Le berceau n’a pas encore parlé.');
                $state['screen'] = 'world';
                break;
            }

            $speciesKey = (string) ($state['creature']['species_key'] ?? 'thermidé');
            $species = genesis_species($speciesKey) ?? genesis_species('thermidé');

            $state['creature']['analyzed'] = true;
            $state['creature']['discovered'] = true;
            $state['creature']['species'] = $species['species'];
            $state['creature']['species_key'] = $speciesKey;
            $state['creature']['rarity'] = $species['rarity'];
            $state['creature']['name'] = $species['name'];
            $state['creature']['stats'] = $species['stats'];
            $state['creature']['abilities'] = $species['abilities'];
            $state['resources']['samples'] = (int) ($state['resources']['samples'] ?? 0) + 1;
            $state['drones']['sample']['deployed'] = (int) ($state['drones']['sample']['deployed'] ?? 0) + 1;

            // Keep in bestiary snapshot
            $state['bestiary'][(string) $state['creature']['id']] = $state['creature'];

            $state['ui']['latest_event'] = [
                'label' => 'Analyse',
                'message' => sprintf(
                    'Espèce %s cataloguée. Stats connues, mutations et missions débloquées. Un prélèvement rejoint les stocks.',
                    $species['species']
                ),
            ];
            genesis_set_voice(
                $state,
                'Hypothèse',
                sprintf(
                    '%s, rareté %s : profil lisible. Deux chemins de mutation s’ouvrent — stats ou capacité — au prix de la pureté.',
                    $species['species'],
                    $species['rarity']
                )
            );
            $state['screen'] = 'expedition';
            break;

        case 'mutate':
        case 'transform': // legacy alias
            if ((int) ($state['resources']['organic'] ?? 0) < 1) {
                $state['ui']['latest_event'] = [
                    'label' => 'Mutation refusée',
                    'message' => 'Sans matière organique, le laboratoire ne fixe rien.',
                ];
                genesis_set_voice($state, 'Seuil', 'Le vivant ne se force pas à vide. L’acte attend un carburant.');
                $state['screen'] = 'expedition';
                break;
            }

            if (!(bool) ($state['creature']['analyzed'] ?? false)) {
                $state['ui']['latest_event'] = [
                    'label' => 'Mutation impossible',
                    'message' => 'Sans analyse, toute mutation reste aveugle.',
                ];
                genesis_set_voice($state, 'Seuil', 'On ne fait pas évoluer ce qu’on n’a pas encore lu.');
                $state['screen'] = 'expedition';
                break;
            }

            if ((bool) ($state['creature']['mutated'] ?? false)) {
                $state['ui']['latest_event'] = [
                    'label' => 'Déjà mutée',
                    'message' => 'Cette créature a déjà subi une mutation dans ce cycle.',
                ];
                $state['screen'] = 'expedition';
                break;
            }

            $choice = (string) ($payload['mutation_choice'] ?? 'ability');
            if (!in_array($choice, ['stats', 'ability'], true)) {
                $choice = 'ability';
            }

            $options = genesis_mutation_options($state['creature']);
            $option = $options[$choice] ?? $options['ability'] ?? null;
            if ($option === null) {
                // Fallback for hybrids without catalog mutations
                $option = [
                    'label' => 'Stabilisation forcée',
                    'description' => 'Force +2 · pureté −12',
                    'stats' => ['force' => 2],
                    'ability' => $choice === 'ability' ? 'Peau de fardeau' : null,
                    'purity_cost' => 12,
                    'name_suffix' => 'altéré',
                    'rarity' => 'peu commune',
                ];
            }

            $state['resources']['organic'] = max(0, (int) $state['resources']['organic'] - 1);

            foreach ($option['stats'] ?? [] as $stat => $delta) {
                $state['creature']['stats'][$stat] = (int) ($state['creature']['stats'][$stat] ?? 0) + (int) $delta;
            }

            if (!empty($option['ability'])) {
                if (!in_array($option['ability'], $state['creature']['abilities'] ?? [], true)) {
                    $state['creature']['abilities'][] = $option['ability'];
                    $state['creature']['abilities'] = array_values(array_slice($state['creature']['abilities'], 0, 2));
                }
            }

            $cost = (int) ($option['purity_cost'] ?? 15);
            $state['creature']['purity'] = max(40, (int) ($state['creature']['purity'] ?? 100) - $cost);
            $state['creature']['mutated'] = true;
            $baseName = explode(' ', (string) ($state['creature']['species'] ?? 'Créature'))[0];
            $speciesName = (string) ($state['creature']['species'] ?? 'Créature');
            $state['creature']['name'] = $speciesName . ' ' . ($option['name_suffix'] ?? 'muté');
            if (!empty($option['rarity'])) {
                $state['creature']['rarity'] = $option['rarity'];
            }

            $state['bestiary'][(string) $state['creature']['id']] = $state['creature'];

            $state['heritage']['memories'][] = sprintf(
                'Mutation « %s » sur %s — pureté en baisse.',
                $option['label'] ?? $choice,
                $speciesName
            );
            $state['ui']['latest_event'] = [
                'label' => 'Mutation',
                'message' => sprintf(
                    '%s. Pureté génétique réduite. Risque accepté.',
                    $option['description'] ?? 'Mutation appliquée'
                ),
            ];
            genesis_set_voice(
                $state,
                'Seuil',
                'Mutation inscrite. Ce qui suit n’est plus une théorie : la mission tranchera.'
            );
            $state['screen'] = 'archives';
            break;

        case 'mission':
        case 'test': // legacy alias
            if (!(bool) ($state['creature']['analyzed'] ?? false)) {
                $state['ui']['latest_event'] = [
                    'label' => 'Mission impossible',
                    'message' => 'Sans analyse, envoyer une créature est une imprudence.',
                ];
                $state['screen'] = 'expedition';
                break;
            }

            $outcome = genesis_roll_mission_outcome($state);
            $state['creature']['mission_done'] = true;
            $state['ui']['force_mission_outcome'] = null;

            if ($outcome === 'injured') {
                $state['creature']['status'] = 'blessée';
                $state['world']['stable'] = false;
                $state['heritage']['memories'][] = 'La mission a blessé la créature : elle tient, mais sera moins efficace jusqu’à récupération.';
                $state['ui']['latest_event'] = [
                    'label' => 'Mission — blessure',
                    'message' => 'La créature revient. Elle a tenu — blessée. Soignez-la au vivarium (2 organique) ou exposez-la telle quelle.',
                ];
                genesis_set_voice(
                    $state,
                    'Tension',
                    'Statut : blessée. La preuve existe, le prix aussi. Le vivarium peut refermer la plaie.'
                );
            } elseif ($outcome === 'exhausted') {
                $state['creature']['status'] = 'épuisée';
                $state['world']['stable'] = true;
                $state['resources']['samples'] = (int) ($state['resources']['samples'] ?? 0) + 1;
                $state['heritage']['memories'][] = 'La mission a épuisé la créature : récupération nécessaire avant un nouvel envoi.';
                $state['ui']['latest_event'] = [
                    'label' => 'Mission — épuisement',
                    'message' => 'Objectif atteint, créature à bout (+1 prélèvement). Soin vivarium : 1 organique.',
                ];
                genesis_set_voice(
                    $state,
                    'Observation',
                    'Succès partiel. L’épuisement limite les prochains envois — le musée peut déjà s’intéresser au spécimen.'
                );
            } else {
                $state['creature']['status'] = 'prête';
                $state['world']['stable'] = true;
                $state['resources']['organic'] = (int) ($state['resources']['organic'] ?? 0) + 1;
                $state['heritage']['memories'][] = 'La mission a confirmé que la créature tient dans le réel.';
                $state['ui']['latest_event'] = [
                    'label' => 'Mission — succès',
                    'message' => 'La créature a tenu sans se briser. +1 organique récupéré sur le terrain.',
                ];
                genesis_set_voice(
                    $state,
                    'Observation',
                    'Tenue confirmée. Les stats et la capacité tiennent sur le terrain.'
                );
            }

            $state['bestiary'][(string) $state['creature']['id']] = $state['creature'];
            $state['screen'] = 'archives';
            break;

        case 'recover':
            if (!genesis_creature_needs_recovery($state['creature'] ?? [])) {
                $state['ui']['latest_event'] = [
                    'label' => 'Rien à soigner',
                    'message' => 'La créature active est déjà prête.',
                ];
                genesis_set_voice($state, 'Observation', 'Aucun trauma lisible. Le vivarium attend un autre patient.');
                $state['screen'] = 'expedition';
                break;
            }

            $cost = genesis_recover_cost($state['creature']);
            if ((int) ($state['resources']['organic'] ?? 0) < $cost) {
                $state['ui']['latest_event'] = [
                    'label' => 'Soin impossible',
                    'message' => sprintf('Il faut %d organique pour ce soin.', $cost),
                ];
                genesis_set_voice($state, 'Seuil', 'Le vivarium refuse le vide. Le vivant ne guérit pas sans matière.');
                $state['screen'] = 'expedition';
                break;
            }

            $before = (string) ($state['creature']['status'] ?? 'prête');
            $state['resources']['organic'] = max(0, (int) $state['resources']['organic'] - $cost);
            $state['creature']['status'] = 'prête';
            if ($before === 'blessée') {
                $state['world']['stable'] = true;
            }
            $state['bestiary'][(string) $state['creature']['id']] = $state['creature'];
            $state['heritage']['memories'][] = sprintf(
                'Soin vivarium : %s repasse prête (−%d organique).',
                $state['creature']['name'] ?? 'créature',
                $cost
            );
            $state['ui']['latest_event'] = [
                'label' => 'Récupération',
                'message' => sprintf(
                    'Statut %s → prête. Coût : %d organique. La créature peut repartir en mission.',
                    $before,
                    $cost
                ),
            ];
            genesis_set_voice(
                $state,
                'Observation',
                'Intégrité restaurée. Ce qui était une plaie redevient une option.'
            );
            $state['screen'] = 'expedition';
            break;

        case 'remission':
            if (empty($state['creature']['analyzed'])) {
                $state['ui']['latest_event'] = [
                    'label' => 'Mission impossible',
                    'message' => 'Sans créature analysée, pas de renvoi.',
                ];
                break;
            }
            if (genesis_creature_needs_recovery($state['creature'])) {
                $state['ui']['latest_event'] = [
                    'label' => 'Trop fragile',
                    'message' => 'Soignez d’abord la créature (vivarium) avant un nouvel envoi.',
                ];
                genesis_set_voice($state, 'Seuil', 'Renvoyer une blessée, c’est accepter une défaite avant l’acte.');
                $state['screen'] = 'expedition';
                break;
            }
            // Reset mission flag then reuse mission logic
            $state['creature']['mission_done'] = false;
            genesis_apply_action($state, 'mission', $payload);
            break;

        case 'new_exploration':
            if (!empty($state['creature']['discovered']) || !empty($state['creature']['analyzed'])) {
                genesis_archive_active_if_ready($state);
            }

            $newId = genesis_next_creature_id($state);
            $state['creature'] = genesis_blank_creature($newId);
            $state['world']['selected_zone'] = null;
            $state['world']['signal'] = null;
            $state['world']['risk'] = 'inconnu';
            // Keep known_zones & recon history; resupply lightly for second loop
            $state['resources']['logistics'] = (int) ($state['resources']['logistics'] ?? 0) + 1;
            $state['drones']['recon']['ready'] = max(
                (int) ($state['drones']['recon']['ready'] ?? 0),
                1
            );
            $state['drones']['sample']['ready'] = max(
                (int) ($state['drones']['sample']['ready'] ?? 0),
                1
            );

            $state['ui']['latest_event'] = [
                'label' => 'Nouvelle exploration',
                'message' => 'Bestiaire conservé. +1 logistique, drones reconfigurés. Choisissez une zone encore (ou déjà) lue.',
            ];
            genesis_set_voice(
                $state,
                'Observation',
                'Le berceau n’est pas fini. Une autre zone peut encore surprendre l’Institut.'
            );
            $state['screen'] = 'world';
            break;

        case 'focus_creature':
            $targetId = (string) ($payload['creature_id'] ?? '');
            $pool = genesis_analyzed_creatures($state);
            if ($targetId === '' || !isset($pool[$targetId])) {
                // Also search full bestiary
                $fromBestiary = $state['bestiary'][$targetId] ?? null;
                if (!is_array($fromBestiary)) {
                    $state['ui']['latest_event'] = [
                        'label' => 'Cible introuvable',
                        'message' => 'Cette créature n’est pas dans le bestiaire.',
                    ];
                    break;
                }
                $focus = $fromBestiary;
            } else {
                $focus = $pool[$targetId];
            }

            if (!empty($state['creature']['id']) && ($state['creature']['id'] ?? '') !== $targetId) {
                genesis_archive_active_if_ready($state);
            }
            $state['creature'] = $focus;
            $state['creature']['id'] = $targetId !== '' ? $targetId : ($focus['id'] ?? 'c1');
            $state['ui']['latest_event'] = [
                'label' => 'Focus',
                'message' => sprintf(
                    '%s est maintenant la créature active.',
                    $state['creature']['name'] ?? 'Spécimen'
                ),
            ];
            genesis_set_voice(
                $state,
                'Observation',
                'Attention reportée sur un autre visage du bestiaire.'
            );
            $state['screen'] = 'expedition';
            break;

        case 'exhibit':
        case 'inscribe': // legacy alias
            $creature = $state['creature'] ?? [];
            $label = genesis_creature_label($creature);
            $piece = [
                'creature_id' => $creature['id'] ?? null,
                'label' => $label,
                'species' => $creature['species'] ?? '—',
                'rarity' => $creature['rarity'] ?? '—',
                'status' => $creature['status'] ?? 'prête',
                'purity' => (int) ($creature['purity'] ?? 100),
                'note' => !empty($creature['parents'])
                    ? 'Hybride unique — parents informatifs seulement.'
                    : 'Pièce remarquable de la collection.',
                'parents' => $creature['parents'] ?? null,
            ];

            $already = genesis_creature_is_exhibited($state, $creature);
            if (!$already) {
                $state['museum']['exhibits'][] = $piece;
                $state['resources']['logistics'] = (int) ($state['resources']['logistics'] ?? 0) + 1;
            }

            $state['heritage']['memories'][] = 'Exposition solennelle : ' . ($creature['name'] ?? 'spécimen') . ' entre au musée.';

            $state['ui']['latest_event'] = [
                'label' => 'Musée',
                'message' => $already
                    ? 'Cette pièce est déjà exposée.'
                    : sprintf(
                        '%s rejoint la collection. +1 logistique. Pas de lignée : une pièce parmi le bestiaire.',
                        $creature['name'] ?? 'La créature'
                    ),
            ];
            genesis_set_voice(
                $state,
                'Continuité',
                sprintf(
                    'Bestiaire enrichi — %s. Une nouvelle exploration peut commencer.',
                    $creature['species'] ?? 'espèce'
                )
            );
            $state['screen'] = 'archives';
            break;

        case 'cross':
            if ((int) ($state['resources']['organic'] ?? 0) < 1) {
                $state['ui']['latest_event'] = [
                    'label' => 'Croisement refusé',
                    'message' => 'Sans matière organique, aucun hybride ne naît.',
                ];
                genesis_set_voice($state, 'Seuil', 'Le croisement exige un carburant. L’acte attend.');
                $state['screen'] = 'expedition';
                break;
            }

            $analyzed = array_values(genesis_analyzed_creatures($state));
            if (count($analyzed) < 2) {
                $state['ui']['latest_event'] = [
                    'label' => 'Croisement impossible',
                    'message' => 'Il faut au moins deux espèces analysées pour croiser.',
                ];
                genesis_set_voice($state, 'Absence', 'Un seul génome ne fait pas un hybride. Explorez une autre zone.');
                $state['screen'] = 'world';
                break;
            }

            // Prefer two distinct species; take first two analyzed
            $parentA = $analyzed[0];
            $parentB = $analyzed[1];
            foreach ($analyzed as $candidate) {
                if (($candidate['species'] ?? '') !== ($parentA['species'] ?? '')) {
                    $parentB = $candidate;
                    break;
                }
            }

            $state['resources']['organic'] = max(0, (int) $state['resources']['organic'] - 1);
            genesis_archive_active_if_ready($state);

            $hybridId = genesis_next_creature_id($state);
            $statsA = $parentA['stats'] ?? [];
            $statsB = $parentB['stats'] ?? [];
            $hybridStats = [
                'force' => (int) floor(((int) ($statsA['force'] ?? 0) + (int) ($statsB['force'] ?? 0)) / 2) + 1,
                'vitesse' => (int) floor(((int) ($statsA['vitesse'] ?? 0) + (int) ($statsB['vitesse'] ?? 0)) / 2) + 1,
                'resistance' => (int) floor(((int) ($statsA['resistance'] ?? 0) + (int) ($statsB['resistance'] ?? 0)) / 2) + 1,
                'intelligence' => (int) floor(((int) ($statsA['intelligence'] ?? 0) + (int) ($statsB['intelligence'] ?? 0)) / 2) + 1,
            ];

            $abilityA = ($parentA['abilities'][0] ?? null);
            $abilityB = ($parentB['abilities'][0] ?? null);
            $hybridAbilities = array_values(array_unique(array_filter([$abilityA, $abilityB])));
            $hybridAbilities = array_slice($hybridAbilities, 0, 2);

            $nameA = (string) ($parentA['species'] ?? 'Alpha');
            $nameB = (string) ($parentB['species'] ?? 'Beta');
            $hybridSpecies = mb_substr($nameA, 0, 4) . mb_strtolower(mb_substr($nameB, 0, 4));
            $hybridSpecies = mb_convert_case($hybridSpecies, MB_CASE_TITLE, 'UTF-8');

            $state['creature'] = [
                'id' => $hybridId,
                'name' => $hybridSpecies,
                'species' => $hybridSpecies,
                'species_key' => null,
                'rarity' => 'unique',
                'stats' => $hybridStats,
                'abilities' => $hybridAbilities !== [] ? $hybridAbilities : ['Écho hybride'],
                'purity' => 70,
                'discovered' => true,
                'analyzed' => true,
                'mutated' => false,
                'mission_done' => false,
                'status' => 'prête',
                'parents' => [
                    $parentA['species'] ?? $parentA['name'] ?? 'Parent A',
                    $parentB['species'] ?? $parentB['name'] ?? 'Parent B',
                ],
                'zone_id' => null,
            ];

            $state['bestiary'][$hybridId] = $state['creature'];
            $state['heritage']['memories'][] = sprintf(
                'Croisement : %s × %s → %s (parents informatifs, pas de lignée).',
                $nameA,
                $nameB,
                $hybridSpecies
            );
            $state['ui']['latest_event'] = [
                'label' => 'Croisement',
                'message' => sprintf(
                    'Nouvel hybride unique : %s. Hérite en partie de %s et %s. Pureté 70 %%.',
                    $hybridSpecies,
                    $nameA,
                    $nameB
                ),
            ];
            genesis_set_voice(
                $state,
                'Hypothèse',
                sprintf(
                    '%s est né. Les parents restent une note — pas une dynastie. Que se passera-t-il si on le mute ou l’envoie en mission ?',
                    $hybridSpecies
                )
            );
            $state['screen'] = 'expedition';
            break;
    }

    genesis_refresh_campus($state);
}

function genesis_state_view(array $query): array
{
    genesis_boot_state();

    if (isset($query['screen'])) {
        $requestedScreen = genesis_screen((string) $query['screen']);
        $_SESSION['genesis']['screen'] = $requestedScreen;
    }

    $state = $_SESSION['genesis'];
    genesis_refresh_campus($state);
    $screen = genesis_screen((string) ($state['screen'] ?? 'institute'));
    $state['screen'] = $screen;
    $state['screen_meta'] = genesis_screen_state($screen, $state);
    $_SESSION['genesis'] = $state;

    return $state;
}

function genesis_process_post(array $post): string
{
    genesis_boot_state();

    $state = $_SESSION['genesis'];
    $action = (string) ($post['action'] ?? '');
    $redirectScreen = genesis_screen((string) ($post['screen'] ?? $state['screen']));

    if (isset($post['force_mission_outcome'])) {
        $forced = (string) $post['force_mission_outcome'];
        if (in_array($forced, ['success', 'injured', 'exhausted', 'scar'], true)) {
            $state['ui']['force_mission_outcome'] = $forced === 'scar' ? 'injured' : $forced;
        }
    }

    if (isset($post['force_test_outcome'])) {
        $forced = (string) $post['force_test_outcome'];
        if ($forced === 'success' || $forced === 'scar') {
            $state['ui']['force_mission_outcome'] = $forced === 'scar' ? 'injured' : 'success';
        }
    }

    if ($action !== '') {
        $payload = [
            'zone_id' => $post['zone_id'] ?? null,
            'mutation_choice' => $post['mutation_choice'] ?? null,
            'creature_id' => $post['creature_id'] ?? null,
        ];
        genesis_apply_action($state, $action, $payload);
        $redirectScreen = genesis_screen((string) ($state['screen'] ?? $redirectScreen));
    }

    $_SESSION['genesis'] = $state;

    return $redirectScreen;
}
