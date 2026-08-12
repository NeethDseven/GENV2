<?php

declare(strict_types=1);

namespace Genesis\Game\Data;

/**
 * Espèces + zones.
 * Cœur = diversité de découvertes → croisements désirables.
 */
final class Catalog
{
    public static function species(): array
    {
        return [
            'thermidé' => self::entry(
                'thermidé',
                'Thermidé',
                'commune',
                'Thermidé d’écho',
                'chaleur',
                'Trace thermique — espèce non cataloguée',
                [4, 6, 3, 5],
                ['Lecture thermique'],
                [
                    'stats' => self::mut('Charge musculaire', 'Force +3 · Résistance +1 · pureté −10', ['force' => 3, 'resistance' => 1], null, 10, 'renforcé', 'peu commune'),
                    'ability' => self::mut('Peau de fardeau', 'Capacité « Peau de fardeau » · Force +1 · pureté −15', ['force' => 1], 'Peau de fardeau', 15, 'de fardeau', 'peu commune'),
                ]
            ),
            'salinide' => self::entry(
                'salinide',
                'Salinide',
                'peu commune',
                'Salinide de crête',
                'minéral',
                'Signature saline — carapace minérale',
                [5, 3, 7, 3],
                ['Coque saline'],
                [
                    'stats' => self::mut('Ossification', 'Résistance +3 · Force +1 · pureté −10', ['resistance' => 3, 'force' => 1], null, 10, 'ossifié', 'rare'),
                    'ability' => self::mut('Marée figée', 'Capacité « Marée figée » · Résistance +1 · pureté −15', ['resistance' => 1], 'Marée figée', 15, 'des marées', 'rare'),
                ]
            ),
            'cryopode' => self::entry(
                'cryopode',
                'Cryopode',
                'rare',
                'Cryopode des brumes',
                'froid',
                'Écho glacé — mouvement sous la canopée',
                [3, 7, 4, 6],
                ['Voile de brume'],
                [
                    'stats' => self::mut('Sprints gelés', 'Vitesse +3 · Intelligence +1 · pureté −10', ['vitesse' => 3, 'intelligence' => 1], null, 10, 'fulgurant', 'rare'),
                    'ability' => self::mut('Souffle d’hiver', 'Capacité « Souffle d’hiver » · Intelligence +1 · pureté −15', ['intelligence' => 1], 'Souffle d’hiver', 15, 'd’hiver', 'très rare'),
                ]
            ),
            'lumivive' => self::entry(
                'lumivive',
                'Lumivive',
                'peu commune',
                'Lumivive des abysses',
                'abysse',
                'Pulsation bioluminescente — esprit des fosses',
                [2, 5, 4, 8],
                ['Lanternes d’abîme'],
                [
                    'stats' => self::mut('Cortex lumineux', 'Intelligence +3 · Vitesse +1 · pureté −10', ['intelligence' => 3, 'vitesse' => 1], null, 10, 'lucide', 'rare'),
                    'ability' => self::mut('Phare mental', 'Capacité « Phare mental » · Intelligence +1 · pureté −15', ['intelligence' => 1], 'Phare mental', 15, 'phare', 'rare'),
                ]
            ),
            'ferrugon' => self::entry(
                'ferrugon',
                'Ferrugon',
                'rare',
                'Ferrugon de scorie',
                'feu',
                'Souffle métallique — plaque de lave figée',
                [8, 2, 6, 3],
                ['Plaque de scorie'],
                [
                    'stats' => self::mut('Forge vive', 'Force +3 · Résistance +1 · pureté −10', ['force' => 3, 'resistance' => 1], null, 10, 'forgé', 'très rare'),
                    'ability' => self::mut('Cœur de magma', 'Capacité « Cœur de magma » · Force +1 · pureté −15', ['force' => 1], 'Cœur de magma', 15, 'magma', 'très rare'),
                ]
            ),
            'mycorène' => self::entry(
                'mycorène',
                'Mycorène',
                'peu commune',
                'Mycorène des marais',
                'spore',
                'Réseau fongique — chuchotements sous la tourbe',
                [3, 4, 5, 7],
                ['Filaments-mémoire'],
                [
                    'stats' => self::mut('Mycélium dense', 'Résistance +2 · Intelligence +2 · pureté −10', ['resistance' => 2, 'intelligence' => 2], null, 10, 'réticulé', 'rare'),
                    'ability' => self::mut('Spores d’écho', 'Capacité « Spores d’écho » · Intelligence +1 · pureté −15', ['intelligence' => 1], 'Spores d’écho', 15, 'sporulé', 'rare'),
                ]
            ),
            'zéphiride' => self::entry(
                'zéphiride',
                'Zéphiride',
                'commune',
                'Zéphiride des crêtes',
                'vent',
                'Sillage aérien — silhouette qui refuse le sol',
                [3, 9, 2, 5],
                ['Voile de courant'],
                [
                    'stats' => self::mut('Jet d’altitude', 'Vitesse +4 · pureté −10', ['vitesse' => 4], null, 10, 'fulgurant', 'peu commune'),
                    'ability' => self::mut('Rafale tranchante', 'Capacité « Rafale tranchante » · Vitesse +1 · pureté −15', ['vitesse' => 1], 'Rafale tranchante', 15, 'des vents', 'peu commune'),
                ]
            ),
            // Nouvelles formes (diversité de croisement)
            'sablex' => self::entry(
                'sablex',
                'Sablex',
                'commune',
                'Sablex de dune',
                'chaleur',
                'Souffle de grain — chaleur qui marche',
                [5, 5, 4, 4],
                ['Peau d’aréna'],
                [
                    'stats' => self::mut('Enfouissement', 'Résistance +2 · Force +2 · pureté −10', ['resistance' => 2, 'force' => 2], null, 10, 'enfoui', 'peu commune'),
                    'ability' => self::mut('Tempête de grains', 'Capacité « Tempête de grains » · Vitesse +1 · pureté −15', ['vitesse' => 1], 'Tempête de grains', 15, 'des dunes', 'peu commune'),
                ]
            ),
            'gravile' => self::entry(
                'gravile',
                'Gravile',
                'peu commune',
                'Gravile des failles',
                'minéral',
                'Choc de pierre — éclat qui écoute',
                [6, 2, 8, 4],
                ['Cœur de schiste'],
                [
                    'stats' => self::mut('Stratification', 'Résistance +4 · pureté −10', ['resistance' => 4], null, 10, 'stratifié', 'rare'),
                    'ability' => self::mut('Faille résonnante', 'Capacité « Faille résonnante » · Intelligence +2 · pureté −15', ['intelligence' => 2], 'Faille résonnante', 15, 'des failles', 'rare'),
                ]
            ),
            'nubivor' => self::entry(
                'nubivor',
                'Nubivor',
                'peu commune',
                'Nubivor des hauts vents',
                'vent',
                'Ombre de nuage — faim d’altitude',
                [2, 8, 3, 7],
                ['Aile de brume'],
                [
                    'stats' => self::mut('Planeur long', 'Vitesse +2 · Intelligence +2 · pureté −10', ['vitesse' => 2, 'intelligence' => 2], null, 10, 'planeur', 'rare'),
                    'ability' => self::mut('Dévorer le ciel', 'Capacité « Dévorer le ciel » · Vitesse +2 · pureté −15', ['vitesse' => 2], 'Dévorer le ciel', 15, 'céleste', 'rare'),
                ]
            ),
            'ombreline' => self::entry(
                'ombreline',
                'Ombreline',
                'rare',
                'Ombreline des parois',
                'abysse',
                'Silhouette collée au noir — ne se laisse pas compter',
                [3, 6, 5, 7],
                ['Peau d’absence'],
                [
                    'stats' => self::mut('Dissolution', 'Vitesse +3 · Intelligence +1 · pureté −10', ['vitesse' => 3, 'intelligence' => 1], null, 10, 'dissoute', 'très rare'),
                    'ability' => self::mut('Troisième ombre', 'Capacité « Troisième ombre » · Intelligence +2 · pureté −15', ['intelligence' => 2], 'Troisième ombre', 15, 'd’ombre', 'très rare'),
                ]
            ),
            // Variantes rares — rejouabilité free-play
            'tourbeille' => self::entry(
                'tourbeille',
                'Tourbeille',
                'rare',
                'Tourbeille des fonds',
                'spore',
                'Bulbe sous la vase — mémoire qui flotte à l’envers',
                [4, 3, 6, 8],
                ['Bulbe-mémoire'],
                [
                    'stats' => self::mut('Tourbe dense', 'Résistance +3 · Intelligence +1 · pureté −10', ['resistance' => 3, 'intelligence' => 1], null, 10, 'tourbé', 'très rare'),
                    'ability' => self::mut('Souffle de vase', 'Capacité « Souffle de vase » · Intelligence +2 · pureté −15', ['intelligence' => 2], 'Souffle de vase', 15, 'des vases', 'très rare'),
                ]
            ),
            'cindrite' => self::entry(
                'cindrite',
                'Cindrite',
                'rare',
                'Cindrite de braise',
                'feu',
                'Cendre encore chaude — un pas de trop et elle s’éteint… ou t’embrase',
                [7, 4, 5, 3],
                ['Peau de braise'],
                [
                    'stats' => self::mut('Braise vive', 'Force +3 · Vitesse +1 · pureté −10', ['force' => 3, 'vitesse' => 1], null, 10, 'incandescent', 'très rare'),
                    'ability' => self::mut('Dernière cendre', 'Capacité « Dernière cendre » · Force +2 · pureté −15', ['force' => 2], 'Dernière cendre', 15, 'de cendre', 'très rare'),
                ]
            ),
            // Phase 2 — formes hors berceau (orbite)
            'astérion' => self::entry(
                'astérion',
                'Astérion',
                'rare',
                'Astérion de brume',
                'froid',
                'Pulsation orbitale — le froid qui n’a jamais touché Aster-0',
                [4, 7, 5, 8],
                ['Voile de vide'],
                [
                    'stats' => self::mut('Orbite fixe', 'Intelligence +3 · Résistance +1 · pureté −10', ['intelligence' => 3, 'resistance' => 1], null, 10, 'orbital', 'très rare'),
                    'ability' => self::mut('Gravité douce', 'Capacité « Gravité douce » · Force +1 · pureté −15', ['force' => 1], 'Gravité douce', 15, 'de lune', 'très rare'),
                ]
            ),
            'videchor' => self::entry(
                'videchor',
                'Videchor',
                'très rare',
                'Videchor du seuil',
                'abysse',
                'Silence entre les mondes — presque une absence de forme',
                [2, 6, 4, 10],
                ['Chœur du vide'],
                [
                    'stats' => self::mut('Dilution', 'Intelligence +4 · pureté −12', ['intelligence' => 4], null, 12, 'dilué', 'unique'),
                    'ability' => self::mut('Appel lointain', 'Capacité « Appel lointain » · Vitesse +2 · pureté −15', ['vitesse' => 2], 'Appel lointain', 15, 'du seuil', 'unique'),
                ]
            ),
            'scorion' => self::entry(
                'scorion',
                'Scorion',
                'rare',
                'Scorion d’anneau',
                'feu',
                'Fragment d’anneau brûlant — scorie sans sol',
                [9, 3, 6, 4],
                ['Carapace d’orbite'],
                [
                    'stats' => self::mut('Fusion lente', 'Force +3 · Résistance +2 · pureté −10', ['force' => 3, 'resistance' => 2], null, 10, 'fondu', 'très rare'),
                    'ability' => self::mut('Éclat d’anneau', 'Capacité « Éclat d’anneau » · Force +2 · pureté −15', ['force' => 2], 'Éclat d’anneau', 15, 'd’anneau', 'très rare'),
                ]
            ),
        ];
    }

