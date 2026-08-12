<?php

declare(strict_types=1);

namespace Genesis\Game;

use Genesis\Game\Data\Catalog;

final class GameEngine
{
    public static function bootSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Session déjà peuplée (tests CLI inclus)
        if (isset($_SESSION['genesis_game']) && is_array($_SESSION['genesis_game'])) {
            return;
        }

        try {
            GameRepository::currentPlayerId();
            $activeId = GameRepository::findActiveId();
            if ($activeId !== null) {
                $state = GameRepository::load($activeId);
                $state = self::migrateState($state);
                $state['ui']['db_game_id'] = $activeId;
                $_SESSION['genesis_game'] = $state;
                $_SESSION['genesis_game_id'] = $activeId;

                return;
            }
            $state = StateFactory::initial();
            $id = GameRepository::create($state, 'Partie en cours', true);
            $state['ui']['db_game_id'] = $id;
            $_SESSION['genesis_game'] = $state;
            $_SESSION['genesis_game_id'] = $id;
        } catch (\Throwable $e) {
            // Sans BDD : session pure (tests / secours)
            $_SESSION['genesis_game'] = StateFactory::initial();
            $_SESSION['genesis_db_error'] = $e->getMessage();
        }
    }

    public static function state(): array
    {
        self::bootSession();
        $state = $_SESSION['genesis_game'];
        GameQueries::refreshCampus($state);
        $_SESSION['genesis_game'] = $state;

        return $state;
    }

    public static function save(array $state): void
    {
        GameQueries::refreshCampus($state);
        $_SESSION['genesis_game'] = $state;
        self::persistActive($state);
    }

    /** Persiste la partie active en BDD (auto-save). */
    private static function persistActive(array $state): void
    {
        $id = (int) ($state['ui']['db_game_id'] ?? $_SESSION['genesis_game_id'] ?? 0);
        if ($id <= 0) {
            return;
        }
        try {
            GameRepository::update($id, $state);
        } catch (\Throwable $e) {
            // silencieux : la session reste la source de vérité immédiate
        }
    }

    public static function reset(): void
    {
        self::bootSession();
        $state = StateFactory::initial();
        try {
            $id = GameRepository::create($state, 'Partie en cours', true);
            $state['ui']['db_game_id'] = $id;
            $_SESSION['genesis_game_id'] = $id;
        } catch (\Throwable $e) {
            unset($_SESSION['genesis_game_id']);
        }
        $_SESSION['genesis_game'] = $state;
    }

    public static function setScreen(array &$state, string $screen): void
    {
        $allowed = ['institute', 'world', 'lab', 'bestiary', 'field', 'museum', 'departure', 'codex'];
        $state['screen'] = in_array($screen, $allowed, true) ? $screen : 'institute';
    }

    public static function view(array $query): array
    {
        self::bootSession();
        $state = self::migrateState($_SESSION['genesis_game']);
        self::tick($state);

        if (isset($query['screen'])) {
            self::setScreen($state, (string) $query['screen']);
        }

        Tutorial::advance($state);
        FreePlayGoals::tick($state);
        // Sync assistant voice while tutorial active (ou fil libre)
        if (!empty($state['tutorial']['active']) || empty($state['ui']['genesis']['message'])) {
            $assist = Tutorial::assistant($state);
            if (trim((string) ($assist['message'] ?? '')) !== '') {
                $state['ui']['genesis'] = [
                    'marker' => $assist['marker'],
                    'message' => $assist['message'],
                ];
            }
        }
        GameQueries::refreshCampus($state);

        // One-shot : consommer flash / latest_event AVANT buildView, ne jamais les re-persister
        // (sinon « Organique synthétisé » reste collé en session/BDD)
        $flashOnce = $state['ui']['flash'] ?? null;
        $eventOnce = $state['ui']['latest_event'] ?? null;
        unset($state['ui']['flash'], $state['ui']['latest_event']);

        $state['view'] = self::buildView($state, $query);
        $state['view']['flash'] = is_array($flashOnce) ? $flashOnce : null;
        if (is_array($eventOnce) && trim((string) ($eventOnce['message'] ?? '')) !== '') {
            $state['view']['latest_event'] = $eventOnce;
        }

        // Session + BDD sans les notifs (cette réponse HTTP seule les affiche)
        $toStore = $state;
        unset($toStore['view']['flash'], $toStore['view']['latest_event']);
        $_SESSION['genesis_game'] = $toStore;
        self::persistActive($toStore);

        return $state;
    }

    public static function process(array $post): string
    {
        self::bootSession();
        $state = self::migrateState($_SESSION['genesis_game']);
        self::tick($state);

        if (isset($post['force_mission_outcome'])) {
            $forced = (string) $post['force_mission_outcome'];
            if (in_array($forced, ['success', 'injured', 'exhausted'], true)) {
                $state['ui']['force_mission_outcome'] = $forced;
            }
        }

        $action = (string) ($post['action'] ?? '');
        if ($action !== '') {
            self::apply($state, $action, [
                'zone_id' => $post['zone_id'] ?? null,
                'mutation_choice' => $post['mutation_choice'] ?? null,
                'creature_id' => $post['creature_id'] ?? null,
                'parent_a' => $post['parent_a'] ?? null,
                'parent_b' => $post['parent_b'] ?? null,
                'save_id' => $post['save_id'] ?? null,
                'save_label' => $post['save_label'] ?? null,
                'drone_type' => $post['drone_type'] ?? null,
                'escort_creature_id' => $post['escort_creature_id'] ?? null,
                'capture_escort_toggle' => $post['capture_escort_toggle'] ?? null,
                'drone_count' => $post['drone_count'] ?? null,
                'capture_count' => $post['capture_count'] ?? null,
                'study_count' => $post['study_count'] ?? null,
                'deploy_mode' => $post['deploy_mode'] ?? null,
                'return_screen' => $post['return_screen'] ?? null,
                'refine_source' => $post['refine_source'] ?? null,
            ]);
        }

        self::tick($state);
        FreePlayGoals::tick($state);
        self::save($state);

        return (string) ($state['screen'] ?? 'institute');
    }

    public static function apply(array &$state, string $action, array $payload = []): void
    {
        $state = self::migrateState($state);
        match ($action) {
            'select_zone' => self::selectZone($state, (string) ($payload['zone_id'] ?? '')),
            'recon' => self::recon($state),
            'analyze' => self::analyze($state),
            'study' => self::study($state),
            'cross' => self::cross($state, $payload),
            'mutate' => self::mutate($state, (string) ($payload['mutation_choice'] ?? 'ability')),
            'mission' => self::mission($state),
            'recover' => self::recover($state),
            'exhibit' => self::exhibit($state),
            'new_exploration' => self::newExploration($state),
            'focus_creature' => self::focusCreature($state, (string) ($payload['creature_id'] ?? '')),
            'focus_signal' => self::focusSignal($state, (string) ($payload['creature_id'] ?? '')),
            'save_game' => self::saveGame($state, (string) ($payload['save_label'] ?? '')),
            'load_game' => self::loadGame($state, (string) ($payload['save_id'] ?? '')),
            'delete_save' => self::deleteSave($state, (string) ($payload['save_id'] ?? '')),
            'open_baie' => self::openBaie($state),
            'depart' => self::openBaie($state),
            'farm' => self::farm($state),
            'restock' => self::restock($state),
            'refine_organic' => self::refineOrganic($state, (string) ($payload['refine_source'] ?? 'samples')),
            'craft_drone' => self::craftDrone($state, (string) ($payload['drone_type'] ?? DroneYard::TYPE_RECON)),
            'set_team' => self::setTeam($state, $payload),
            'continue_tutorial' => self::continueTutorial($state),
            'dismiss_tips' => self::dismissTips($state),
            'reset' => self::resetGame($state),
            default => null,
        };

        Tutorial::advance($state);
        FreePlayGoals::tick($state);
        GameQueries::refreshCampus($state);
    }

    private static function setTeam(array &$state, array $payload): void
    {
        $prev = $state['expedition'] ?? [];
        $mode = (string) ($payload['deploy_mode'] ?? 'full');
        $readyCap = DroneYard::countReady($state, DroneYard::TYPE_CAPTURE);

        $droneType = (string) ($prev['drone_type'] ?? DroneYard::TYPE_RECON);
        if ($mode === 'full' && isset($payload['drone_type']) && $payload['drone_type'] !== '') {
            $droneType = (string) $payload['drone_type'];
        }
        if (!isset(DroneYard::recipes()[$droneType])
            || in_array($droneType, [DroneYard::TYPE_CAPTURE, DroneYard::TYPE_STUDY], true)) {
            $droneType = DroneYard::TYPE_RECON;
        }
        if ($droneType === DroneYard::TYPE_ORBITAL
            && empty($state['export']['baie_unlocked'])
            && empty($state['export']['departed'])) {
            $droneType = DroneYard::TYPE_RECON;
        }

        // Intention (1–5) même si flotte insuffisante — le lancement prend min(voulu, prêts)
        $droneCount = array_key_exists('drone_count', $payload) && $payload['drone_count'] !== null && $payload['drone_count'] !== ''
            ? max(1, min(5, (int) $payload['drone_count']))
            : max(1, min(5, (int) ($prev['drone_count'] ?? 1)));
        $captureCount = array_key_exists('capture_count', $payload) && $payload['capture_count'] !== null && $payload['capture_count'] !== ''
            ? max(1, min(5, (int) $payload['capture_count']))
            : max(1, min(5, (int) ($prev['capture_count'] ?? 1)));
        $studyCount = array_key_exists('study_count', $payload) && $payload['study_count'] !== null && $payload['study_count'] !== ''
            ? max(0, min(5, (int) $payload['study_count']))
            : max(0, min(5, (int) ($prev['study_count'] ?? 0)));

        $escort = $prev['escort_creature_id'] ?? null;
        if (array_key_exists('escort_creature_id', $payload)) {
            $escort = $payload['escort_creature_id'] ?? null;
            if ($escort === '' || $escort === 'none') {
                $escort = null;
            }
        }
        $pool = GameQueries::analyzedCreatures($state);
        if ($escort !== null && !isset($pool[(string) $escort])) {
            $escort = null;
        }

        // Créatures d’appui capture (0–3), bascule par id
        $capEscorts = $prev['capture_escorts'] ?? [];
        if (!is_array($capEscorts)) {
            $capEscorts = [];
        }
        $capEscorts = array_values(array_filter(array_map('strval', $capEscorts), static fn (string $id): bool => $id !== ''));
        $toggle = (string) ($payload['capture_escort_toggle'] ?? '');
        if ($toggle !== '') {
            if (isset($pool[$toggle])) {
                if (in_array($toggle, $capEscorts, true)) {
                    $capEscorts = array_values(array_filter($capEscorts, static fn (string $id): bool => $id !== $toggle));
                } elseif (count($capEscorts) < Economy::captureEscortMax()) {
                    $capEscorts[] = $toggle;
                }
            }
        }
        // Retirer absents / cible active (signal)
        $targetId = (string) ($state['creature']['id'] ?? '');
        $capEscorts = array_values(array_filter(
            $capEscorts,
            static fn (string $id): bool => $id !== $targetId && isset($pool[$id])
        ));
        $capEscorts = array_slice($capEscorts, 0, Economy::captureEscortMax());

        $state['expedition'] = [
            'drone_type' => $droneType,
            'escort_creature_id' => $escort,
            'capture_escorts' => $capEscorts,
            'drone_count' => $droneCount,
            'capture_count' => $captureCount,
            'study_count' => $studyCount,
        ];
        $state['ui']['team_configured'] = true;

        $readyStudy = DroneYard::countReady($state, DroneYard::TYPE_STUDY);
        $capChance = Economy::captureSuccessChance($state, min($captureCount, max(1, $readyCap)));
        $mutChance = Economy::mutateSuccessChance($state['creature'] ?? [], min($studyCount, $readyStudy));
        $capEscortN = count(Economy::captureEscortIds($state));

        if ($mode === 'capture') {
            self::event(
                $state,
                'Effectif de capture',
                sprintf(
                    '×%d drone(s) · %d créature(s) · ~%d%%.',
                    min($captureCount, max(1, $readyCap)),
                    $capEscortN,
                    $capChance
                )
            );
            $state['screen'] = 'lab';
            // Pas de flash permanent : event suffit
            return;
        }
        if ($mode === 'study') {
            self::event(
                $state,
                'Effectif d’analyse',
                sprintf('×%d drone(s) d’analyse · mutation ~%d%%.', min($studyCount, $readyStudy), $mutChance)
            );
            $state['screen'] = 'lab';
            // Event suffit — pas de flash collé

            return;
        }

        $selZone = $state['world']['zones'][(string) ($state['world']['selected_zone'] ?? '')] ?? [];
        $reconChance = self::explorationSuccessChance($state, is_array($selZone) ? $selZone : [], [
            'drone_type' => $droneType,
            'drone_count' => $droneCount,
            'escort_id' => $escort,
        ]);
        self::event(
            $state,
            'Effectifs validés',
            sprintf(
                'Sortie ×%d sondes (~%d%%) · prise allouée ×%d · analyse ×%d.',
                $droneCount,
                $reconChance,
                $captureCount,
                $studyCount
            )
        );
        $state['screen'] = (string) ($payload['return_screen'] ?? $state['screen'] ?? 'world');
        if (!in_array($state['screen'], ['world', 'lab', 'institute'], true)) {
            $state['screen'] = 'world';
        }
    }

    private static function continueTutorial(array &$state): void
    {
        if (($state['tutorial']['step'] ?? '') !== Tutorial::STEP_WELCOME) {
            $state['screen'] = 'world';
            return;
        }
        $state['tutorial']['completed'][] = Tutorial::STEP_WELCOME;
        $state['tutorial']['step'] = Tutorial::STEP_RECON;
        $assist = Tutorial::assistant($state);
        $state['ui']['genesis'] = [
            'marker' => $assist['marker'],
            'message' => $assist['message'],
        ];
        self::event($state, 'Fil ouvert', 'La Plaine d’écho est la première porte.');
        $state['screen'] = 'world';
    }

    private static function assertFeature(array &$state, string $feature, string $message): bool
    {
        if (Tutorial::can($state, $feature)) {
            return true;
        }
        $stepHint = self::featureLockHint($feature, $state);
        self::event($state, 'Étape encore fermée', $message !== '' ? $message : $stepHint);
        self::voice($state, 'Observation', $stepHint);

        return false;
    }

    /** Pourquoi une action est verrouillée (tutoriel). */
    private static function featureLockHint(string $feature, array $state): string
    {
        $step = (string) ($state['tutorial']['step'] ?? '');
        $map = [
            'analyze' => 'Capture & analyse : d’abord un signal vivant sur la carte (exploration).',
            'mutate' => 'Mutation : capturez et étudiez deux formes, puis l’assistant ouvrira le labo de mutation.',
            'mission' => 'Mission terrain : composez une équipe, muter si demandé, puis le Terrain s’ouvre.',
            'cross' => 'Croisement : réussissez une mission terrain d’abord — le croisement vient après.',
            'museum' => 'Musée : créez un hybride, puis exposez-le.',
            'farm' => 'Récolte : disponible après votre première capture.',
            'craft' => 'Assemblage drones : après la deuxième zone touchée (ou une 2ᵉ forme).',
            'team' => 'Composition d’équipe : après une mutation réussie.',
            'baie' => 'Spatio-port : boucle berceau complète (MVE).',
        ];

        $base = $map[$feature] ?? 'Cette action s’ouvrira plus loin dans le fil.';
        if ($step !== '' && $step !== Tutorial::STEP_FREE) {
            return $base . ' (étape actuelle : ' . $step . ')';
        }

        return $base;
    }

    /** Complète les timers prêts. */
    public static function tick(array &$state): void
    {
        $completed = TaskRunner::collectCompleted($state);
        foreach ($completed as $task) {
            self::completeTask($state, $task);
        }
    }

    private static function migrateState(array $state): array
    {
        $base = StateFactory::initial();
        if (!isset($state['tasks']) || !is_array($state['tasks'])) {
            $state['tasks'] = [];
        }
        if (!isset($state['resources']['biomass'])) {
            $state['resources']['biomass'] = 2;
        }
        if (!isset($state['drones']['fleet']) || !is_array($state['drones']['fleet'])) {
            $state['drones'] = $base['drones'];
        }
        if (!isset($state['ui']['next_task_seq'])) {
            $state['ui']['next_task_seq'] = 0;
        }
        if (!isset($state['ui']['next_drone_seq'])) {
            $state['ui']['next_drone_seq'] = 10;
        }
        // Merge orbital zones if missing
        foreach ($base['world']['zones'] as $id => $zone) {
            if (!isset($state['world']['zones'][$id])) {
                $state['world']['zones'][$id] = $zone;
            } else {
                $state['world']['zones'][$id] = array_merge($zone, $state['world']['zones'][$id]);
            }
        }
        if (!isset($state['export']['baie_unlocked'])) {
            $state['export']['baie_unlocked'] = !empty($state['export']['departed']);
        }
        if (!isset($state['export']['deep_space_signal'])) {
            $state['export']['deep_space_signal'] = false;
        }
        FreePlayGoals::ensure($state);
        Phase2::ensureOrbitalZones($state);
        if (!isset($state['tutorial'])) {
            $state['tutorial'] = Tutorial::defaultState();
            // legacy sessions: skip tutorial if already progressed
            if (count(GameQueries::analyzedCreatures($state)) >= 2) {
                $state['tutorial'] = [
                    'step' => Tutorial::STEP_FREE,
                    'completed' => Tutorial::order(),
                    'active' => false,
                ];
            }
        }
        if (!isset($state['expedition'])) {
            $state['expedition'] = [
                'drone_type' => DroneYard::TYPE_RECON,
                'escort_creature_id' => null,
                'drone_count' => 1,
                'capture_count' => 1,
            ];
        }
        if (!isset($state['expedition']['drone_count'])) {
            $state['expedition']['drone_count'] = 1;
        }
        if (!isset($state['expedition']['capture_count'])) {
            $state['expedition']['capture_count'] = 1;
        }
        if (!isset($state['expedition']['study_count'])) {
            $state['expedition']['study_count'] = 0;
        }
        if (!isset($state['expedition']['capture_escorts']) || !is_array($state['expedition']['capture_escorts'])) {
            $legacy = $state['expedition']['escort_creature_id'] ?? null;
            $state['expedition']['capture_escorts'] = ($legacy !== null && $legacy !== '' && $legacy !== 'none')
                ? [(string) $legacy]
                : [];
        }
        // Zone unlock flags for natal
        foreach (Catalog::zones() as $id => $def) {
            if (!isset($state['world']['zones'][$id])) {
                $starter = !empty($def['starter']);
                $state['world']['zones'][$id] = array_merge($def, [
                    'explored' => false,
                    'scan' => 0,
                    'layer' => 'natal',
                    'unlocked' => $starter,
                    'locked' => !$starter,
                    'species_found' => [],
                ]);
            } else {
                if (!isset($state['world']['zones'][$id]['unlocked'])) {
                    $starter = !empty($def['starter']);
                    $state['world']['zones'][$id]['unlocked'] = $starter || !empty($state['world']['zones'][$id]['explored']);
                    $state['world']['zones'][$id]['locked'] = empty($state['world']['zones'][$id]['unlocked']);
                }
                if (!isset($state['world']['zones'][$id]['scan'])) {
                    $state['world']['zones'][$id]['scan'] = !empty($state['world']['zones'][$id]['explored']) ? 100 : 0;
                }
                $state['world']['zones'][$id] = array_merge($def, $state['world']['zones'][$id]);
            }
        }
        // Baie ouverte : ne force PAS toutes les orbites (chaîne progressive)
        // Les saves legacy qui avaient tout ouvert le gardent via flags unlocked déjà stockés.
        if ((!empty($state['export']['baie_unlocked']) || !empty($state['export']['departed']))
            && empty($state['ui']['orbital_chain_v2'])) {
            // Migration douce : si aucune orbite ouverte, ouvrir la starter
            $anyOrbitalOpen = false;
            foreach ($state['world']['zones'] as $z) {
                if (($z['layer'] ?? '') === 'orbital' && !empty($z['unlocked']) && empty($z['locked'])) {
                    $anyOrbitalOpen = true;
                    break;
                }
            }
            if (!$anyOrbitalOpen) {
                Phase2::unlockStarterOrbitals($state);
            }
            $state['ui']['orbital_chain_v2'] = true;
        }

        return $state;
    }

    private static function resetGame(array &$state): void
    {
        $state = StateFactory::initial();
        try {
            $id = GameRepository::create($state, 'Partie en cours', true);
            $state['ui']['db_game_id'] = $id;
            $_SESSION['genesis_game_id'] = $id;
        } catch (\Throwable $e) {
            unset($state['ui']['db_game_id'], $_SESSION['genesis_game_id']);
        }
        $assist = Tutorial::assistant($state);
        $state['ui']['latest_event'] = [
            'label' => 'Nouvelle partie',
            'message' => 'L’Institut recommence à zéro sur Aster-0.',
        ];
        $state['ui']['genesis'] = [
            'marker' => $assist['marker'],
            'message' => $assist['message'],
        ];
        $state['screen'] = 'institute';
    }

    // ------------------------------------------------------------------
    // Actions
    // ------------------------------------------------------------------

    private static function event(array &$state, string $label, string $message): void
    {
        $state['ui']['latest_event'] = ['label' => $label, 'message' => $message];
    }

    private static function voice(array &$state, string $marker, string $message): void
    {
        $state['ui']['genesis'] = ['marker' => $marker, 'message' => $message];
    }

    private static function memory(array &$state, string $line): void
    {
        $state['heritage']['memories'][] = $line;
    }

    private static function nextId(array &$state): string
    {
        $seq = (int) ($state['ui']['next_creature_seq'] ?? 2);
        $state['ui']['next_creature_seq'] = $seq + 1;

        return 'c' . $seq;
    }

    private static function archiveActive(array &$state): void
    {
        $c = $state['creature'] ?? [];
        if (empty($c['discovered']) && empty($c['analyzed'])) {
            return;
        }
        $id = (string) ($c['id'] ?? 'c1');
        if ($id === '' || $id === '0') {
            return;
        }
        // Conservé au bestiaire (capturé) ou en file « signaux en attente » (contact non pris)
        $state['bestiary'][$id] = $c;
    }

    /** Active un signal en attente (discovered, non capturé) pour pouvoir retenter la prise. */
    private static function focusPendingSignal(array &$state, ?string $preferId = null): bool
    {
        $pending = GameQueries::pendingSignals($state);
        if ($pending === []) {
            return false;
        }
        $id = $preferId !== null && $preferId !== '' && isset($pending[$preferId])
            ? $preferId
            : (string) array_key_first($pending);
        $signal = $pending[$id];
        self::archiveActive($state);
        $state['creature'] = $signal;
        $state['creature']['id'] = $id;
        $state['world']['signal'] = $signal['species'] ?? 'Signal vivant';
        $zoneId = (string) ($signal['zone_id'] ?? '');
        if ($zoneId !== '' && isset($state['world']['zones'][$zoneId])) {
            $state['world']['selected_zone'] = $zoneId;
            $state['world']['risk'] = $state['world']['zones'][$zoneId]['risk'] ?? 'modéré';
        }

        return true;
    }

    private static function selectZone(array &$state, string $zoneId): void
    {
        $zone = $state['world']['zones'][$zoneId] ?? Catalog::zone($zoneId);
        if ($zone === null || !is_array($zone)) {
            self::event($state, 'Zone invalide', 'Cette zone n’existe pas.');
            return;
        }
        $layer = (string) ($zone['layer'] ?? 'natal');
        if (empty($zone['unlocked']) || !empty($zone['locked'])) {
            self::event($state, 'Zone fermée', 'Encore inaccessible — explorez d’abord les terres voisines.');
            $state['screen'] = 'world';
            return;
        }
        // Accuser réception de la surprise d’ouverture
        if (!empty($state['world']['zones'][$zoneId]['newly_unlocked'])) {
            unset($state['world']['zones'][$zoneId]['newly_unlocked']);
            self::voice(
                $state,
                'Observation',
                sprintf('Tu poses le pied sur %s. Lis le terrain avant de forcer le contact.', $zone['name'] ?? $zoneId)
            );
        }
        if ($layer === 'orbital' && empty($state['export']['baie_unlocked'])) {
            self::event($state, 'Orbite fermée', 'Inaccessible pour l’instant.');
            $state['screen'] = 'world';
            return;
        }
        $state['world']['selected_zone'] = $zoneId;
        $state['world']['risk'] = $zone['risk'] ?? 'modéré';
        self::event($state, 'Zone choisie', (string) ($zone['name'] ?? $zoneId));
        if (!empty($state['tutorial']['active'])) {
            $assist = Tutorial::assistant($state);
            self::voice($state, $assist['marker'], $assist['message']);
        }
        $state['screen'] = 'world';
    }

    private static function recon(array &$state): void
    {
        if (!self::assertFeature($state, 'recon', 'L’exploration n’est pas encore ouverte.')) {
            return;
        }
        if (TaskRunner::hasRunningType($state, 'recon') || TaskRunner::hasRunningType($state, 'orbital')) {
            self::event($state, 'Recon déjà en cours', 'L’équipe est encore dehors.');
            return;
        }

        $zoneId = (string) ($state['world']['selected_zone'] ?? '');
        $zone = $state['world']['zones'][$zoneId] ?? null;
        if ($zoneId === '' || $zone === null) {
            $zoneId = 'plaine';
            $state['world']['selected_zone'] = $zoneId;
            $zone = $state['world']['zones'][$zoneId] ?? null;
        }
        if ($zone === null) {
            self::event($state, 'Zone invalide', 'Sélectionnez une zone débloquée.');
            return;
        }

        $layer = (string) ($zone['layer'] ?? 'natal');
        if (empty($zone['unlocked']) || !empty($zone['locked'])) {
            self::event($state, 'Zone fermée', 'Encore inaccessible.');
            $state['screen'] = 'world';
            return;
        }
        if ($layer === 'orbital' && empty($state['export']['baie_unlocked'])) {
            self::event($state, 'Orbite fermée', 'Spatio-port requis (fin de tutoriel + carte natale).');
            $state['screen'] = 'world';
            return;
        }

        $droneType = $layer === 'orbital'
            ? DroneYard::TYPE_ORBITAL
            : (string) ($state['expedition']['drone_type'] ?? DroneYard::TYPE_RECON);
        if ($droneType === DroneYard::TYPE_CAPTURE) {
            $droneType = DroneYard::TYPE_RECON;
        }
        if ($layer === 'orbital') {
            $droneType = DroneYard::TYPE_ORBITAL;
        }

        $wantDrones = max(1, min(5, (int) ($state['expedition']['drone_count'] ?? 1)));
        $ready = DroneYard::countReady($state, $droneType);
        $droneCount = min($wantDrones, max(1, $ready));
        $droneIds = DroneYard::reserveMany($state, $droneType, $droneCount);
        if ($droneIds === []) {
            self::event($state, 'Pas de drone', 'Aucun ' . DroneYard::label($droneType) . ' disponible.');
            $state['screen'] = 'institute';
            return;
        }
        $droneCount = count($droneIds);

        $cost = Economy::reconCost($zone, $droneCount);
        if ((int) ($state['resources']['logistics'] ?? 0) < $cost) {
            DroneYard::releaseMany($state, $droneIds, 'ready');
            self::event($state, 'Vol impossible', sprintf('Il faut %d logistique pour %s.', $cost, $zone['name'] ?? $zoneId));
            return;
        }

        $state['resources']['logistics'] -= $cost;
        $escortId = $state['expedition']['escort_creature_id'] ?? null;
        $escortCreature = null;
        if ($escortId) {
            $pool = GameQueries::analyzedCreatures($state);
            $escortCreature = $pool[(string) $escortId] ?? null;
        }
        $duration = Economy::taskDuration($layer === 'orbital' ? 'orbital' : 'recon', [
            'risk' => $zone['risk'] ?? 'modéré',
            'escort' => $escortCreature,
        ]);
        // Plus de drones : un peu plus long mais plus de rendement au retour
        $duration = max(3, $duration + ($droneCount - 1) * 2);
        if (($_ENV['GENESIS_INSTANT'] ?? getenv('GENESIS_INSTANT') ?: '') === '1'
            || (defined('GENESIS_INSTANT') && GENESIS_INSTANT)) {
            $duration = 0;
        }
        TaskRunner::start($state, $layer === 'orbital' ? 'orbital' : 'recon', 'Recon : ' . ($zone['name'] ?? $zoneId), $duration, [
            'zone_id' => $zoneId,
            'drone_id' => $droneIds[0],
            'drone_ids' => $droneIds,
            'drone_type' => $droneType,
            'drone_count' => $droneCount,
            'cost' => $cost,
            'escort_id' => $escortId,
        ]);
        self::event(
            $state,
            'Vol lancé',
            sprintf('%s — %d sonde(s) en route.', $zone['name'] ?? $zoneId, $droneCount)
        );
        $state['screen'] = 'world';
        self::tick($state);
    }

    private static function analyze(array &$state): void
    {
        if (!self::assertFeature($state, 'analyze', 'Pas encore — explorez d’abord.')) {
            return;
        }
        if (empty($state['creature']['discovered'])) {
            self::event($state, 'Analyse impossible', 'Aucun signal. Explorez d’abord.');
            return;
        }
        if (!empty($state['creature']['analyzed'])) {
            self::event($state, 'Déjà analysé', 'Ce spécimen est déjà catalogué.');
            return;
        }
        if (TaskRunner::hasRunningType($state, 'analyze')) {
            self::event($state, 'Analyse en cours', 'Le laboratoire travaille encore.');
            return;
        }
        $readyCap = DroneYard::countReady($state, DroneYard::TYPE_CAPTURE);
        if ($readyCap < 1) {
            self::event($state, 'Pas de drone de capture', 'Il en faut un pour tenter la prise.');
            return;
        }
        $wantCap = max(1, min(5, (int) ($state['expedition']['capture_count'] ?? min(5, $readyCap))));
        $capIds = DroneYard::reserveMany($state, DroneYard::TYPE_CAPTURE, min($wantCap, $readyCap));
        if ($capIds === []) {
            self::event($state, 'Pas de drone de capture', 'Aucun prêt.');
            return;
        }
        $capCount = count($capIds);
        $chance = Economy::captureSuccessChance($state, $capCount);
        $duration = Economy::taskDuration('analyze', [
            'creature' => $state['creature'] ?? [],
        ]);
        TaskRunner::start($state, 'analyze', 'Tentative de capture', $duration, [
            'drone_id' => $capIds[0],
            'drone_ids' => $capIds,
            'capture_count' => $capCount,
            'success_chance' => $chance,
            'creature_id' => $state['creature']['id'] ?? null,
        ]);
        self::event(
            $state,
            'Capture lancée',
            sprintf('%d drone(s) de prise · chance estimée ~%d%%.', $capCount, $chance)
        );
        $state['screen'] = 'lab';
        self::tick($state);
    }

    /** Approfondit la lecture d’un spécimen déjà analysé (connaissance progressive). */
    private static function study(array &$state): void
    {
        if (!self::assertFeature($state, 'analyze', 'Pas encore.')) {
            return;
        }
        if (empty($state['creature']['analyzed'])) {
            self::event($state, 'Étude impossible', 'Il faut d’abord une première analyse.');
            return;
        }
        if (CreatureFactory::knowledge($state['creature']) >= 100) {
            self::event($state, 'Fiche complète', 'L’institut connaît déjà cette forme.');
            return;
        }
        if (TaskRunner::hasRunningType($state, 'study')) {
            self::event($state, 'Étude en cours', 'Le laboratoire lit encore.');
            return;
        }
        $duration = Economy::taskDuration('analyze', [
            'creature' => $state['creature'] ?? [],
        ]);
        // Étude un peu plus courte qu’une première analyse
        if ($duration > 0) {
            $duration = max(3, $duration - 2);
        }
        TaskRunner::start($state, 'study', 'Étude approfondie', $duration, [
            'creature_id' => $state['creature']['id'] ?? null,
        ]);
        self::event($state, 'Étude lancée', 'On creuse la lecture du spécimen.');
        $state['screen'] = 'bestiary';
        self::tick($state);
    }

    private static function cross(array &$state, array $payload): void
    {
        if (!self::assertFeature($state, 'cross', 'Pas encore — une autre étape d’abord.')) {
            return;
        }
        if (TaskRunner::hasRunningType($state, 'cross')) {
            self::event($state, 'Croisement en cours', 'Attendez la fin du cycle.');
            return;
        }
        $crossCost = Economy::crossCost();
        if ((int) ($state['resources']['organic'] ?? 0) < $crossCost) {
            self::event($state, 'Croisement refusé', sprintf('Il faut %d organique.', $crossCost));
            $state['screen'] = 'lab';
            return;
        }
        if (count(GameQueries::distinctAnalyzedSpecies($state)) < 2) {
            self::event($state, 'Croisement impossible', 'Il faut deux espèces analysées distinctes.');
            $state['screen'] = 'world';
            return;
        }

        $pool = GameQueries::analyzedCreatures($state);
        $idA = (string) ($payload['parent_a'] ?? '');
        $idB = (string) ($payload['parent_b'] ?? '');
        if ($idA === '' || $idB === '' || $idA === $idB || !isset($pool[$idA], $pool[$idB])) {
            $distinct = array_values(GameQueries::distinctAnalyzedSpecies($state));
            $idA = (string) ($distinct[0]['id'] ?? '');
            $idB = (string) ($distinct[1]['id'] ?? '');
        }

        $state['resources']['organic'] -= $crossCost;
        $wantStudy = max(0, min(5, (int) ($state['expedition']['study_count'] ?? 0)));
        $readyStudy = DroneYard::countReady($state, DroneYard::TYPE_STUDY);
        $studyN = min($wantStudy, $readyStudy);
        $studyIds = $studyN > 0 ? DroneYard::reserveMany($state, DroneYard::TYPE_STUDY, $studyN) : [];
        $studyN = count($studyIds);
        $crossChance = Economy::crossSuccessChance($state, $studyN);
        $parentIntel = (int) (($pool[$idA]['stats']['intelligence'] ?? 0) + ($pool[$idB]['stats']['intelligence'] ?? 0));
        $duration = Economy::taskDuration('cross', [
            'creature' => $pool[$idA] ?? [],
            'parent_intel' => $parentIntel,
        ]);
        TaskRunner::start($state, 'cross', 'Croisement génétique', $duration, [
            'parent_a' => $idA,
            'parent_b' => $idB,
            'success_chance' => $crossChance,
            'study_drone_ids' => $studyIds,
            'study_count' => $studyN,
        ]);
        self::event(
            $state,
            'Croisement lancé',
            sprintf('Réussite ~%d%% · drones d’analyse ×%d · incubation %ds.', $crossChance, $studyN, $duration)
        );
        $state['screen'] = 'lab';
        self::tick($state);
    }

    private static function saveGame(array &$state, string $label): void
    {
        try {
            // Snapshot nommé + maj partie active
            $meta = SaveStore::save($state, $label !== '' ? $label : ('Archive — ' . date('d/m H:i')));
            $activeId = (int) ($state['ui']['db_game_id'] ?? $_SESSION['genesis_game_id'] ?? 0);
            if ($activeId > 0) {
                GameRepository::update($activeId, $state);
            }
            $state['ui']['last_save_id'] = $meta['id'];
            self::event(
                $state,
                'Mémoire archivée',
                sprintf('%s.', $meta['label'])
            );
            self::voice($state, 'Continuité', 'L’archive de l’Institut s’enrichit.');
        } catch (\Throwable $e) {
            self::event($state, 'Archive impossible', $e->getMessage());
        }
        $state['screen'] = 'institute';
    }

    private static function loadGame(array &$state, string $id): void
    {
        try {
            $gameId = (int) $id;
            $loaded = GameRepository::load($gameId);
            $base = StateFactory::initial();
            $state = array_merge($base, $loaded);
            $state['world'] = array_merge($base['world'], $loaded['world'] ?? []);
            $state['world']['zones'] = $base['world']['zones'];
            foreach ($loaded['world']['zones'] ?? [] as $zid => $zone) {
                $state['world']['zones'][$zid] = array_merge(
                    $base['world']['zones'][$zid] ?? [],
                    is_array($zone) ? $zone : []
                );
            }
            $state['resources'] = array_merge($base['resources'], $loaded['resources'] ?? []);
            $state['campus'] = array_merge($base['campus'], $loaded['campus'] ?? []);
            $state['export'] = array_merge($base['export'], $loaded['export'] ?? []);
            $state['ui'] = array_merge($base['ui'], $loaded['ui'] ?? []);
            $state['heritage'] = array_merge($base['heritage'], $loaded['heritage'] ?? []);
            $state['tutorial'] = $loaded['tutorial'] ?? $base['tutorial'];
            $state['bestiary'] = $loaded['bestiary'] ?? [];
            $state['creature'] = $loaded['creature'] ?? $base['creature'];
            $state['drones'] = $loaded['drones'] ?? $base['drones'];
            $state['tasks'] = $loaded['tasks'] ?? [];
            $state['museum'] = $loaded['museum'] ?? $base['museum'];
            $state = self::migrateState($state);
            GameRepository::setActive($gameId);
            $state['ui']['db_game_id'] = $gameId;
            $_SESSION['genesis_game_id'] = $gameId;
            self::event($state, 'Fil repris', 'La partie reprend.');
            self::voice($state, 'Continuité', Tutorial::assistant($state)['message']);
        } catch (\Throwable $e) {
            self::event($state, 'Chargement impossible', $e->getMessage());
        }
        $state['screen'] = 'institute';
    }

    private static function deleteSave(array &$state, string $id): void
    {
        $ok = false;
        try {
            $ok = GameRepository::delete((int) $id);
        } catch (\Throwable $e) {
            $ok = false;
        }
        self::event(
            $state,
            $ok ? 'Archive effacée' : 'Effacement impossible',
            $ok ? 'Entrée retirée de la mémoire.' : 'Partie introuvable.'
        );
        $state['screen'] = 'institute';
    }

    private static function mutate(array &$state, string $choice): void
    {
        if (!self::assertFeature($state, 'mutate', 'Pas encore.')) {
            return;
        }
        if (TaskRunner::hasRunningType($state, 'mutate')) {
            self::event($state, 'Mutation en cours', 'Attendez la fin.');
            return;
        }
        $mutateCost = Economy::mutateCost();
        if ((int) ($state['resources']['samples'] ?? 0) < $mutateCost) {
            self::event(
                $state,
                'Mutation refusée',
                sprintf(
                    'Il faut %d prélèvement(s) (vous : %d). Explorez avec escorte ou capturez.',
                    $mutateCost,
                    (int) ($state['resources']['samples'] ?? 0)
                )
            );
            return;
        }
        if (empty($state['creature']['analyzed']) || !empty($state['creature']['mutated'])) {
            self::event($state, 'Mutation impossible', 'Spécimen non éligible.');
            return;
        }
        if (!in_array($choice, ['stats', 'ability'], true)) {
            $choice = 'ability';
        }
        $wantStudy = max(0, min(5, (int) ($state['expedition']['study_count'] ?? 0)));
        $readyStudy = DroneYard::countReady($state, DroneYard::TYPE_STUDY);
        $studyN = min($wantStudy, $readyStudy);
        $studyIds = $studyN > 0 ? DroneYard::reserveMany($state, DroneYard::TYPE_STUDY, $studyN) : [];
        $studyN = count($studyIds);
        $mutChance = Economy::mutateSuccessChance($state['creature'], $studyN);
        $state['resources']['samples'] = (int) ($state['resources']['samples'] ?? 0) - $mutateCost;
        $duration = Economy::taskDuration('mutate', [
            'creature' => $state['creature'] ?? [],
        ]);
        TaskRunner::start($state, 'mutate', 'Mutation contrôlée', $duration, [
            'choice' => $choice,
            'creature_id' => $state['creature']['id'] ?? null,
            'success_chance' => $mutChance,
            'study_drone_ids' => $studyIds,
            'study_count' => $studyN,
        ]);
        self::event(
            $state,
            'Mutation lancée',
            sprintf(
                'Réussite ~%d%% · drones d’analyse ×%d. Échec = perte du spécimen.',
                $mutChance,
                $studyN
            )
        );
        $state['screen'] = 'lab';
        self::tick($state);
    }

    private static function mission(array &$state): void
    {
        if (!self::assertFeature($state, 'mission', 'Pas encore.')) {
            return;
        }
        if (empty($state['creature']['analyzed'])) {
            self::event($state, 'Mission impossible', 'Sans analyse, pas d’envoi.');
            return;
        }
        if (GameQueries::needsRecovery($state['creature'])) {
            self::event($state, 'Trop fragile', 'Soignez d’abord.');
            return;
        }
        if (TaskRunner::hasRunningType($state, 'mission')) {
            self::event($state, 'Mission en cours', 'Attendez le rapport de terrain.');
            return;
        }
        $chance = Economy::missionSuccessChance($state['creature']);
        $duration = Economy::taskDuration('mission', [
            'creature' => $state['creature'] ?? [],
        ]);
        TaskRunner::start($state, 'mission', 'Mission de terrain', $duration, [
            'creature_id' => $state['creature']['id'] ?? null,
            'success_chance' => $chance,
        ]);
        self::event(
            $state,
            'Mission lancée',
            sprintf('Sortie %ds. Estimation succès ~%d%%.', $duration, $chance)
        );
        $state['screen'] = 'field';
        self::tick($state);
    }

    private static function farm(array &$state): void
    {
        if (!self::assertFeature($state, 'farm', 'Pas encore.')) {
            return;
        }
        if (TaskRunner::hasRunningType($state, 'farm')) {
            self::event($state, 'Récolte en cours', 'L’équipe est déjà dehors.');
            return;
        }
        $duration = Economy::taskDuration('farm');
        $before = (int) ($state['resources']['biomass'] ?? 0);
        TaskRunner::start($state, 'farm', 'Récolte de biomasse', $duration, [
            'before_biomass' => $before,
        ]);
        self::event($state, 'Récolte lancée', 'L’équipe part collecter de la biomasse.');
        $state['screen'] = 'institute';
        self::tick($state);
    }

    /** Convertit de la biomasse en logistique (réappro institut). */
    private static function restock(array &$state): void
    {
        if (!self::assertFeature($state, 'farm', 'Pas encore.')) {
            return;
        }
        if (TaskRunner::hasRunningType($state, 'restock')) {
            self::event($state, 'Réappro en cours', 'Attendez la fin du cycle.');
            return;
        }
        $cost = Economy::restockBiomassCost();
        if ((int) ($state['resources']['biomass'] ?? 0) < $cost) {
            self::event($state, 'Biomasse insuffisante', sprintf('Il faut %d biomasse pour réapprovisionner.', $cost));
            return;
        }
        $state['resources']['biomass'] -= $cost;
        $duration = Economy::taskDuration('restock');
        TaskRunner::start($state, 'restock', 'Réapprovisionnement logistique', $duration, [
            'biomass_spent' => $cost,
        ]);
        self::event($state, 'Réappro lancé', sprintf('−%d biomasse. La logistique reviendra en fin de cycle.', $cost));
        $state['screen'] = 'institute';
        self::tick($state);
    }

    /**
     * Synthèse d’organique retirée : l’essence se gagne en mission terrain.
     */
    private static function refineOrganic(array &$state, string $source): void
    {
        self::event(
            $state,
            'Pas de distillation',
            'L’organique ne se distille plus. Envoie un spécimen en mission terrain.'
        );
        $state['screen'] = 'institute';
    }

    private static function craftDrone(array &$state, string $type): void
    {
        if (!self::assertFeature($state, 'craft', 'Assemblage : progressez dans le tutoriel pour ouvrir la flotte.')) {
            return;
        }
        $recipes = DroneYard::recipes();
        if (!isset($recipes[$type])) {
            $type = DroneYard::TYPE_RECON;
        }
        $recipe = $recipes[$type];
        // Types de drones débloqués au fil des étapes du tuto
        if (!DroneYard::canCraft($state, $type)) {
            $need = Tutorial::stepLabel(Tutorial::craftUnlockStep($type));
            self::event(
                $state,
                'Craft encore fermé',
                sprintf(
                    '%s : débloqué après « %s ».',
                    (string) ($recipe['label'] ?? $type),
                    $need
                )
            );
            $state['screen'] = 'institute';

            return;
        }
        if (!empty($recipe['requires_baie'])
            && empty($state['export']['baie_unlocked'])
            && empty($state['export']['departed'])) {
            self::event($state, 'Craft bloqué', 'Sonde orbitale : spatio-port requis.');
            return;
        }
        if (TaskRunner::hasRunningType($state, 'craft_drone')) {
            self::event($state, 'Assemblage en cours', 'Un drone est déjà en fabrication.');
            return;
        }
        $bio = (int) ($state['resources']['biomass'] ?? 0);
        $log = (int) ($state['resources']['logistics'] ?? 0);
        if ($bio < (int) $recipe['biomass'] || $log < (int) $recipe['logistics']) {
            self::event(
                $state,
                'Ressources insuffisantes',
                sprintf('Il faut %d biomasse et %d logistique.', (int) $recipe['biomass'], (int) $recipe['logistics'])
            );
            return;
        }
        $state['resources']['biomass'] -= (int) $recipe['biomass'];
        $state['resources']['logistics'] -= (int) $recipe['logistics'];
        $duration = Economy::taskDuration('craft_drone', ['duration' => (int) $recipe['duration']]);
        TaskRunner::start($state, 'craft_drone', 'Assemblage : ' . $recipe['label'], $duration, [
            'drone_type' => $type,
        ]);
        self::event($state, 'Assemblage', sprintf('%s en fabrication (%ds).', $recipe['label'], $duration));
        $state['screen'] = 'institute';
        self::tick($state);
    }

    private static function openBaie(array &$state): void
    {
        if (!Tutorial::can($state, 'baie')) {
            self::event($state, 'Trop tôt', 'Le spatio-port n’est pas encore accessible.');
            return;
        }
        $mve = MveEvaluator::evaluate($state);
        if (!$mve['complete']) {
            self::event($state, 'Spatio-port verrouillé', 'MVE ' . $mve['score'] . ' — cartographiez tout le berceau et complétez le patrimoine.');
            self::voice($state, 'Seuil', $mve['phrase']);
            $state['screen'] = 'institute';
            return;
        }
        if (!empty($state['export']['baie_unlocked'])) {
            self::event($state, 'Déjà ouvert', 'Le spatio-port est opérationnel. Explorez les zones orbitales.');
            $state['screen'] = 'world';
            return;
        }

        $state['export']['baie_unlocked'] = true;
        $state['export']['departed'] = true; // capacité hors berceau (pas une fin de partie)
        $state['export']['departed_at'] = date('c');
        $state['export']['manifest'] = ExportManifest::build($state, $mve);
        Phase2::ensureOrbitalZones($state);
        // Progression : une seule porte d’orbite s’ouvre d’abord
        Phase2::unlockStarterOrbitals($state);
        $state['world']['selected_zone'] = 'orbite_a';
        self::memory($state, 'Spatio-port ouvert : la Lune de brume devient lisible. Les autres orbites suivront.');
        self::event(
            $state,
            'Spatio-port ouvert',
            'La Baie n’est pas une fin : une première orbite s’offre à la lecture.'
        );
        self::voice(
            $state,
            'Continuité',
            'Projection prête. Assemblez une sonde extra-planétaire. Commencez par la Lune de brume.'
        );
        $state['screen'] = 'world';
    }

    private static function completeTask(array &$state, array $task): void
    {
        $type = (string) ($task['type'] ?? '');
        $payload = $task['payload'] ?? [];

        match ($type) {
            'recon', 'orbital' => self::finishRecon($state, $payload),
            'analyze' => self::finishAnalyze($state, $payload),
            'study' => self::finishStudy($state, $payload),
            'cross' => self::finishCross($state, $payload),
            'mutate' => self::finishMutate($state, $payload),
            'mission' => self::finishMission($state, $payload),
            'farm' => self::finishFarm($state),
            'restock' => self::finishRestock($state),
            'craft_drone' => self::finishCraft($state, $payload),
            default => null,
        };
    }

    private static function finishRecon(array &$state, array $payload): void
    {
        $zoneId = (string) ($payload['zone_id'] ?? '');
        $zone = $state['world']['zones'][$zoneId] ?? Catalog::zone($zoneId) ?? [];
        if (!is_array($zone)) {
            $zone = [];
        }
        $droneIds = $payload['drone_ids'] ?? [];
        if (!is_array($droneIds) || $droneIds === []) {
            $droneIds = array_filter([(string) ($payload['drone_id'] ?? '')]);
        }
        DroneYard::releaseMany($state, array_map('strval', $droneIds), 'ready');
        $droneCount = max(1, (int) ($payload['drone_count'] ?? count($droneIds) ?: 1));

        $successChance = self::explorationSuccessChance($state, $zone, $payload);
        $roll = random_int(1, 100);
        $forced = $state['ui']['force_mission_outcome'] ?? null;
        $instant = ($_ENV['GENESIS_INSTANT'] ?? getenv('GENESIS_INSTANT') ?: '') === '1'
            || (defined('GENESIS_INSTANT') && GENESIS_INSTANT);
        if ($forced === 'success') {
            $roll = 1;
        } elseif ($forced === 'injured') {
            $roll = 100;
        } elseif ($instant) {
            $roll = 1;
        }

        $zoneName = (string) ($zone['name'] ?? $zoneId);
        $risk = (string) ($zone['risk'] ?? 'modéré');
        $droneLabel = DroneYard::label((string) ($payload['drone_type'] ?? DroneYard::TYPE_RECON));
        $escortId = $payload['escort_id'] ?? $state['expedition']['escort_creature_id'] ?? null;
        $escortLine = 'aucune';
        $escort = null;
        if ($escortId) {
            $pool = GameQueries::analyzedCreatures($state);
            $escort = $pool[(string) $escortId] ?? null;
            if ($escort) {
                $bits = [(string) ($escort['name'] ?? $escort['species'] ?? $escortId)];
                if (!empty($escort['mutated'])) {
                    $bits[] = 'muté';
                }
                if (!empty($escort['is_hybrid'])) {
                    $bits[] = 'hybride';
                }
                $escortLine = implode(', ', $bits);
            }
        }

        if ($roll > $successChance) {
            // Échec : petit butin de secours (pas punitif à zéro)
            $state['resources']['biomass'] = (int) ($state['resources']['biomass'] ?? 0) + 1;
            $state['world']['last_report'] = sprintf(
                "RAPPORT D’EXPLORATION — ÉCHEC PARTIEL\nZone : %s\nRisque : %s\nÉquipe : %s ×%d · escorte %s\nLecture instable (%d / ~%d).\n+1 biomasse récupérée en repli.\nRéessayez avec plus de sondes ou une escorte adaptée.",
                $zoneName,
                $risk,
                $droneLabel,
                $droneCount,
                $escortLine,
                $roll,
                $successChance
            );
            self::event($state, 'Exploration fragile', sprintf('%s — peu de données, un peu de matière.', $zoneName));
            self::memory($state, sprintf('Recon %s : échec partiel.', $zoneName));
            $state['screen'] = 'world';
            return;
        }

        // ——— Cartographie progressive (scan 0–100) ———
        $scanBefore = (int) ($state['world']['zones'][$zoneId]['scan'] ?? 0);
        $scanGain = 18 + ($droneCount - 1) * 10;
        if ($escort && ($escort['status'] ?? '') === 'prête') {
            $stats = $escort['stats'] ?? [];
            $scanGain += min(12, intdiv(
                (int) ($stats['intelligence'] ?? 0) + (int) ($stats['vitesse'] ?? 0),
                3
            ));
            if (!empty($escort['mutated'])) {
                $scanGain += 4;
            }
            if (!empty($escort['is_hybrid'])) {
                $scanGain += 5;
            }
        }
        if ($instant || $forced === 'success') {
            $scanGain = max($scanGain, 100); // tests : une passe = cartographie complète
        }
        $scanAfter = min(100, $scanBefore + $scanGain);
        $state['world']['zones'][$zoneId]['scan'] = $scanAfter;
        $fullyMapped = $scanAfter >= 100;
        if ($fullyMapped) {
            $state['world']['zones'][$zoneId]['explored'] = true;
        }

        // ——— Ressources terrain (explo = prélèvements / bio / log — PAS d’organique) ———
        $bioGain = 1 + ($droneCount > 1 ? 1 : 0);
        $logGain = 0;
        // Prélèvements : lecture du terrain (mutation), pas d’essence organique
        $sampleGain = 1;
        if ($scanAfter >= 25 || $droneCount >= 2) {
            $logGain = 1;
        }
        if ($droneCount >= 3) {
            $sampleGain += 1; // sortie lourde : plus d’échantillons
        }
        if ($escort && ($escort['status'] ?? '') === 'prête') {
            $logGain += (!empty($escort['is_hybrid']) || (int) ($escort['stats']['intelligence'] ?? 0) >= Economy::escortIntelThreshold()) ? 1 : 0;
            $sampleGain += 1; // escorte prête : bonus prélèvements
            $state['resources']['samples'] = (int) ($state['resources']['samples'] ?? 0) + 1;
            if (!empty($escort['mutated']) || !empty($escort['is_hybrid'])) {
                $state['resources']['samples'] = (int) $state['resources']['samples'] + 1;
            }
            $escort['status'] = 'épuisée';
            $state['bestiary'][(string) $escortId] = $escort;
            if (($state['creature']['id'] ?? '') === $escortId) {
                $state['creature'] = $escort;
            }
        }
        $state['resources']['biomass'] = (int) ($state['resources']['biomass'] ?? 0) + $bioGain;
        if ($logGain > 0) {
            $state['resources']['logistics'] = (int) ($state['resources']['logistics'] ?? 0) + $logGain;
        }
        if ($sampleGain > 0) {
            $state['resources']['samples'] = (int) ($state['resources']['samples'] ?? 0) + $sampleGain;
        }

        // ——— Déblocage voisins (progression de carte, pas besoin de 100 %) ———
        $unlockThreshold = 40;
        $unlockedNames = [];
        $unlockedIds = [];
        if ($scanAfter >= $unlockThreshold || $fullyMapped) {
            foreach ($zone['unlocks'] ?? [] as $nextId) {
                if (isset($state['world']['zones'][$nextId])) {
                    $wasLocked = empty($state['world']['zones'][$nextId]['unlocked'])
                        || !empty($state['world']['zones'][$nextId]['locked']);
                    $state['world']['zones'][$nextId]['unlocked'] = true;
                    $state['world']['zones'][$nextId]['locked'] = false;
                    if ($wasLocked) {
                        $state['world']['zones'][$nextId]['newly_unlocked'] = true;
                        $unlockedNames[] = $state['world']['zones'][$nextId]['name'] ?? $nextId;
                        $unlockedIds[] = (string) $nextId;
                    }
                }
            }
        }
        if ($unlockedNames !== []) {
            // Surprise d’aventurier — bannière prioritaire (zones nommées, pas de boutons clones)
            $zoneCtas = [];
            foreach ($unlockedIds as $i => $uid) {
                $zoneCtas[] = [
                    'id' => (string) $uid,
                    'name' => (string) ($unlockedNames[$i] ?? ($state['world']['zones'][$uid]['name'] ?? $uid)),
                ];
            }
            $state['ui']['flash'] = [
                'type' => 'horizon',
                'title' => '✦ Nouvelle terre !',
                'message' => sprintf(
                    '%s se révèle au-delà de %s. Une voie s’ouvre — choisissez où envoyer les sondes.',
                    implode(' · ', $unlockedNames),
                    $zoneName
                ),
                'zones' => $zoneCtas,
            ];
            self::voice(
                $state,
                'Tension',
                sprintf(
                    'Arrête-toi. %s… je ne l’avais pas sur le plan. Vas-y. Regarde.',
                    implode(', ', $unlockedNames)
                )
            );
            self::event(
                $state,
                'Découverte de zone',
                sprintf('Accès ouvert : %s', implode(', ', $unlockedNames))
            );
        }
        if (!in_array($zoneName, $state['world']['known_zones'] ?? [], true)) {
            $state['world']['known_zones'][] = $zoneName;
        }

        // ——— Contact spécimen (pas garanti) ———
        $specimenChance = self::specimenFindChance($state, $zone, $payload, $scanAfter, $droneCount);
        $specRoll = random_int(1, 100);
        $tutorialWantsSpecimen = in_array(
            (string) ($state['tutorial']['step'] ?? ''),
            [Tutorial::STEP_RECON, Tutorial::STEP_ANALYZE, Tutorial::STEP_ZONE2, Tutorial::STEP_ANALYZE2],
            true
        );
        $findSpecimen = $specRoll <= $specimenChance;
        if ($instant || $forced === 'success') {
            $findSpecimen = true; // boucle de test déterministe
        }
        if ($tutorialWantsSpecimen && empty($state['creature']['discovered'])
            && count(GameQueries::analyzedCreatures($state)) < 1
            && $scanAfter >= 15) {
            $findSpecimen = true; // première capture tutoriel
        }

        $resourceLine = sprintf(
            'Butin terrain : +%d biomasse%s%s',
            $bioGain,
            $logGain > 0 ? sprintf(' · +%d logistique', $logGain) : '',
            $sampleGain > 0 ? sprintf(' · +%d prélèvement(s)', $sampleGain) : ''
        );

        if (!$findSpecimen) {
            $state['world']['risk'] = $risk;
            $state['world']['signal'] = null;
            $state['world']['last_report'] = sprintf(
                "RAPPORT D’EXPLORATION — CARTOGRAPHIE\nZone : %s\nRisque : %s\nÉquipe : %s ×%d · escorte %s\nScan zone : %d%% → %d%%%s\n%s\nAucun spécimen stabilisé ce passage.\n%s%s",
                $zoneName,
                $risk,
                $droneLabel,
                $droneCount,
                $escortLine,
                $scanBefore,
                $scanAfter,
                $fullyMapped ? ' (zone cartographiée)' : '',
                $resourceLine,
                $unlockedNames !== [] ? 'Nouveaux accès : ' . implode(', ', $unlockedNames) . "\n" : '',
                $scanAfter < 100 ? 'La zone n’est pas encore entièrement lue — renvoyez une équipe.' : 'Zone entièrement lue. D’autres formes peuvent encore y apparaître.'
            );
            self::memory($state, sprintf('Recon %s : scan %d%%, sans spécimen.', $zoneName, $scanAfter));
            if ($unlockedNames !== []) {
                self::event(
                    $state,
                    'Horizon nouveau',
                    sprintf('%s se dessine au-delà de %s.', implode(', ', $unlockedNames), $zoneName)
                );
            } else {
                self::event(
                    $state,
                    $fullyMapped ? 'Zone cartographiée' : 'Lecture terrain',
                    sprintf('%s — scan %d%%. %s', $zoneName, $scanAfter, $resourceLine)
                );
            }
            if (($zone['layer'] ?? 'natal') === 'orbital' && $fullyMapped) {
                Phase2::afterOrbitalSuccess($state);
            }
            $state['screen'] = 'world';
            return;
        }

        // Spécimen trouvé (contact = pas encore capturé ; species_found uniquement à la prise)
        $speciesKey = Catalog::pickZoneSpecies($zone, $state);
        $species = Catalog::speciesByKey($speciesKey) ?? [];
        $capturedHere = $state['world']['zones'][$zoneId]['species_found'] ?? [];
        if (!is_array($capturedHere)) {
            $capturedHere = [];
        }
        $alreadyCapturedHere = in_array($speciesKey, $capturedHere, true);

        // Signaux précédents : conservés en attente (reprise + capture plus tard)
        $prevPending = count(GameQueries::pendingSignals($state));
        self::archiveActive($state);
        $id = self::nextId($state);
        $state['creature'] = CreatureFactory::fromDiscovery($id, $speciesKey, $zoneId);
        // Marquer immédiatement en bestiaire-attente pour ne jamais le perdre
        $state['bestiary'][$id] = $state['creature'];
        $state['world']['risk'] = $risk;
        $state['world']['signal'] = $species['signal'] ?? 'Signal vivant';

        $pendingAfter = count(GameQueries::pendingSignals($state));
        $state['world']['last_report'] = sprintf(
            "RAPPORT D’EXPLORATION — CONTACT\nZone : %s\nScan : %d%% → %d%%%s\nÉquipe : %s ×%d · escorte %s\n%s\nSignal : %s\nEspèce contactée : %s (pas encore capturée)\nChance de contact estimée : ~%d%%\n%s%s%s",
            $zoneName,
            $scanBefore,
            $scanAfter,
            $fullyMapped ? ' (cartographie complète)' : '',
            $droneLabel,
            $droneCount,
            $escortLine,
            $resourceLine,
            $species['signal'] ?? 'vivant',
            $species['species'] ?? $speciesKey,
            $specimenChance,
            $alreadyCapturedHere ? "Forme déjà prise ici auparavant — nouveau spécimen possible.\n" : '',
            $prevPending > 0
                ? sprintf("Autres signaux encore en attente : %d (reprenez-les pour capturer plus tard).\n", $pendingAfter)
                : "Vous pouvez préparer flotte/escorte avant de tenter la capture — le signal reste en attente.\n",
            $unlockedNames !== [] ? 'Nouveaux accès : ' . implode(', ', $unlockedNames) : ''
        );
        self::memory($state, sprintf('Recon %s : contact %s (scan %d%%).', $zoneName, $species['species'] ?? $speciesKey, $scanAfter));

        // Flash : horizon + contact, ou contact seul — CTAs nommés (pas 2× « Explorer cette terre »)
        $zoneCtas = [];
        foreach ($unlockedIds as $i => $uid) {
            $zoneCtas[] = [
                'id' => (string) $uid,
                'name' => (string) ($unlockedNames[$i] ?? ($state['world']['zones'][$uid]['name'] ?? $uid)),
            ];
        }
        if ($unlockedNames !== []) {
            $state['ui']['flash'] = [
                'type' => 'horizon',
                'title' => '✦ Terre nouvelle + contact !',
                'message' => sprintf(
                    '%s s’ouvre… et ici, un vivant se montre (%s). Signal en attente : capturez au labo, ou explorez d’abord une nouvelle terre.',
                    implode(' · ', $unlockedNames),
                    $species['species'] ?? $speciesKey
                ),
                'zones' => $zoneCtas,
                'cta_lab' => true,
                'cta_lab_label' => sprintf('Capturer %s', $species['species'] ?? 'le signal'),
            ];
        } else {
            $state['ui']['flash'] = [
                'type' => 'contact',
                'title' => '◈ Contact vivant',
                'message' => sprintf(
                    '%s répond dans %s. Signal en attente — montez l’effectif, puis capturez au labo.',
                    $species['species'] ?? 'Une forme',
                    $zoneName
                ),
                'cta_lab' => true,
                'cta_lab_label' => sprintf('Capturer %s', $species['species'] ?? 'le signal'),
            ];
        }
        self::event(
            $state,
            'Contact',
            sprintf('%s — signal en attente (capture quand vous êtes prêt).', $species['species'] ?? 'Signal')
        );
        self::voice($state, 'Tension', 'Il ne fuit pas tout de suite. Prépare ta prise, puis tente.');
        if (($zone['layer'] ?? 'natal') === 'orbital' && $fullyMapped) {
            Phase2::afterOrbitalSuccess($state);
        }
        // Reste sur la carte pour la surprise
        $state['screen'] = 'world';
    }

    /** Chance 0–100 de stabiliser un spécimen ce passage. */
    private static function specimenFindChance(
        array $state,
        array $zone,
        array $payload,
        int $scanAfter,
        int $droneCount
    ): int {
        $chance = 28 + ($droneCount - 1) * 12;
        // Cartographie aide un peu, sans garantir
        $chance += min(15, intdiv($scanAfter, 8));
        $escortId = $payload['escort_id'] ?? null;
        if ($escortId) {
            $pool = GameQueries::analyzedCreatures($state);
            $escort = $pool[(string) $escortId] ?? null;
            if ($escort && ($escort['status'] ?? '') === 'prête') {
                $chance += 10;
                if (!empty($escort['mutated'])) {
                    $chance += 6;
                }
                if (!empty($escort['is_hybrid'])) {
                    $chance += 8;
                }
                $eb = (string) ($escort['biome'] ?? '');
                $zb = (string) ($zone['biome'] ?? '');
                if ($eb !== '' && $eb === $zb) {
                    $chance += 8;
                }
            }
        }
        $found = $zone['species_found'] ?? ($state['world']['zones'][$zone['id'] ?? '']['species_found'] ?? []);
        if (is_array($found) && $found !== []) {
            $chance -= min(12, count($found) * 4); // formes déjà croisées ici : un peu plus rare
        }
        $risk = (string) ($zone['risk'] ?? 'modéré');
        if ($risk === 'élevé') {
            $chance -= 5;
        } elseif ($risk === 'extrême') {
            $chance -= 10;
        }

        return max(12, min(78, $chance));
    }

    public static function explorationSuccessChance(array $state, array $zone, array $payload): int
    {
        $base = match ((string) ($zone['risk'] ?? 'modéré')) {
            'extrême' => 45,
            'élevé' => 65,
            default => 88,
        };
        $droneType = (string) ($payload['drone_type'] ?? DroneYard::TYPE_RECON);
        $droneCount = max(1, (int) ($payload['drone_count'] ?? 1));
        if ($droneType === DroneYard::TYPE_ORBITAL && ($zone['layer'] ?? '') === 'orbital') {
            $base += 10;
        }
        if ($droneType === DroneYard::TYPE_RECON) {
            $base += 5;
        }
        $base += min(15, ($droneCount - 1) * 7);
        $escortId = $payload['escort_id'] ?? null;
        if ($escortId) {
            $pool = GameQueries::analyzedCreatures($state);
            $escort = $pool[(string) $escortId] ?? null;
            if ($escort) {
                $stats = $escort['stats'] ?? [];
                $power = (int) ($stats['force'] ?? 0) + (int) ($stats['vitesse'] ?? 0)
                    + (int) ($stats['resistance'] ?? 0) + (int) ($stats['intelligence'] ?? 0);
                $base += min(20, intdiv($power, 2));
                if (!empty($escort['mutated'])) {
                    $base += 8;
                }
                if (!empty($escort['is_hybrid'])) {
                    $base += 10; // formes uniques : meilleure lecture terrain
                }
                // Affinité biome escorte / zone
                $escortBiome = mb_strtolower((string) ($escort['biome'] ?? ''));
                $zoneBiome = mb_strtolower((string) ($zone['biome'] ?? ''));
                $ebMain = explode('/', $escortBiome)[0] ?? $escortBiome;
                $base += self::biomeAffinityScore($ebMain, $zoneBiome);
                if (($escort['status'] ?? '') !== 'prête') {
                    $base -= 25;
                }
            }
        }

        return max(15, min(95, $base));
    }

    private static function finishAnalyze(array &$state, array $payload): void
    {
        $droneIds = $payload['drone_ids'] ?? [];
        if (!is_array($droneIds) || $droneIds === []) {
            $droneIds = array_filter([(string) ($payload['drone_id'] ?? '')]);
        }
        $capCount = max(1, (int) ($payload['capture_count'] ?? count($droneIds) ?: 1));
        if (empty($state['creature']['discovered'])) {
            DroneYard::releaseMany($state, array_map('strval', $droneIds), 'ready');
            return;
        }

        $chance = (int) ($payload['success_chance'] ?? Economy::captureSuccessChance($state, $capCount));
        $roll = random_int(1, 100);
        $forced = $state['ui']['force_mission_outcome'] ?? null;
        $instant = ($_ENV['GENESIS_INSTANT'] ?? getenv('GENESIS_INSTANT') ?: '') === '1'
            || (defined('GENESIS_INSTANT') && GENESIS_INSTANT);
        if ($forced === 'success' || $instant) {
            $roll = 1;
        } elseif ($forced === 'injured') {
            $roll = 100;
        }

        if ($roll > $chance) {
            // Échec de capture : un drone de prise est perdu, signal fuit
            $lostId = (string) ($droneIds[0] ?? '');
            if ($lostId !== '') {
                self::destroyDrone($state, $lostId);
            }
            $rest = array_slice(array_map('strval', $droneIds), 1);
            DroneYard::releaseMany($state, $rest, 'ready');
            $state['resources']['logistics'] = max(0, (int) ($state['resources']['logistics'] ?? 0) - 1);
            $state['resources']['organic'] = max(0, (int) ($state['resources']['organic'] ?? 0) - 1);
            $lostName = $state['creature']['name'] ?? 'signal';
            $lostId = (string) ($state['creature']['id'] ?? '');
            // Tentative ratée = le signal fuit (seule façon de le perdre une fois contacté)
            if ($lostId !== '' && isset($state['bestiary'][$lostId])) {
                unset($state['bestiary'][$lostId]);
            }
            $state['creature'] = CreatureFactory::blank(self::nextId($state));
            $state['world']['signal'] = null;
            $state['world']['last_report'] = sprintf(
                "RAPPORT DE CAPTURE — ÉCHEC\nCible : %s\nChance estimée : ~%d%%\nRésultat : la prise a échoué.\nPertes : 1 drone de capture · −1 logistique · −1 organique.\nLe signal s’est dissous (une tentative ratée le fait fuir).",
                $lostName,
                $chance
            );
            self::memory($state, 'Capture échouée — drone perdu, signal perdu.');
            self::event($state, 'Prise manquée', 'La forme s’est dérobée. Un drone ne reviendra pas. Le signal n’est plus en attente.');
            self::voice($state, 'Tension', 'Trop tôt, ou trop peu de force. La prochaine fois, prépare mieux.');
            $state['ui']['flash'] = [
                'type' => 'loss',
                'title' => 'Échappé',
                'message' => 'Capture ratée : drone perdu et signal dissipé. (Attendre sans tenter ne le fait pas fuir.)',
            ];
            $state['screen'] = 'world';
            return;
        }

        DroneYard::releaseMany($state, array_map('strval', $droneIds), 'ready');
        $state['creature'] = CreatureFactory::applyAnalysis($state['creature']);
        // Capture : prélèvements (pas d’organique — réservé aux missions terrain)
        $state['resources']['samples'] = (int) ($state['resources']['samples'] ?? 0) + 1;
        $state['bestiary'][(string) $state['creature']['id']] = $state['creature'];

        // Cataloguer la capture sur la zone (liste « capturés », pas les simples contacts)
        $zoneId = (string) ($state['creature']['zone_id'] ?? '');
        $speciesKey = (string) ($state['creature']['species_key'] ?? '');
        if ($zoneId !== '' && $speciesKey !== '' && isset($state['world']['zones'][$zoneId])) {
            $found = $state['world']['zones'][$zoneId]['species_found'] ?? [];
            if (!is_array($found)) {
                $found = [];
            }
            if (!in_array($speciesKey, $found, true)) {
                $found[] = $speciesKey;
            }
            $state['world']['zones'][$zoneId]['species_found'] = $found;
        }

        $species = (string) $state['creature']['species'];
        $k = CreatureFactory::knowledge($state['creature']);
        $report = sprintf(
            "RAPPORT DE CAPTURE — RÉUSSITE\nEspèce : %s\nBiome : %s\nChance de prise : ~%d%% (%d drone(s))\nConnaissance : %d%%\n+1 prélèvement.\nTraits profonds encore voilés — poursuivez l’étude au bestiaire.\n(L’organique ne se gagne qu’en mission terrain.)",
            $species,
            $state['creature']['biome'] ?? '—',
            $chance,
            $capCount,
            $k
        );
        $state['world']['last_report'] = $report;
        self::memory($state, sprintf('Capture : %s (%d%% connu) · +1 prélèvement.', $species, $k));
        self::event($state, 'Prise réussie', $species . ' au bestiaire · +1 prélèvement.');
        $state['ui']['flash'] = [
            'type' => 'contact',
            'title' => '◈ Capturé !',
            'message' => $species . ' retenu. +1 prélèvement. L’organique attend une mission terrain.',
        ];
        $state['screen'] = 'lab';
    }

    private static function destroyDrone(array &$state, string $droneId): void
    {
        $fleet = $state['drones']['fleet'] ?? [];
        $state['drones']['fleet'] = array_values(array_filter(
            $fleet,
            static fn (array $d): bool => (string) ($d['id'] ?? '') !== $droneId
        ));
    }

    private static function finishStudy(array &$state, array $payload): void
    {
        if (empty($state['creature']['analyzed'])) {
            return;
        }
        $before = CreatureFactory::knowledge($state['creature']);
        $intel = (int) ($state['creature']['stats']['intelligence'] ?? 0);
        $gain = 22 + min(12, intdiv($intel, 2));
        $state['creature'] = CreatureFactory::deepenKnowledge($state['creature'], $gain);
        $after = CreatureFactory::knowledge($state['creature']);
        $state['bestiary'][(string) $state['creature']['id']] = $state['creature'];
        $vis = CreatureFactory::visibility($state['creature']);
        $unlocked = [];
        if ($before < 45 && $after >= 45) {
            $unlocked[] = 'rareté';
        }
        if ($before < 50 && $after >= 50) {
            $unlocked[] = 'pureté';
        }
        if ($before < 60 && $after >= 60) {
            $unlocked[] = 'caractéristiques';
        }
        if ($before < 78 && $after >= 78) {
            $unlocked[] = 'capacités';
        }
        $state['world']['last_report'] = sprintf(
            "RAPPORT D’ÉTUDE\nSujet : %s\nConnaissance : %d%% → %d%%%s\n%s",
            $state['creature']['name'] ?? '—',
            $before,
            $after,
            $vis['complete'] ? ' (fiche complète)' : '',
            $unlocked !== [] ? 'Nouvelles lectures : ' . implode(', ', $unlocked) . '.' : 'La lecture s’affine.'
        );
        self::memory($state, sprintf('Étude : %s → %d%%.', $state['creature']['name'] ?? '?', $after));
        self::event(
            $state,
            $vis['complete'] ? 'Fiche complète' : 'Lecture approfondie',
            $unlocked !== []
                ? ('Révélé : ' . implode(', ', $unlocked))
                : sprintf('Connaissance %d%%.', $after)
        );
        $state['screen'] = 'bestiary';
    }

    private static function finishCross(array &$state, array $payload): void
    {
        $studyIds = $payload['study_drone_ids'] ?? [];
        if (is_array($studyIds)) {
            DroneYard::releaseMany($state, array_map('strval', $studyIds), 'ready');
        }
        $studyN = max(0, (int) ($payload['study_count'] ?? 0));
        $chance = (int) ($payload['success_chance'] ?? Economy::crossSuccessChance($state, $studyN));
        $roll = random_int(1, 100);
        $forced = $state['ui']['force_mission_outcome'] ?? null;
        $instant = ($_ENV['GENESIS_INSTANT'] ?? getenv('GENESIS_INSTANT') ?: '') === '1'
            || (defined('GENESIS_INSTANT') && GENESIS_INSTANT);
        if ($forced === 'success' || $instant) {
            $roll = 1;
        } elseif ($forced === 'injured') {
            $roll = 100;
        }

        $pool = GameQueries::analyzedCreatures($state);
        $idA = (string) ($payload['parent_a'] ?? '');
        $idB = (string) ($payload['parent_b'] ?? '');
        $parentA = $pool[$idA] ?? null;
        $parentB = $pool[$idB] ?? null;
        if ($parentA === null || $parentB === null) {
            $distinct = array_values(GameQueries::distinctAnalyzedSpecies($state));
            $parentA = $distinct[0] ?? null;
            $parentB = $distinct[1] ?? null;
        }
        if ($parentA === null || $parentB === null) {
            self::event($state, 'Croisement avorté', 'Parents introuvables.');
            return;
        }

        if ($roll > $chance) {
            // Échec : un parent meurt (enjeu réel, comme la mutation)
            $activeId = (string) ($state['creature']['id'] ?? '');
            $idA = (string) ($parentA['id'] ?? $idA);
            $idB = (string) ($parentB['id'] ?? $idB);
            // Préférer le focus actif s’il est parent, sinon le plus fragile (pureté basse), sinon A
            if ($activeId !== '' && ($activeId === $idA || $activeId === $idB)) {
                $victimId = $activeId;
                $victim = $activeId === $idA ? $parentA : $parentB;
            } else {
                $pA = (int) ($parentA['purity'] ?? 100);
                $pB = (int) ($parentB['purity'] ?? 100);
                if ($pB < $pA) {
                    $victimId = $idB;
                    $victim = $parentB;
                } else {
                    $victimId = $idA;
                    $victim = $parentA;
                }
            }
            $victimName = (string) ($victim['name'] ?? $victim['species'] ?? 'Un spécimen');
            $survivor = $victimId === $idA ? $parentB : $parentA;
            $survivorId = (string) ($survivor['id'] ?? '');
            $survivorName = (string) ($survivor['name'] ?? $survivor['species'] ?? '—');
            $biomeA = (string) ($parentA['biome'] ?? '—');
            $biomeB = (string) ($parentB['biome'] ?? '—');

            if ($victimId !== '' && isset($state['bestiary'][$victimId])) {
                unset($state['bestiary'][$victimId]);
            }
            // Nettoyer escorte / appui capture si c’était la victime
            if (($state['expedition']['escort_creature_id'] ?? null) === $victimId) {
                $state['expedition']['escort_creature_id'] = null;
            }
            $capEsc = $state['expedition']['capture_escorts'] ?? [];
            if (is_array($capEsc)) {
                $state['expedition']['capture_escorts'] = array_values(array_filter(
                    array_map('strval', $capEsc),
                    static fn (string $id): bool => $id !== $victimId
                ));
            }
            // Focus : survivant, ou blank
            if ($survivorId !== '' && isset($state['bestiary'][$survivorId])) {
                $state['creature'] = $state['bestiary'][$survivorId];
                $state['creature']['id'] = $survivorId;
            } else {
                $state['creature'] = CreatureFactory::blank(self::nextId($state));
            }

            $deathLines = [
                sprintf(
                    '%s n’a pas supporté le mélange %s × %s. Le sang a choisi un seul corps — et l’a brisé.',
                    $victimName,
                    $biomeA,
                    $biomeB
                ),
                sprintf(
                    'L’union a tenu quelques battements, puis %s s’est effondré. %s reste, seul, sans héritier.',
                    $victimName,
                    $survivorName
                ),
                sprintf(
                    'Deux peaux, une seule place. %s a perdu. Pas d’hybride — seulement un silence de labo.',
                    $victimName
                ),
            ];
            $deathLine = $deathLines[array_rand($deathLines)];

            $state['world']['last_report'] = sprintf(
                "RAPPORT DE CROISEMENT — ÉCHEC\nParents : %s × %s (%s × %s)\nChance : ~%d%% · drones d’analyse ×%d\nRésultat : l’union n’a pas tenu.\nMORT : %s\nSurvivant : %s\n\n%s\n\nOrganique consommé, pas d’hybride.\nConseil GENESIS : plus de lentilles d’analyse avant la prochaine union.",
                $parentA['species'] ?? '?',
                $parentB['species'] ?? '?',
                $biomeA,
                $biomeB,
                $chance,
                $studyN,
                $victimName,
                $survivorName,
                $deathLine
            );
            self::memory($state, sprintf('Croisement échoué — %s est mort. %s', $victimName, $deathLine));
            self::event(
                $state,
                'Union brisée',
                sprintf('%s est mort (~%d%%). %s', $victimName, $chance, $deathLine)
            );
            self::voice(
                $state,
                'Tension',
                sprintf(
                    'L’union a mangé %s. %s porte encore la preuve. Plus de lentilles la prochaine fois.',
                    $victimName,
                    $survivorName
                )
            );
            $state['ui']['flash'] = [
                'type' => 'loss',
                'title' => 'Union brisée',
                'message' => $deathLine,
            ];
            $state['screen'] = 'lab';

            return;
        }

        self::archiveActive($state);
        $hybridId = self::nextId($state);
        $state['creature'] = CreatureFactory::hybrid($hybridId, $parentA, $parentB);
        $state['bestiary'][$hybridId] = $state['creature'];
        $name = (string) $state['creature']['name'];
        $flavor = (string) ($state['creature']['synergy_flavor'] ?? '');
        $synTitle = (string) ($state['creature']['synergy_title'] ?? '');
        self::memory($state, sprintf('Croisement : %s × %s → %s.', $parentA['species'] ?? '?', $parentB['species'] ?? '?', $name));
        if ($synTitle !== '') {
            self::memory($state, 'Synergie : ' . $synTitle);
            $state['heritage']['synergies'][] = [
                'id' => $state['creature']['synergy_id'] ?? null,
                'title' => $synTitle,
                'hybrid' => $name,
                'parents' => $state['creature']['parents'] ?? [],
            ];
        }
        $stats = $state['creature']['stats'] ?? [];
        $state['world']['last_report'] = sprintf(
            "RAPPORT DE CROISEMENT — RÉUSSITE\nHybride : %s\nParents : %s × %s\nSynergie : %s\nChance était ~%d%% · analyse ×%d\nStats F%d V%d R%d I%d\nPureté : %d%%",
            $name,
            $parentA['species'] ?? '?',
            $parentB['species'] ?? '?',
            $synTitle !== '' ? $synTitle : 'aucune nommée',
            $chance,
            $studyN,
            (int) ($stats['force'] ?? 0),
            (int) ($stats['vitesse'] ?? 0),
            (int) ($stats['resistance'] ?? 0),
            (int) ($stats['intelligence'] ?? 0),
            (int) ($state['creature']['purity'] ?? 70)
        );
        self::event($state, $synTitle !== '' ? 'Hybride — ' . $synTitle : 'Hybride créé', $flavor !== '' ? $flavor : $name . ' est né.');
        $state['screen'] = 'lab';
    }

    private static function finishMutate(array &$state, array $payload): void
    {
        $studyIds = $payload['study_drone_ids'] ?? [];
        if (is_array($studyIds)) {
            DroneYard::releaseMany($state, array_map('strval', $studyIds), 'ready');
        }
        $choice = (string) ($payload['choice'] ?? 'ability');
        if (empty($state['creature']['analyzed']) || !empty($state['creature']['mutated'])) {
            return;
        }
        $studyN = max(0, (int) ($payload['study_count'] ?? 0));
        $chance = (int) ($payload['success_chance'] ?? Economy::mutateSuccessChance($state['creature'], $studyN));
        $roll = random_int(1, 100);
        $forced = $state['ui']['force_mission_outcome'] ?? null;
        $instant = ($_ENV['GENESIS_INSTANT'] ?? getenv('GENESIS_INSTANT') ?: '') === '1'
            || (defined('GENESIS_INSTANT') && GENESIS_INSTANT);
        if ($forced === 'success' || $instant) {
            $roll = 1;
        } elseif ($forced === 'injured') {
            $roll = 100;
        }

        if ($roll > $chance) {
            $lost = $state['creature'];
            $id = (string) ($lost['id'] ?? '');
            $lostName = (string) ($lost['name'] ?? 'Le spécimen');
            $lostSpecies = (string) ($lost['species'] ?? 'forme');
            $lostBiome = (string) ($lost['biome'] ?? 'inconnu');
            if ($id !== '' && isset($state['bestiary'][$id])) {
                unset($state['bestiary'][$id]);
            }
            $state['creature'] = CreatureFactory::blank(self::nextId($state));
            $failLines = [
                sprintf(
                    '%s (%s, %s) s’est tordu hors de toute lecture. Il ne reste qu’une tache sur la table froide.',
                    $lostName,
                    $lostSpecies,
                    $lostBiome
                ),
                sprintf(
                    'La mutation a mangé %s de l’intérieur. Sans lentilles, le vivant n’a pas de rambarde.',
                    $lostName
                ),
                sprintf(
                    '%s n’a pas tenu le seuil (~%d%%). La pureté a craqué avant le gène.',
                    $lostName,
                    $chance
                ),
            ];
            $failLine = $failLines[array_rand($failLines)];
            $state['world']['last_report'] = sprintf(
                "RAPPORT DE MUTATION — ÉCHEC\nSujet : %s (%s)\nBiome : %s\nChance estimée : ~%d%% · lentilles ×%d\nRésultat : la forme n’a pas tenu.\n\n%s\n\nLe spécimen est perdu.\nConseil GENESIS : alloue plus de lentilles d’analyse avant de tordre.",
                $lostName,
                $lostSpecies,
                $lostBiome,
                $chance,
                $studyN,
                $failLine
            );
            self::memory($state, sprintf('Mutation fatale : %s perdu. %s', $lostName, $failLine));
            self::event($state, 'Mutation fatale', $failLine);
            self::voice(
                $state,
                'Tension',
                'Sans stabilisation, le vivant casse. Les lentilles ne sont pas un luxe — c’est le filet.'
            );
            $state['ui']['flash'] = [
                'type' => 'loss',
                'title' => 'Forme effondrée',
                'message' => $failLine,
            ];
            $state['screen'] = 'lab';
            return;
        }

        if (!empty($state['creature']['is_hybrid'])) {
            $option = [
                'label' => 'Stabilisation hybride',
                'description' => 'Force +1 · pureté −10',
                'stats' => ['force' => 1],
                'ability' => null,
                'purity_cost' => 10,
                'name_suffix' => 'affiné',
                'rarity' => 'unique',
            ];
        } else {
            $options = GameQueries::mutationOptions($state['creature']);
            $option = $options[$choice] ?? $options['ability'] ?? null;
            if ($option === null) {
                return;
            }
        }
        foreach ($option['stats'] ?? [] as $stat => $delta) {
            $state['creature']['stats'][$stat] = (int) ($state['creature']['stats'][$stat] ?? 0) + (int) $delta;
        }
        if (!empty($option['ability']) && !in_array($option['ability'], $state['creature']['abilities'] ?? [], true)) {
            $state['creature']['abilities'][] = $option['ability'];
            $state['creature']['abilities'] = array_values(array_slice($state['creature']['abilities'], 0, 2));
        }
        $state['creature']['purity'] = max(40, (int) ($state['creature']['purity'] ?? 100) - (int) ($option['purity_cost'] ?? 10));
        $state['creature']['mutated'] = true;
        if (!empty($option['name_suffix'])) {
            $state['creature']['name'] = ($state['creature']['species'] ?? 'Créature') . ' ' . $option['name_suffix'];
        }
        if (!empty($option['rarity'])) {
            $state['creature']['rarity'] = $option['rarity'];
        }
        $state['bestiary'][(string) $state['creature']['id']] = $state['creature'];
        self::memory($state, sprintf('Mutation « %s » (pureté %d%%).', $option['label'] ?? $choice, (int) $state['creature']['purity']));
        self::event($state, 'Mutation réussie', (string) ($option['description'] ?? 'Appliquée'));
        $stats = $state['creature']['stats'] ?? [];
        $state['world']['last_report'] = sprintf(
            "RAPPORT DE MUTATION — RÉUSSITE\nSujet : %s\nVoie : %s\nChance était : ~%d%%\nEffet : %s\nStats F%d V%d R%d I%d · pureté %d%%",
            $state['creature']['name'] ?? '—',
            $option['label'] ?? $choice,
            $chance,
            $option['description'] ?? '—',
            (int) ($stats['force'] ?? 0),
            (int) ($stats['vitesse'] ?? 0),
            (int) ($stats['resistance'] ?? 0),
            (int) ($stats['intelligence'] ?? 0),
            (int) ($state['creature']['purity'] ?? 100)
        );
        $state['screen'] = 'lab';
    }

    private static function finishMission(array &$state, array $payload): void
    {
        $outcome = self::rollMission($state);
        $state['creature']['mission_done'] = true;
        $state['ui']['force_mission_outcome'] = null;
        $c = $state['creature'] ?? [];
        $chance = (int) ($payload['success_chance'] ?? Economy::missionSuccessChance($c));
        $profile = sprintf(
            '%s · pureté %d%% · F%d V%d R%d I%d%s%s',
            $c['species'] ?? ($c['name'] ?? '—'),
            (int) ($c['purity'] ?? 100),
            (int) ($c['stats']['force'] ?? 0),
            (int) ($c['stats']['vitesse'] ?? 0),
            (int) ($c['stats']['resistance'] ?? 0),
            (int) ($c['stats']['intelligence'] ?? 0),
            !empty($c['mutated']) ? ' · muté' : '',
            !empty($c['is_hybrid']) ? ' · hybride' : ''
        );

        if ($outcome === 'injured') {
            $state['creature']['status'] = 'blessée';
            $state['world']['stable'] = false;
            $healCost = Economy::recoverCost('blessée');
            $msg = sprintf(
                "RÉSULTAT : BLESSURE\n"
                . "Le spécimen a été touché sur le terrain. Il rentre vivant mais hors-service.\n"
                . "Conséquences immédiates :\n"
                . "  • Impossible de mission / d’escorte tant qu’il est blessé\n"
                . "  • Soin obligatoire : −%d organique (Laboratoire → Soigner la blessure)\n"
                . "Sans soin, la forme reste immobilisée.",
                $healCost
            );
            self::memory($state, 'Mission : blessure — soin requis.');
            self::event(
                $state,
                'Blessure au retour',
                sprintf('Blessé. Allez au Laboratoire et soignez (−%d organique) avant toute sortie.', $healCost)
            );
            self::voice($state, 'Tension', 'Il saigne. Laboratoire d’abord — pas une autre sortie.');
            $state['ui']['flash'] = [
                'type' => 'loss',
                'title' => 'Blessure',
                'message' => sprintf(
                    '%s rentre blessé. Soignez au labo (−%d organique) pour le réactiver (mission / escorte).',
                    $c['name'] ?? 'Le spécimen',
                    $healCost
                ),
            ];
        } elseif ($outcome === 'exhausted') {
            $state['creature']['status'] = 'épuisée';
            $gain = Economy::missionExhaustedSampleGain();
            $state['resources']['samples'] = (int) ($state['resources']['samples'] ?? 0) + $gain;
            $healCost = Economy::recoverCost('épuisée');
            $msg = sprintf(
                "RÉSULTAT : ÉPUISEMENT\n"
                . "La sortie a rapporté (+%d prélèvement) mais le spécimen est à bout.\n"
                . "Conséquences :\n"
                . "  • Pas d’escorte ni de mission tant qu’il est épuisé\n"
                . "  • Restauration : −%d organique (Laboratoire → Restaurer)\n"
                . "Ce n’est pas une blessure grave — juste du repos forcé.",
                $gain,
                $healCost
            );
            self::memory($state, 'Mission : épuisement.');
            self::event(
                $state,
                'Retour épuisé',
                sprintf('+%d prélèvement. Restaurez (−%d organique) pour repartir.', $gain, $healCost)
            );
            $state['ui']['flash'] = [
                'type' => 'discovery',
                'title' => 'Épuisement',
                'message' => sprintf(
                    'Sortie utile (+%d prélèvement) mais %s est épuisé. Restaurez au labo (−%d organique).',
                    $gain,
                    $c['name'] ?? 'le spécimen',
                    $healCost
                ),
            ];
        } else {
            $state['creature']['status'] = 'prête';
            $state['world']['stable'] = true;
            $gain = Economy::missionSuccessOrganicGain();
            $state['resources']['organic'] = (int) ($state['resources']['organic'] ?? 0) + $gain;
            if (!empty($c['is_hybrid'])) {
                $state['resources']['samples'] = (int) ($state['resources']['samples'] ?? 0) + 1;
                $msg = sprintf(
                    "RÉSULTAT : SUCCÈS\n"
                    . "La forme a validé le terrain sans dommage.\n"
                    . "Gains : +%d organique, +1 prélèvement. Statut : prête (mission / escorte OK).",
                    $gain
                );
            } else {
                $msg = sprintf(
                    "RÉSULTAT : SUCCÈS\n"
                    . "Sortie propre. +%d organique. Le spécimen reste prêt pour mission ou escorte.",
                    $gain
                );
            }
            self::memory($state, 'Mission : succès.');
            self::event($state, 'Mission réussie', sprintf('+%d organique. Spécimen encore prêt.', $gain));
            $state['ui']['flash'] = [
                'type' => 'contact',
                'title' => 'Mission réussie',
                'message' => sprintf('%s a tenu le terrain. +%d organique.', $c['name'] ?? 'Le spécimen', $gain),
            ];
        }

        $state['world']['last_report'] = sprintf(
            "RAPPORT DE MISSION\nSujet : %s\nProfil : %s\nChance estimée de succès : ~%d%% (échec ≈ blessure ou épuisement)\n\n%s",
            $c['name'] ?? '—',
            $profile,
            $chance,
            $msg
        );
        $state['bestiary'][(string) $state['creature']['id']] = $state['creature'];
        // Blessure / épuisement → bestiaire (soins), succès → terrain
        $state['screen'] = $outcome === 'injured' || $outcome === 'exhausted' ? 'bestiary' : 'field';
    }

    private static function finishFarm(array &$state): void
    {
        $yield = Economy::farmBiomassYield($state);
        $logGain = Economy::farmLogisticsYield();
        if (!isset($state['resources']['biomass'])) {
            $state['resources']['biomass'] = 0;
        }
        $before = (int) $state['resources']['biomass'];
        $state['resources']['biomass'] = $before + $yield;
        $after = (int) $state['resources']['biomass'];
        $logBefore = (int) ($state['resources']['logistics'] ?? 0);
        $state['resources']['logistics'] = $logBefore + $logGain;
        self::event(
            $state,
            'Récolte terminée',
            sprintf('Biomasse %d → %d (+%d) · logistique +%d.', $before, $after, $yield, $logGain)
        );
        self::memory($state, sprintf('Récolte : biomasse +%d, logistique +%d.', $yield, $logGain));
        $state['world']['last_report'] = sprintf(
            "RAPPORT DE RÉCOLTE\nBiomasse : %d → %d (+%d)\nLogistique : %d → %d (+%d)",
            $before,
            $after,
            $yield,
            $logBefore,
            $logBefore + $logGain,
            $logGain
        );
        $state['screen'] = 'institute';
    }

    private static function finishRestock(array &$state): void
    {
        $gain = Economy::restockLogisticsGain();
        $before = (int) ($state['resources']['logistics'] ?? 0);
        $state['resources']['logistics'] = $before + $gain;
        self::event(
            $state,
            'Réappro terminé',
            sprintf('Logistique %d → %d (+%d).', $before, $before + $gain, $gain)
        );
        self::memory($state, sprintf('Réappro : +%d logistique.', $gain));
        $state['world']['last_report'] = sprintf(
            "RAPPORT DE RÉAPPROVISIONNEMENT\nLogistique : %d → %d (+%d)\nSource : conversion biomasse → chaîne logistique.",
            $before,
            $before + $gain,
            $gain
        );
        $state['screen'] = 'institute';
    }

    private static function finishCraft(array &$state, array $payload): void
    {
        $type = (string) ($payload['drone_type'] ?? DroneYard::TYPE_RECON);
        $drone = DroneYard::add($state, $type);
        self::event($state, 'Drone prêt', $drone['label'] . ' opérationnel.');
        self::memory($state, 'Assemblage : ' . $drone['label']);
        $state['screen'] = 'institute';
    }

    private static function rollMission(array $state): string
    {
        $forced = $state['ui']['force_mission_outcome'] ?? null;
        if (in_array($forced, ['success', 'injured', 'exhausted'], true)) {
            return $forced;
        }

        $roll = random_int(1, 100);
        $success = Economy::missionSuccessChance($state['creature'] ?? []);
        if ($roll <= $success) {
            return 'success';
        }

        return $roll <= $success + 18 ? 'exhausted' : 'injured';
    }

    private static function dismissTips(array &$state): void
    {
        $state['ui']['tips_dismissed'] = true;
        self::event($state, 'Guide replié', 'Vous pouvez rouvrir le Codex à tout moment.');
        $state['screen'] = (string) ($state['screen'] ?? 'institute');
    }

    private static function recover(array &$state): void
    {
        if (!GameQueries::needsRecovery($state['creature'] ?? [])) {
            self::event($state, 'Rien à soigner', 'Le spécimen actif est déjà prêt.');
            return;
        }
        $cost = GameQueries::recoverCost($state['creature']);
        $have = (int) ($state['resources']['organic'] ?? 0);
        if ($have < $cost) {
            self::event(
                $state,
                'Soin impossible',
                sprintf('Il faut %d organique (vous en avez %d). Gagnez-en en mission réussie ou via le terrain.', $cost, $have)
            );
            $state['screen'] = 'bestiary';
            return;
        }
        $before = (string) $state['creature']['status'];
        $state['resources']['organic'] -= $cost;
        $state['creature']['status'] = 'prête';
        if ($before === 'blessée') {
            $state['world']['stable'] = true;
        }
        $id = (string) ($state['creature']['id'] ?? '');
        if ($id !== '') {
            $state['bestiary'][$id] = $state['creature'];
        }
        self::memory($state, sprintf('Soin : %s → prête (−%d organique).', $before, $cost));
        if ($before === 'blessée') {
            FreePlayGoals::markHealedWounded($state);
        }
        self::event(
            $state,
            $before === 'blessée' ? 'Blessure soignée' : 'Repos terminé',
            $before === 'blessée'
                ? sprintf('−%d organique. Le spécimen peut repartir (mission / escorte).', $cost)
                : sprintf('−%d organique. Fatigue levée — prêt pour escorte ou mission.', $cost)
        );
        self::voice($state, 'Continuité', 'Il respire mieux. Tu peux le renvoyer dehors.');
        $state['ui']['flash'] = [
            'type' => 'contact',
            'title' => $before === 'blessée' ? 'Soigné' : 'Reposé',
            'message' => sprintf(
                '%s est de nouveau prête (−%d organique).',
                $state['creature']['name'] ?? 'Le spécimen',
                $cost
            ),
        ];
        $state['screen'] = 'bestiary';
    }

    private static function exhibit(array &$state): void
    {
        if (!self::assertFeature($state, 'museum', 'Pas encore.')) {
            return;
        }
        $c = $state['creature'] ?? [];
        if (empty($c['analyzed'])) {
            self::event($state, 'Exposition impossible', 'Rien de digne à conserver.');
            return;
        }
        if (GameQueries::isExhibited($state, $c)) {
            self::event($state, 'Déjà au musée', 'Cette pièce est déjà exposée.');
            $state['screen'] = 'museum';
            return;
        }

        $state['museum']['exhibits'][] = [
            'creature_id' => $c['id'] ?? null,
            'label' => CreatureFactory::label($c),
            'species' => $c['species'] ?? '—',
            'rarity' => $c['rarity'] ?? '—',
            'status' => $c['status'] ?? 'prête',
            'purity' => (int) ($c['purity'] ?? 100),
            'note' => !empty($c['is_hybrid'])
                ? 'Hybride unique — création de l’institut.'
                : 'Découverte majeure du berceau.',
            'parents' => $c['parents'] ?? null,
        ];
        $state['resources']['logistics'] = (int) ($state['resources']['logistics'] ?? 0) + Economy::exhibitLogisticsGain();
        self::memory($state, 'Musée : ' . ($c['name'] ?? 'spécimen') . ' entre dans la collection.');
        self::event(
            $state,
            'Musée',
            sprintf('%s est conservé. +1 logistique. Le patrimoine s’écrit.', $c['name'] ?? 'La créature')
        );
        self::voice(
            $state,
            'Continuité',
            'Collection enrichie. Ce n’est pas une fin : c’est une mémoire pour la suite de la science.'
        );
        $state['screen'] = 'museum';
    }

    private static function newExploration(array &$state): void
    {
        // N’efface pas les signaux en attente — ils restent au bestiaire-pending
        self::archiveActive($state);
        $pendingN = count(GameQueries::pendingSignals($state));
        $state['creature'] = CreatureFactory::blank(self::nextId($state));
        $state['world']['selected_zone'] = null;
        $state['world']['signal'] = $pendingN > 0 ? sprintf('%d signal(s) en attente', $pendingN) : null;
        $state['world']['risk'] = 'inconnu';
        $gain = Economy::newExplorationLogisticsGain();
        $state['resources']['logistics'] = (int) ($state['resources']['logistics'] ?? 0) + $gain;
        $state['ui']['loop'] = (int) ($state['ui']['loop'] ?? 1) + 1;

        self::event(
            $state,
            'Exploration',
            sprintf(
                'Focus libéré. +%d logistique.%s',
                $gain,
                $pendingN > 0
                    ? sprintf(' %d signal(s) restent en attente — reprenez-les pour capturer.', $pendingN)
                    : ' Choisissez une zone.'
            )
        );
        self::voice($state, 'Observation', 'La carte attend. La Baie n’interrompt pas la science locale.');
        $state['screen'] = 'world';
    }

    private static function focusCreature(array &$state, string $id): void
    {
        $pool = GameQueries::analyzedCreatures($state);
        $fromBestiary = $state['bestiary'][$id] ?? null;
        $pending = GameQueries::pendingSignals($state);
        if (!isset($pool[$id]) && !is_array($fromBestiary) && !isset($pending[$id])) {
            self::event($state, 'Introuvable', 'Créature absente du bestiaire ou des signaux.');
            return;
        }
        self::archiveActive($state);
        if (isset($pending[$id])) {
            $state['creature'] = $pending[$id];
            $state['creature']['id'] = $id;
            $state['world']['signal'] = $state['creature']['species'] ?? 'Signal vivant';
            $zoneId = (string) ($state['creature']['zone_id'] ?? '');
            if ($zoneId !== '' && isset($state['world']['zones'][$zoneId])) {
                $state['world']['selected_zone'] = $zoneId;
            }
            $ch = Economy::captureSuccessChance(
                $state,
                min(5, max(1, (int) ($state['expedition']['capture_count'] ?? 1)), DroneYard::countReady($state, DroneYard::TYPE_CAPTURE) ?: 1)
            );
            self::event($state, 'Signal repris', 'Signal en attente actif — vous pouvez tenter la capture (~' . $ch . '%).');
            self::voice($state, 'Tension', 'Il est encore là. Alloue assez de drones de prise, puis tente.');
            $state['screen'] = 'lab';

            return;
        }
        $state['creature'] = $pool[$id] ?? $fromBestiary;
        $state['creature']['id'] = $id;
        self::event($state, 'Focus', ($state['creature']['name'] ?? 'Spécimen') . ' est actif.');
        self::voice($state, 'Observation', 'Attention reportée sur un autre visage du bestiaire.');
        // Spécimen capturé : bestiaire (soin / étude) ; signal non pris reste au labo
        $state['screen'] = !empty($state['creature']['analyzed']) ? 'bestiary' : 'lab';
    }

    private static function focusSignal(array &$state, string $id): void
    {
        if ($id === '' || !self::focusPendingSignal($state, $id)) {
            // Sans id : premier signal en attente
            if ($id === '' && self::focusPendingSignal($state, null)) {
                $ch = Economy::captureSuccessChance(
                    $state,
                    min(5, max(1, (int) ($state['expedition']['capture_count'] ?? 1)), max(1, DroneYard::countReady($state, DroneYard::TYPE_CAPTURE)))
                );
                self::event($state, 'Signal repris', sprintf('Prêt pour une tentative de capture (~%d%%).', $ch));
                $state['screen'] = 'lab';

                return;
            }
            self::event($state, 'Aucun signal', 'Pas de signe de vie en attente. Explorez pour en trouver.');
            $state['screen'] = 'world';

            return;
        }
        $ch = Economy::captureSuccessChance(
            $state,
            min(5, max(1, (int) ($state['expedition']['capture_count'] ?? 1)), max(1, DroneYard::countReady($state, DroneYard::TYPE_CAPTURE)))
        );
        self::event($state, 'Signal repris', sprintf('Signal actif — capture ~%d%% avec l’effectif actuel.', $ch));
        self::voice($state, 'Tension', 'Il attend. Monte l’effectif si la chance est trop basse, puis tente.');
        $state['screen'] = 'lab';
    }

    // ------------------------------------------------------------------
    // View model
    // ------------------------------------------------------------------

    public static function buildView(array $state, array $query = []): array
    {
        $screen = (string) ($state['screen'] ?? 'institute');
        $primary = IntentResolver::primary($state);
        $secondary = IntentResolver::secondary($state);
        $progress = GameQueries::progress($state);
        $creature = $state['creature'] ?? [];
        $zones = array_values($state['world']['zones'] ?? []);
        $analyzed = GameQueries::analyzedCreatures($state);
        $analyzedList = array_values($analyzed);

        $titles = [
            'institute' => ['Institut', 'Le cœur battant d’Aster-0'],
            'world' => ['Horizon', 'Le berceau sous les sondes'],
            'lab' => ['Laboratoire', 'Où le vivant est tordu, non détruit'],
            'bestiary' => ['Bestiaire', 'Ceux que tu as touchés'],
            'field' => ['Terrain', 'Seuls, sans filet'],
            'museum' => ['Musée', 'Mémoire, pas trophée'],
            'departure' => ['Projection', 'Au-delà du berceau'],
            'codex' => ['Codex', 'Les noms que le croisement révèle'],
        ];
        [$title, $subtitle] = $titles[$screen] ?? $titles['institute'];

        // Zones : coût + filtre débloquées seulement
        $zonesAnnotated = [];
        foreach ($state['world']['zones'] ?? [] as $z) {
            if (!is_array($z)) {
                continue;
            }
            if (empty($z['unlocked']) || !empty($z['locked'])) {
                continue;
            }
            if (($z['layer'] ?? 'natal') === 'orbital' && empty($state['export']['baie_unlocked'])) {
                continue;
            }
            $droneCount = max(1, min(5, (int) ($state['expedition']['drone_count'] ?? 1)));
            $payloadPrev = [
                'drone_type' => $state['expedition']['drone_type'] ?? DroneYard::TYPE_RECON,
                'drone_count' => $droneCount,
                'escort_id' => $state['expedition']['escort_creature_id'] ?? null,
            ];
            $z['recon_cost'] = Economy::reconCost($z, $droneCount);
            $z['species_pool'] = Catalog::zoneSpeciesKeys($z);
            $z['species_count'] = count($z['species_pool']);
            $z['scan'] = (int) ($z['scan'] ?? (!empty($z['explored']) ? 100 : 0));
            $z['success_chance'] = self::explorationSuccessChance($state, $z, $payloadPrev);
            $z['specimen_chance'] = self::specimenFindChance($state, $z, $payloadPrev, $z['scan'], $droneCount);
            $z['affinity'] = self::escortAffinityNote($state, $z);
            $z['affinity_score'] = self::escortAffinityScore($state, $z);
            $z['hover'] = self::zoneHoverText($z);
            $z['newly_unlocked'] = !empty($z['newly_unlocked']);
            $zonesAnnotated[] = $z;
        }
        $zones = $zonesAnnotated;

        $features = Tutorial::features($state);
        $assistant = Tutorial::assistant($state);
        // Libre jeu : objectifs / seuil si silence ; sinon voix d’événement (ui.genesis)
        $filler = 'Je ne suis pas un manuel';
        if (trim((string) ($assistant['message'] ?? '')) === '') {
            $gMsg = trim((string) ($state['ui']['genesis']['message'] ?? ''));
            if ($gMsg !== '' && !str_contains($gMsg, $filler)) {
                $assistant['message'] = $gMsg;
                $assistant['marker'] = (string) ($state['ui']['genesis']['marker'] ?? 'Observation');
            }
        }
        $freeplay = FreePlayGoals::view($state);
        $memoryJournal = MemoryJournal::view($state, 14);

        // Estimation succès recon avec équipe courante
        $selectedZoneId = (string) ($state['world']['selected_zone'] ?? 'plaine');
        $selZone = $state['world']['zones'][$selectedZoneId] ?? [];
        $teamChance = self::explorationSuccessChance($state, $selZone, [
            'drone_type' => $state['expedition']['drone_type'] ?? DroneYard::TYPE_RECON,
            'drone_count' => max(1, min(5, (int) ($state['expedition']['drone_count'] ?? 1))),
            'escort_id' => $state['expedition']['escort_creature_id'] ?? null,
        ]);

        $discoveredIds = GameQueries::discoveredSynergyIds($state);
        $codex = \Genesis\Game\Data\Synergies::codex($discoveredIds);
        $codexFound = count(array_filter($codex, static fn (array $e): bool => !empty($e['discovered'])));

        // Preview croisement (GET parent_a / parent_b)
        $preview = null;
        $previewA = (string) ($query['parent_a'] ?? '');
        $previewB = (string) ($query['parent_b'] ?? '');
        if ($previewA !== '' && $previewB !== '' && $previewA !== $previewB
            && isset($analyzed[$previewA], $analyzed[$previewB])) {
            $preview = CreatureFactory::hybridPreview($analyzed[$previewA], $analyzed[$previewB]);
        } elseif (count($analyzedList) >= 2) {
            // Aperçu par défaut : deux premières espèces distinctes
            $distinct = array_values(GameQueries::distinctAnalyzedSpecies($state));
            if (count($distinct) >= 2) {
                $preview = CreatureFactory::hybridPreview($distinct[0], $distinct[1]);
                $previewA = (string) ($distinct[0]['id'] ?? '');
                $previewB = (string) ($distinct[1]['id'] ?? '');
            }
        }

        $mve = MveEvaluator::evaluate($state);

        return [
            'title' => $title,
            'subtitle' => $subtitle,
            'objective' => '',
            'primary' => $primary,
            'secondary' => $secondary,
            'progress' => $progress,
            'creature_label' => CreatureFactory::label($creature),
            'zones' => $zones,
            'analyzed' => $analyzedList,
            'pending_signals' => array_values(GameQueries::pendingSignals($state)),
            'resources_label' => GameQueries::resourceLabel($state['resources'] ?? []),
            'can_cross' => GameQueries::canCross($state),
            'has_cross_parents' => GameQueries::hasCrossParents($state),
            'resource_tips' => Economy::resourceTips($state['resources'] ?? []),
            'refine' => [
                'samples_cost' => Economy::refineSamplesCost(),
                'samples_gain' => Economy::refineOrganicFromSamples(),
                'biomass_cost' => Economy::refineBiomassCost(),
                'biomass_gain' => Economy::refineOrganicFromBiomass(),
                // Organique = missions uniquement (plus de synthèse)
                'can_samples' => false,
                'can_biomass' => false,
            ],
            'mutation_options' => GameQueries::mutationOptions($creature),
            'cross_preview' => $preview,
            'preview_parent_a' => $previewA,
            'preview_parent_b' => $previewB,
            'species_count' => count(Catalog::species()),
            'zone_count' => count(Catalog::zones()),
            'mve' => $mve,
            'saves' => SaveStore::list(),
            'db_driver' => (static function (): string {
                try {
                    return Database::driver();
                } catch (\Throwable $e) {
                    return 'none';
                }
            })(),
            'db_game_id' => (int) ($state['ui']['db_game_id'] ?? $_SESSION['genesis_game_id'] ?? 0),
            'export' => $state['export'] ?? [],
            'synergies_log' => $state['heritage']['synergies'] ?? [],
            'codex' => $codex,
            'codex_found' => $codexFound,
            'codex_total' => \Genesis\Game\Data\Synergies::totalNamed(),
            'tips_dismissed' => !empty($state['ui']['tips_dismissed']),
            'playtest_tips' => self::playtestTips($state),
            'economy' => [
                'cross' => Economy::crossCost(),
                'mutate' => Economy::mutateCost(),
                'recon' => [
                    'modéré' => 1,
                    'élevé' => 2,
                    'extrême' => 3,
                ],
            ],
            'tasks' => TaskRunner::running($state),
            'tasks_server_now' => TaskRunner::now(),
            'drone_summary' => DroneYard::summary($state),
            'drone_recipes' => DroneYard::recipesForState($state),
            'last_report' => $state['world']['last_report'] ?? null,
            'baie_unlocked' => !empty($state['export']['baie_unlocked']) || !empty($state['export']['departed']),
            'mission_risk' => !empty($creature['analyzed'])
                ? IntentResolver::missionRiskHint($creature)
                : null,
            'features' => $features,
            'assistant' => $assistant,
            'freeplay' => $freeplay,
            'memory_journal' => $memoryJournal,
            'tutorial' => $state['tutorial'] ?? Tutorial::defaultState(),
            'expedition' => $state['expedition'] ?? [],
            'team_chance' => $teamChance,
            'biomass' => (int) ($state['resources']['biomass'] ?? 0),
            'farm_yield_preview' => Economy::farmBiomassYield($state),
            'phase2' => Phase2::orbitalProgress($state),
            'deep_space_signal' => !empty($state['export']['deep_space_signal']),
            'flash' => $state['ui']['flash'] ?? null,
            'mutate_chance' => !empty($creature['analyzed']) && empty($creature['mutated'])
                ? Economy::mutateSuccessChance(
                    $creature,
                    min(
                        max(0, (int) ($state['expedition']['study_count'] ?? 0)),
                        DroneYard::countReady($state, DroneYard::TYPE_STUDY)
                    )
                )
                : null,
            'cross_chance' => GameQueries::canCross($state)
                ? Economy::crossSuccessChance(
                    $state,
                    min(
                        max(0, (int) ($state['expedition']['study_count'] ?? 0)),
                        DroneYard::countReady($state, DroneYard::TYPE_STUDY)
                    )
                )
                : null,
            'capture_chance' => self::previewCaptureChance($state),
            'recon_chance_table' => self::reconChanceTable($state, $selZone),
            'capture_chance_table' => self::captureChanceTable($state),
            'study_chance_table' => self::studyChanceTable($state, $creature),
            'deploy' => self::deploySnapshot($state),
            'hud' => [
                'recon_ready' => DroneYard::countReady($state, DroneYard::TYPE_RECON),
                'recon_total' => DroneYard::countTotal($state, DroneYard::TYPE_RECON),
                'recon_busy' => DroneYard::countBusy($state, DroneYard::TYPE_RECON),
                'capture_ready' => DroneYard::countReady($state, DroneYard::TYPE_CAPTURE),
                'capture_total' => DroneYard::countTotal($state, DroneYard::TYPE_CAPTURE),
                'capture_busy' => DroneYard::countBusy($state, DroneYard::TYPE_CAPTURE),
                'study_ready' => DroneYard::countReady($state, DroneYard::TYPE_STUDY),
                'study_total' => DroneYard::countTotal($state, DroneYard::TYPE_STUDY),
                'study_busy' => DroneYard::countBusy($state, DroneYard::TYPE_STUDY),
                'orbital_ready' => DroneYard::countReady($state, DroneYard::TYPE_ORBITAL),
                'orbital_total' => DroneYard::countTotal($state, DroneYard::TYPE_ORBITAL),
                'species' => count($analyzedList),
            ],
        ];
    }

    private static function previewCaptureChance(array $state): ?int
    {
        if (empty($state['creature']['discovered']) || !empty($state['creature']['analyzed'])) {
            return null;
        }
        $ready = DroneYard::countReady($state, DroneYard::TYPE_CAPTURE);
        if ($ready < 1) {
            return 0;
        }
        $want = max(1, min(5, (int) ($state['expedition']['capture_count'] ?? 1)));
        $n = min($want, $ready);

        return Economy::captureSuccessChance($state, $n);
    }

    /** @return list<array{n:int,chance:int,cost:int,available:bool}> */
    private static function reconChanceTable(array $state, array $zone): array
    {
        $droneType = (string) ($state['expedition']['drone_type'] ?? DroneYard::TYPE_RECON);
        if ($droneType === DroneYard::TYPE_CAPTURE) {
            $droneType = DroneYard::TYPE_RECON;
        }
        $ready = DroneYard::countReady($state, $droneType);
        $escort = $state['expedition']['escort_creature_id'] ?? null;
        $rows = [];
        for ($n = 1; $n <= 5; $n++) {
            $ch = self::explorationSuccessChance($state, $zone, [
                'drone_type' => $droneType,
                'drone_count' => $n,
                'escort_id' => $escort,
            ]);
            $ct = self::specimenFindChance($state, $zone, [
                'drone_type' => $droneType,
                'drone_count' => $n,
                'escort_id' => $escort,
            ], (int) ($zone['scan'] ?? 0), $n);
            $cost = Economy::reconCost($zone !== [] ? $zone : ['risk' => 'modéré'], $n);
            $rows[] = [
                'n' => $n,
                'chance' => $ch,
                'contact' => $ct,
                'cost' => $cost,
                'available' => $ready >= $n,
                'tooltip' => Economy::reconSlotTooltip($n, $ch, $ct, $cost),
            ];
        }

        return $rows;
    }

    /** @return list<array{n:int,chance:int,available:bool,tooltip:string}> */
    private static function captureChanceTable(array $state): array
    {
        $ready = DroneYard::countReady($state, DroneYard::TYPE_CAPTURE);
        $escortN = count(Economy::captureEscortIds($state));
        $rows = [];
        for ($n = 1; $n <= 5; $n++) {
            $ch = Economy::captureSuccessChance($state, $n);
            $rows[] = [
                'n' => $n,
                'chance' => $ch,
                'available' => $ready >= $n,
                'tooltip' => Economy::captureSlotTooltip($n, $ch, $escortN),
            ];
        }

        return $rows;
    }

    /** @return list<array{n:int,mutate:int,cross:int,available:bool,tooltip:string}> */
    private static function studyChanceTable(array $state, array $creature): array
    {
        $ready = DroneYard::countReady($state, DroneYard::TYPE_STUDY);
        $rows = [];
        for ($n = 0; $n <= 5; $n++) {
            $m = !empty($creature['analyzed']) && empty($creature['mutated'])
                ? Economy::mutateSuccessChance($creature, $n)
                : Economy::mutateSuccessChance(['analyzed' => true, 'purity' => 100, 'stats' => ['intelligence' => 5]], $n);
            $c = Economy::crossSuccessChance($state, $n);
            $rows[] = [
                'n' => $n,
                'mutate' => $m,
                'cross' => $c,
                'available' => $ready >= $n,
                'tooltip' => Economy::studySlotTooltip($n, $m, $c),
            ];
        }

        return $rows;
    }

    /** @return array<string,mixed> */
    private static function deploySnapshot(array $state): array
    {
        $exp = $state['expedition'] ?? [];
        $droneType = (string) ($exp['drone_type'] ?? DroneYard::TYPE_RECON);
        $readyRecon = DroneYard::countReady($state, $droneType === DroneYard::TYPE_CAPTURE ? DroneYard::TYPE_RECON : $droneType);
        $readyCap = DroneYard::countReady($state, DroneYard::TYPE_CAPTURE);
        $wantRecon = max(1, min(5, (int) ($exp['drone_count'] ?? 1)));
        $wantCap = max(1, min(5, (int) ($exp['capture_count'] ?? 1)));

        $readyStudy = DroneYard::countReady($state, DroneYard::TYPE_STUDY);
        $wantStudy = max(0, min(5, (int) ($exp['study_count'] ?? 0)));

        return [
            'drone_type' => $droneType,
            'drone_type_label' => DroneYard::label($droneType === DroneYard::TYPE_CAPTURE ? DroneYard::TYPE_RECON : $droneType),
            'recon_want' => $wantRecon,
            'recon_ready' => $readyRecon,
            'recon_will_use' => min($wantRecon, max(0, $readyRecon)),
            'capture_want' => $wantCap,
            'capture_ready' => $readyCap,
            'capture_will_use' => min($wantCap, max(0, $readyCap)),
            'study_want' => $wantStudy,
            'study_ready' => $readyStudy,
            'study_will_use' => min($wantStudy, max(0, $readyStudy)),
            'escort_id' => $exp['escort_creature_id'] ?? null,
        ];
    }

    private static function escortAffinityNote(array $state, array $zone): string
    {
        $escortId = $state['expedition']['escort_creature_id'] ?? null;
        if (!$escortId) {
            return 'Aucune escorte — bonus d’affinité null';
        }
        $pool = GameQueries::analyzedCreatures($state);
        $escort = $pool[(string) $escortId] ?? null;
        if (!$escort) {
            return 'Escorte introuvable';
        }
        $eb = mb_strtolower((string) ($escort['biome'] ?? ''));
        $zb = mb_strtolower((string) ($zone['biome'] ?? ''));
        $ebMain = explode('/', $eb)[0] ?? $eb;
        $score = self::biomeAffinityScore($ebMain, $zb);
        $name = (string) ($escort['name'] ?? $escort['species'] ?? 'escorte');
        $status = (string) ($escort['status'] ?? 'prête');
        if ($status !== 'prête') {
            return sprintf('%s (%s) : handicap — soignez d’abord', $name, $status);
        }
        if ($score >= 10) {
            return sprintf('%s : native / alliée ici (affinité +%d)', $name, $score);
        }
        if ($score >= 4) {
            return sprintf('%s : bien adaptée (+%d)', $name, $score);
        }
        if ($score <= -8) {
            return sprintf('%s : hostile ici (affinité %d) — feu mal à l’aise en froid/abysse, etc.', $name, $score);
        }
        if ($score < 0) {
            return sprintf('%s : mal adaptée (%d)', $name, $score);
        }

        return sprintf('%s : contribution neutre', $name);
    }

    private static function escortAffinityScore(array $state, array $zone): int
    {
        $escortId = $state['expedition']['escort_creature_id'] ?? null;
        if (!$escortId) {
            return 0;
        }
        $pool = GameQueries::analyzedCreatures($state);
        $escort = $pool[(string) $escortId] ?? null;
        if (!$escort) {
            return 0;
        }
        $eb = mb_strtolower((string) ($escort['biome'] ?? ''));
        $zb = mb_strtolower((string) ($zone['biome'] ?? ''));
        $ebMain = explode('/', $eb)[0] ?? $eb;

        return self::biomeAffinityScore($ebMain, $zb);
    }

    public static function biomeAffinityScore(string $creatureBiome, string $zoneBiome): int
    {
        $c = str_replace('minéral', 'mineral', $creatureBiome);
        $z = str_replace('minéral', 'mineral', $zoneBiome);
        if ($c === '' || $z === '') {
            return 0;
        }
        if ($c === $z) {
            return 12;
        }
        $allies = [
            'feu|chaleur' => 8,
            'chaleur|feu' => 8,
            'froid|abysse' => 6,
            'abysse|froid' => 6,
            'spore|mineral' => 5,
            'mineral|spore' => 5,
            'vent|froid' => 5,
            'froid|vent' => 5,
            'vent|chaleur' => 4,
            'chaleur|vent' => 4,
        ];
        $key = $c . '|' . $z;
        if (isset($allies[$key])) {
            return $allies[$key];
        }
        $foes = [
            'feu|froid' => -12,
            'froid|feu' => -12,
            'feu|abysse' => -10,
            'abysse|feu' => -10,
            'chaleur|froid' => -10,
            'froid|chaleur' => -10,
            'chaleur|abysse' => -8,
            'abysse|chaleur' => -8,
            'spore|feu' => -6,
            'feu|spore' => -6,
        ];

        return $foes[$key] ?? 0;
    }

    private static function zoneHoverText(array $zone): string
    {
        $scan = (int) ($zone['scan'] ?? 0);
        $succ = (int) ($zone['success_chance'] ?? 0);
        $contact = (int) ($zone['specimen_chance'] ?? 0);
        $cost = (int) ($zone['recon_cost'] ?? 1);
        $aff = (string) ($zone['affinity'] ?? 'Aucune escorte');
        $biome = (string) ($zone['biome'] ?? '—');
        $risk = (string) ($zone['risk'] ?? 'modéré');
        $lines = [
            sprintf('Réussite exploration ~%d%%', $succ),
            sprintf('Chance de contact ~%d%%', $contact),
            sprintf('Coût %d logistique', $cost),
            sprintf('Biome %s · risque %s', $biome, $risk),
            'Escorte : ' . $aff,
            'Un spécimen hors de son biome naturel est moins efficace (feu mal à l’aise en eau/froid, etc.).',
        ];
        if ($scan >= 100) {
            $lines[] = 'Scan 100% — zone lue ; d’autres formes restent possibles.';
        } elseif ($scan > 0) {
            $lines[] = sprintf('Scan %d%% — renvoyez des sondes pour cartographier.', $scan);
        } else {
            $lines[] = 'Scan 0% — terre encore opaque.';
        }
        if (!empty($zone['newly_unlocked'])) {
            $lines[] = '✦ NOUVELLE TERRE — venez d’ouvrir ce passage.';
        }

        return implode("\n", array_filter($lines));
    }

    /**
     * @return list<string>
     */
    private static function playtestTips(array $state): array
    {
        // Tips UI désactivés : l’assistant GENESIS porte le tutoriel
        return [];
    }
}
