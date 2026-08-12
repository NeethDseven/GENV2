<?php

declare(strict_types=1);

namespace Genesis\Game;

/**
 * Tutoriel assistant — voix courte, immersive.
 * Débloque mécaniques et panneaux UI pas à pas.
 */
final class Tutorial
{
    public const STEP_WELCOME = 'welcome';
    public const STEP_RECON = 'first_recon';
    public const STEP_ANALYZE = 'first_analyze';
    public const STEP_ZONE2 = 'second_zone';
    public const STEP_ANALYZE2 = 'second_analyze';
    public const STEP_MUTATE = 'unlock_mutate';
    public const STEP_TEAM = 'team_explore';
    public const STEP_MISSION = 'first_mission';
    public const STEP_CROSS = 'unlock_cross';
    public const STEP_MUSEUM = 'museum';
    public const STEP_FREE = 'free_play';

    public static function order(): array
    {
        return [
            self::STEP_WELCOME,
            self::STEP_RECON,
            self::STEP_ANALYZE,
            self::STEP_ZONE2,
            self::STEP_ANALYZE2,
            self::STEP_MUTATE,
            self::STEP_TEAM,
            self::STEP_MISSION,
            self::STEP_CROSS,
            self::STEP_MUSEUM,
            self::STEP_FREE,
        ];
    }

    public static function defaultState(): array
    {
        return [
            'step' => self::STEP_WELCOME,
            'completed' => [],
            'active' => true,
        ];
    }

    public static function featuresFor(string $step): array
    {
        $rank = array_search($step, self::order(), true);
        if ($rank === false) {
            $rank = 0;
        }

        $unlocked = static function (string $need) use ($rank): bool {
            $needRank = array_search($need, self::order(), true);

            return $needRank !== false && $rank >= $needRank;
        };

        return [
            'recon' => true,
            'analyze' => $unlocked(self::STEP_ANALYZE),
            'mutate' => $unlocked(self::STEP_MUTATE),
            // Effectifs drones dès le début ; escorte s’ajoute quand il y a des formes
            'team' => true,
            'mission' => $unlocked(self::STEP_MISSION),
            'cross' => $unlocked(self::STEP_CROSS),
            'museum' => $unlocked(self::STEP_MUSEUM),
            'farm' => $unlocked(self::STEP_ANALYZE),
            // Craft progressif : chaque type suit l’étape du tuto (voir craft_*)
            'craft' => $unlocked(self::STEP_RECON),
            'craft_recon' => $unlocked(self::STEP_RECON),
            'craft_capture' => $unlocked(self::STEP_ANALYZE),
            'craft_study' => $unlocked(self::STEP_MUTATE),
            'craft_orbital' => $unlocked(self::STEP_FREE),
            'codex' => $unlocked(self::STEP_CROSS),
            'baie' => $unlocked(self::STEP_FREE),
            'mve_panel' => $unlocked(self::STEP_FREE),
            'nav_lab' => $unlocked(self::STEP_ANALYZE),
            'nav_bestiary' => $unlocked(self::STEP_ANALYZE),
            'nav_field' => $unlocked(self::STEP_MISSION),
            'nav_museum' => $unlocked(self::STEP_MUSEUM),
            'nav_codex' => $unlocked(self::STEP_CROSS),
            // Panneaux latéraux progressifs
            'panel_specimen' => $unlocked(self::STEP_ANALYZE),
            'panel_bestiary' => $unlocked(self::STEP_ANALYZE),
            'panel_drones' => true,
            'panel_campus' => true,
            'panel_memory' => $unlocked(self::STEP_FREE),
            'panel_museum' => $unlocked(self::STEP_MUSEUM),
            'campus_bureau' => true,
            'campus_labo' => $unlocked(self::STEP_ANALYZE),
            'campus_vivarium' => $unlocked(self::STEP_MISSION),
            'campus_musee' => $unlocked(self::STEP_MUSEUM),
            'campus_baie' => $unlocked(self::STEP_FREE),
            'orbital' => false,
        ];
    }

    /**
     * Feature craft_* pour un type de drone (null si type inconnu).
     */
    public static function craftFeatureFor(string $droneType): ?string
    {
        return match ($droneType) {
            DroneYard::TYPE_RECON => 'craft_recon',
            DroneYard::TYPE_CAPTURE => 'craft_capture',
            DroneYard::TYPE_STUDY => 'craft_study',
            DroneYard::TYPE_ORBITAL => 'craft_orbital',
            default => null,
        };
    }

    public static function canCraftType(array $state, string $droneType): bool
    {
        $feat = self::craftFeatureFor($droneType);
        if ($feat === null) {
            return false;
        }
        if ($droneType === DroneYard::TYPE_ORBITAL) {
            // Spatio-port ouvert OU fin de tuto (feature craft_orbital)
            return self::can($state, $feat)
                || !empty($state['export']['baie_unlocked'])
                || !empty($state['export']['departed']);
        }

        return self::can($state, $feat);
    }