    /**
     * Zones orbitales (phase 2) — débloquées en chaîne après spatio-port.
     * starter_orbital = ouverte dès l’ouverture de la Baie.
     */
    public static function orbitalZones(): array
    {
        return [
            'orbite_a' => [
                'id' => 'orbite_a',
                'name' => 'Lune de brume',
                'risk' => 'élevé',
                'biome' => 'froid',
                'species_key' => 'astérion',
                'species_keys' => ['astérion', 'cryopode', 'ombreline'],
                'layer' => 'orbital',
                'starter_orbital' => true,
                'unlocks' => ['orbite_b'],
                'starter' => false,
            ],
            'orbite_b' => [
                'id' => 'orbite_b',
                'name' => 'Anneau de scorie',
                'risk' => 'extrême',
                'biome' => 'feu',
                'species_key' => 'scorion',
                'species_keys' => ['scorion', 'ferrugon', 'sablex'],
                'layer' => 'orbital',
                'starter_orbital' => false,
                'unlocks' => ['orbite_c'],
                'starter' => false,
            ],
            'orbite_c' => [
                'id' => 'orbite_c',
                'name' => 'Voile de poussière',
                'risk' => 'élevé',
                'biome' => 'vent',
                'species_key' => 'nubivor',
                'species_keys' => ['nubivor', 'zéphiride', 'astérion'],
                'layer' => 'orbital',
                'starter_orbital' => false,
                'unlocks' => ['orbite_d'],
                'starter' => false,
            ],
            'orbite_d' => [
                'id' => 'orbite_d',
                'name' => 'Seuil du noir',
                'risk' => 'extrême',
                'biome' => 'abysse',
                'species_key' => 'videchor',
                'species_keys' => ['videchor', 'ombreline', 'lumivive'],
                'layer' => 'orbital',
                'starter_orbital' => false,
                'unlocks' => [],
                'starter' => false,
            ],
        ];
    }

    public static function speciesByKey(string $key): ?array
    {
        return self::species()[$key] ?? null;
    }

    public static function zones(): array
    {
        // species_keys : 1 à 3 espèces possibles par zone (tirage à l’exploration)
        // species_key : principale (rétrocompat / premier contact)
        // unlocks = zones révélées après exploration réussie
        return [
            'plaine' => [
                'id' => 'plaine',
                'name' => 'Plaine d’écho',
                'risk' => 'modéré',
                'biome' => 'chaleur',
                'species_key' => 'thermidé',
                'species_keys' => ['thermidé', 'sablex', 'zéphiride'],
                'starter' => true,
                'unlocks' => ['crete', 'marais'],
            ],
            'crete' => [
                'id' => 'crete',
                'name' => 'Crêtes salines',
                'risk' => 'élevé',
                'biome' => 'minéral',
                'species_key' => 'salinide',
                'species_keys' => ['salinide', 'gravile', 'ferrugon'],
                'starter' => false,
                'unlocks' => ['foret', 'vents'],
            ],
            'foret' => [
                'id' => 'foret',
                'name' => 'Forêt froide',
                'risk' => 'modéré',
                'biome' => 'froid',
                'species_key' => 'cryopode',
                'species_keys' => ['cryopode', 'mycorène', 'nubivor'],
                'starter' => false,
                'unlocks' => ['abysse'],
            ],
            'abysse' => [
                'id' => 'abysse',
                'name' => 'Fosse lumineuse',
                'risk' => 'élevé',
                'biome' => 'abysse',
                'species_key' => 'lumivive',
                'species_keys' => ['lumivive', 'ombreline'],
                'starter' => false,
                'unlocks' => ['scorie'],
            ],
            'scorie' => [
                'id' => 'scorie',
                'name' => 'Champs de scorie',
                'risk' => 'extrême',
                'biome' => 'feu',
                'species_key' => 'ferrugon',
                'species_keys' => ['ferrugon', 'cindrite', 'gravile', 'sablex'],
                'starter' => false,
                'unlocks' => [],
            ],
            'marais' => [
                'id' => 'marais',
                'name' => 'Marais sporulés',
                'risk' => 'modéré',
                'biome' => 'spore',
                'species_key' => 'mycorène',
                'species_keys' => ['mycorène', 'tourbeille', 'lumivive', 'ombreline'],
                'starter' => false,
                'unlocks' => ['vents'],
            ],
            'vents' => [
                'id' => 'vents',
                'name' => 'Crêtes venteuses',
                'risk' => 'élevé',
                'biome' => 'vent',
                'species_key' => 'zéphiride',
                'species_keys' => ['zéphiride', 'nubivor', 'cryopode'],
                'starter' => false,
                'unlocks' => ['scorie'],
            ],
        ];
    }