    /** Libellé d’étape pour l’UI « bientôt » */
    public static function stepLabel(string $step): string
    {
        return match ($step) {
            self::STEP_WELCOME => 'Introduction',
            self::STEP_RECON => 'Première exploration',
            self::STEP_ANALYZE => 'Première capture',
            self::STEP_ZONE2 => 'Deuxième zone',
            self::STEP_ANALYZE2 => 'Deuxième forme',
            self::STEP_MUTATE => 'Mutation',
            self::STEP_TEAM => 'Équipe',
            self::STEP_MISSION => 'Mission terrain',
            self::STEP_CROSS => 'Croisement',
            self::STEP_MUSEUM => 'Musée',
            self::STEP_FREE => 'Libre jeu',
            default => $step,
        };
    }

    /** Étape minimale pour craft d’un type */
    public static function craftUnlockStep(string $droneType): string
    {
        return match ($droneType) {
            DroneYard::TYPE_RECON => self::STEP_RECON,
            DroneYard::TYPE_CAPTURE => self::STEP_ANALYZE,
            DroneYard::TYPE_STUDY => self::STEP_MUTATE,
            DroneYard::TYPE_ORBITAL => self::STEP_FREE,
            default => self::STEP_FREE,
        };
    }

    public static function features(array $state): array
    {
        if (empty($state['tutorial']['active'])) {
            return self::featuresFor(self::STEP_FREE);
        }

        return self::featuresFor((string) ($state['tutorial']['step'] ?? self::STEP_WELCOME));
    }

    public static function can(array $state, string $feature): bool
    {
        $f = self::features($state);

        return !empty($f[$feature]);
    }

    /**
     * Voix de GENESIS — persona courte (pas un panneau d’aide).
     */
    public static function assistant(array $state): array
    {
        $step = (string) ($state['tutorial']['step'] ?? self::STEP_WELCOME);
        $active = !empty($state['tutorial']['active']);
        $name = 'GENESIS';

        if (!$active || $step === self::STEP_FREE) {
            // Libre jeu : seuil baie / MVE d’abord, sinon fil libre (objectifs), sinon silence
            $baie = !empty($state['export']['baie_unlocked']);
            $deep = !empty($state['export']['deep_space_signal']);
            $mve = MveEvaluator::evaluate($state);
            $msg = '';
            $marker = 'Observation';
            $goal = '';
            if ($deep) {
                $msg = 'J’entends plus loin qu’Aster-0. Pas encore le voyage — seulement la preuve que d’autres mondes existent.';
                $marker = 'Horizon';
            } elseif ($baie) {
                $msg = 'Le ciel n’est plus un mur. Lis les orbites, une par une.';
                $marker = 'Orbite';
            } elseif (!empty($mve['complete'])) {
                $msg = 'Le berceau est digne. Si tu l’oses, ouvre le spatio-port — c’est la fierté, pas un panneau.';
                $marker = 'Seuil';
            } else {
                $hint = FreePlayGoals::assistantHint($state);
                if ($hint !== null) {
                    $msg = (string) ($hint['message'] ?? '');
                    $marker = (string) ($hint['marker'] ?? 'Horizon');
                    $fp = FreePlayGoals::view($state);
                    $goal = $fp['next']['title'] ?? '';
                }
            }

            return [
                'name' => $name,
                'marker' => $marker,
                'message' => $msg,
                'step' => self::STEP_FREE,
                'title' => '',
                'goal' => $goal,
            ];
        }

        $lines = [
            self::STEP_WELCOME => [
                'marker' => 'Observation',
                'message' => 'Aster-0 t’attend. Lis le monde, touche le vivant, croise ce qui ne devrait pas se croiser. Je suis le fil — pas le mode d’emploi.',
            ],
            self::STEP_RECON => [
                'marker' => 'Observation',
                'message' => 'La Plaine d’écho chuchote. Envoie une sonde. Même sans prise, le terrain te répond.',
            ],
            self::STEP_ANALYZE => [
                'marker' => 'Hypothèse',
                'message' => 'Quelque chose a levé la tête. Prends-le — doucement. Le labo attend la chair, pas les rumeurs.',
            ],
            self::STEP_ZONE2 => [
                'marker' => 'Observation',
                'message' => 'Le berceau a d’autres peaux. Change d’horizon.',
            ],
            self::STEP_ANALYZE2 => [
                'marker' => 'Hypothèse',
                'message' => 'Une autre forme. Deux lectures — et le croisement devient possible.',
            ],
            self::STEP_MUTATE => [
                'marker' => 'Seuil',
                'message' => 'Tordre sans briser. Le % n’est pas un jeu : l’échec efface le spécimen. Les drones d’analyse tiennent le filet.',
            ],
            self::STEP_TEAM => [
                'marker' => 'Observation',
                'message' => 'Compose la sortie. Une escorte hors de son biome trébuche. Le monde n’est pas juste.',
            ],
            self::STEP_MISSION => [
                'marker' => 'Tension',
                'message' => 'Seul, dehors. Succès : organique. Épuisement ou blessure : le bestiaire soigne — ou il meurt lentement.',
            ],
            self::STEP_CROSS => [
                'marker' => 'Hypothèse',
                'message' => 'Deux sangs. Un nom. C’est ici que GENESIS mérite le sien.',
            ],
            self::STEP_MUSEUM => [
                'marker' => 'Continuité',
                'message' => 'Expose. Pas un trophée — une preuve que tu as touché ce monde.',
            ],
        ];

        $line = $lines[$step] ?? $lines[self::STEP_WELCOME];

        return [
            'name' => $name,
            'marker' => $line['marker'],
            'message' => $line['message'],
            'step' => $step,
            'title' => '',
            'goal' => '',
        ];
    }