    public static function zone(string $id): ?array
    {
        return self::zones()[$id] ?? null;
    }

    /**
     * Liste des espèces possibles dans une zone (1–3).
     *
     * @return list<string>
     */
    public static function zoneSpeciesKeys(array $zone): array
    {
        $keys = $zone['species_keys'] ?? null;
        if (is_array($keys) && $keys !== []) {
            $out = [];
            foreach ($keys as $k) {
                $k = (string) $k;
                if ($k !== '' && self::speciesByKey($k) !== null) {
                    $out[] = $k;
                }
            }
            if ($out !== []) {
                return array_values(array_unique($out));
            }
        }
        $single = (string) ($zone['species_key'] ?? 'thermidé');

        return self::speciesByKey($single) !== null ? [$single] : ['thermidé'];
    }

    /**
     * Choisit une espèce pour une exploration.
     * Priorité : jamais croisée dans cette zone → jamais analysée globalement → replay.
     * Mode instant = première clé (tests déterministes).
     */
    public static function pickZoneSpecies(array $zone, array $state = []): string
    {
        $keys = self::zoneSpeciesKeys($zone);
        $instant = ($_ENV['GENESIS_INSTANT'] ?? getenv('GENESIS_INSTANT') ?: '') === '1'
            || (defined('GENESIS_INSTANT') && GENESIS_INSTANT);
        if ($instant || count($keys) === 1) {
            return $keys[0];
        }

        $zoneId = (string) ($zone['id'] ?? '');
        $foundHere = $state['world']['zones'][$zoneId]['species_found'] ?? [];
        if (!is_array($foundHere)) {
            $foundHere = [];
        }

        $knownGlobal = [];
        foreach (array_merge(
            array_values($state['bestiary'] ?? []),
            [($state['creature'] ?? [])]
        ) as $c) {
            if (!empty($c['analyzed']) && !empty($c['species_key'])) {
                $knownGlobal[(string) $c['species_key']] = true;
            }
        }

        $notFoundHere = array_values(array_filter(
            $keys,
            static fn (string $k): bool => !in_array($k, $foundHere, true)
        ));
        if ($notFoundHere !== []) {
            $neverAnalyzed = array_values(array_filter(
                $notFoundHere,
                static fn (string $k): bool => empty($knownGlobal[$k])
            ));
            $pool = $neverAnalyzed !== [] ? $neverAnalyzed : $notFoundHere;

            return $pool[random_int(0, count($pool) - 1)];
        }

        // Zone entièrement contactée : replay — encore utile (lecture secondaire)
        return $keys[random_int(0, count($keys) - 1)];
    }

    /**
     * @param array{0:int,1:int,2:int,3:int} $stats force,vitesse,resistance,intelligence
     */
    private static function entry(
        string $key,
        string $species,
        string $rarity,
        string $name,
        string $biome,
        string $signal,
        array $stats,
        array $abilities,
        array $mutations
    ): array {
        return [
            'key' => $key,
            'species' => $species,
            'rarity' => $rarity,
            'name' => $name,
            'biome' => $biome,
            'signal' => $signal,
            'stats' => [
                'force' => $stats[0],
                'vitesse' => $stats[1],
                'resistance' => $stats[2],
                'intelligence' => $stats[3],
            ],
            'abilities' => $abilities,
            'mutations' => $mutations,
        ];
    }

    private static function mut(
        string $label,
        string $description,
        array $stats,
        ?string $ability,
        int $purityCost,
        string $suffix,
        string $rarity
    ): array {
        return [
            'label' => $label,
            'description' => $description,
            'stats' => $stats,
            'ability' => $ability,
            'purity_cost' => $purityCost,
            'name_suffix' => $suffix,
            'rarity' => $rarity,
        ];
    }
}