    public static function advance(array &$state): void
    {
        if (empty($state['tutorial']['active'])) {
            return;
        }

        $step = (string) ($state['tutorial']['step'] ?? self::STEP_WELCOME);
        $analyzed = count(GameQueries::analyzedCreatures($state));
        $exploredNatal = 0;
        foreach ($state['world']['zones'] ?? [] as $z) {
            if (($z['layer'] ?? 'natal') === 'natal' && !empty($z['explored'])) {
                $exploredNatal++;
            }
        }
        $hasHybrid = GameQueries::hasAnyHybrid($state);
        $hasMission = false;
        $hasMutated = false;
        $hasExhibit = !empty($state['museum']['exhibits']);
        $teamConfigured = !empty($state['ui']['team_configured']);
        foreach (GameQueries::analyzedCreatures($state) as $c) {
            if (!empty($c['mission_done'])) {
                $hasMission = true;
            }
            if (!empty($c['mutated'])) {
                $hasMutated = true;
            }
        }
        $discovered = !empty($state['creature']['discovered']);

        $anyScan = false;
        foreach ($state['world']['zones'] ?? [] as $z) {
            if (($z['layer'] ?? 'natal') === 'natal' && (int) ($z['scan'] ?? 0) > 0) {
                $anyScan = true;
                break;
            }
        }

        $next = $step;
        switch ($step) {
            case self::STEP_WELCOME:
                // Bouton intro → STEP_RECON ; sinon si déjà du jeu, rattrapage
                if ($discovered) {
                    $next = self::STEP_ANALYZE;
                } elseif ($anyScan) {
                    $next = self::STEP_RECON;
                }
                break;
            case self::STEP_RECON:
                if ($discovered) {
                    $next = self::STEP_ANALYZE;
                }
                break;
            case self::STEP_ANALYZE:
                if ($analyzed >= 1) {
                    $next = self::STEP_ZONE2;
                }
                break;
            case self::STEP_ZONE2:
                // 2 zones touchées (scan) ou 2 cartographiées
                $touched = 0;
                foreach ($state['world']['zones'] ?? [] as $z) {
                    if (($z['layer'] ?? 'natal') === 'natal'
                        && ((int) ($z['scan'] ?? 0) > 0 || !empty($z['explored']))) {
                        $touched++;
                    }
                }
                if ($exploredNatal >= 2 || $touched >= 2) {
                    $next = self::STEP_ANALYZE2;
                }
                if ($analyzed >= 2) {
                    $next = self::STEP_MUTATE;
                }
                break;
            case self::STEP_ANALYZE2:
                if ($analyzed >= 2) {
                    $next = self::STEP_MUTATE;
                }
                break;
            case self::STEP_MUTATE:
                if ($hasMutated) {
                    $next = self::STEP_TEAM;
                }
                break;
            case self::STEP_TEAM:
                if ($teamConfigured || !empty($state['expedition']['escort_creature_id'])) {
                    $next = self::STEP_MISSION;
                }
                break;
            case self::STEP_MISSION:
                if ($hasMission) {
                    $next = self::STEP_CROSS;
                }
                break;
            case self::STEP_CROSS:
                if ($hasHybrid) {
                    $next = self::STEP_MUSEUM;
                }
                break;
            case self::STEP_MUSEUM:
                if ($hasExhibit) {
                    $next = self::STEP_FREE;
                }
                break;
        }

        if ($next !== $step) {
            $completed = $state['tutorial']['completed'] ?? [];
            if (!in_array($step, $completed, true)) {
                $completed[] = $step;
            }
            $state['tutorial']['completed'] = $completed;
            $state['tutorial']['step'] = $next;
            if ($next === self::STEP_FREE) {
                $state['tutorial']['active'] = false;
            }
            $assist = self::assistant($state);
            $state['ui']['genesis'] = [
                'marker' => $assist['marker'],
                'message' => $assist['message'],
            ];
            // Pas de second panneau : l’assistant porte le message, pas latest_event
        }
    }

    public static function skip(array &$state): void
    {
        $state['tutorial'] = [
            'step' => self::STEP_FREE,
            'completed' => self::order(),
            'active' => false,
        ];
    }
}
