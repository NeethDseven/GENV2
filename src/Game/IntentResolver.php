<?php

declare(strict_types=1);

namespace Genesis\Game;

/**
 * CTA piloté par le tutoriel + l’écran courant (parcours séparé).
 */
final class IntentResolver
{
    public static function primary(array $state): array
    {
        $screen = (string) ($state['screen'] ?? 'institute');
        $running = TaskRunner::running($state);

        if ($running !== []) {
            $t = $running[0];

            return [
                'type' => 'link',
                'href' => '?screen=' . rawurlencode($screen),
                'label' => sprintf('%s…', $t['label'] ?? 'En cours'),
                'screen' => $screen,
                'hint' => '',
            ];
        }

        $step = (string) ($state['tutorial']['step'] ?? '');
        if (!empty($state['tutorial']['active']) && $step === Tutorial::STEP_WELCOME) {
            return self::post(
                'continue_tutorial',
                'Compris — ouvrir la carte',
                'world',
                ''
            );
        }

        return match ($screen) {
            'world' => self::primaryWorld($state),
            'lab' => self::primaryLab($state),
            'bestiary' => self::primaryBestiary($state),
            'field' => self::primaryField($state),
            'museum' => self::primaryMuseum($state),
            'codex' => self::link('?screen=lab', 'Retour labo', 'lab', ''),
            default => self::primaryInstitute($state),
        };
    }

    /** Institut : résumé, logistique, craft — pas de capture. */
    private static function primaryInstitute(array $state): array
    {
        $features = Tutorial::features($state);
        $pending = GameQueries::pendingSignals($state);
        $organic = (int) ($state['resources']['organic'] ?? 0);
        $biomass = (int) ($state['resources']['biomass'] ?? 0);
        $logistics = (int) ($state['resources']['logistics'] ?? 0);
        $creature = $state['creature'] ?? [];

        if (GameQueries::needsRecovery($creature) && !empty($creature['analyzed'])) {
            return self::link(
                '?screen=lab',
                'Spécimen à soigner — Laboratoire',
                'lab',
                'Soin et capture se font au labo.'
            );
        }

        if ($pending !== [] && !empty($features['analyze'])) {
            return self::link(
                '?screen=lab',
                sprintf('Signal en attente (%d) — Laboratoire', count($pending)),
                'lab',
                'Préparez la flotte ici si besoin, puis capturez au labo.'
            );
        }

        if (!empty($features['baie'])) {
            $mve = MveEvaluator::evaluate($state);
            if ($mve['ready'] && empty($state['export']['baie_unlocked'])) {
                return self::post('open_baie', 'Ouvrir le spatio-port', 'institute', '');
            }
        }

        // Craft seulement si bloqué pour continuer — et type déjà débloqué par le tuto
        $reconReady = DroneYard::countReady($state, DroneYard::TYPE_RECON);
        if (DroneYard::canCraft($state, DroneYard::TYPE_RECON) && $reconReady < 1) {
            $recipe = DroneYard::recipes()[DroneYard::TYPE_RECON];
            $a = self::post(
                'craft_drone',
                sprintf('Assembler sonde d’exploration (−%d bio · −%d log)', $recipe['biomass'], $recipe['logistics']),
                'institute',
                ''
            );
            $a['drone_type'] = DroneYard::TYPE_RECON;

            return $a;
        }

        $capReady = DroneYard::countReady($state, DroneYard::TYPE_CAPTURE);
        if (DroneYard::canCraft($state, DroneYard::TYPE_CAPTURE) && $capReady < 1 && $pending !== []) {
            $recipe = DroneYard::recipes()[DroneYard::TYPE_CAPTURE];
            $a = self::post(
                'craft_drone',
                sprintf('Assembler drone de capture (−%d bio · −%d log)', $recipe['biomass'], $recipe['logistics']),
                'institute',
                'Puis retournez au labo pour capturer.'
            );
            $a['drone_type'] = DroneYard::TYPE_CAPTURE;

            return $a;
        }

        // Organique : uniquement missions terrain (plus de synthèse)
        if ($organic < 1 && !empty($features['mission'])) {
            return self::link(
                '?screen=field',
                'Mission pour l’organique',
                'field',
                'L’essence se gagne dehors, avec un spécimen.'
            );
        }

        // Réappro seulement si logistique vraiment à sec
        if (!empty($features['farm']) && $logistics < 1
            && $biomass >= Economy::restockBiomassCost()
            && !TaskRunner::hasRunningType($state, 'restock')) {
            return self::post(
                'restock',
                sprintf('Réappro logistique (−%d bio → +%d log)', Economy::restockBiomassCost(), Economy::restockLogisticsGain()),
                'institute',
                ''
            );
        }

        // Par défaut : horizon (récolte / craft en secondaires)
        return self::link(
            '?screen=world',
            'Vers l’horizon',
            'world',
            ''
        );
    }

    /** Monde : zone → effectifs → recon uniquement. */
    private static function primaryWorld(array $state): array
    {
        $features = Tutorial::features($state);
        $logistics = (int) ($state['resources']['logistics'] ?? 0);
        $selected = $state['world']['selected_zone'] ?? null;
        $creature = $state['creature'] ?? [];
        $step = (string) ($state['tutorial']['step'] ?? '');

        if (!empty($state['tutorial']['active']) && $step === Tutorial::STEP_TEAM
            && empty($state['ui']['team_configured'])) {
            return self::link(
                '?screen=world#team',
                'Composer l’équipe d’exploration',
                'world',
                'Choisissez une zone, allouez sondes / escorte, validez les effectifs.'
            );
        }

        // Soin : pointer le labo, pas soigner ici
        if (GameQueries::needsRecovery($creature) && !empty($creature['analyzed'])) {
            return self::link(
                '?screen=lab',
                'Spécimen fatigué — soigner au labo',
                'lab',
                ''
            );
        }

        return self::explorePrimary($state, $selected, $logistics, $features);
    }

    /** Labo : capture, mutation, croisement (soin / étude → Bestiaire). */
    private static function primaryLab(array $state): array
    {
        $creature = $state['creature'] ?? [];
        $organic = (int) ($state['resources']['organic'] ?? 0);
        $discovered = !empty($creature['discovered']);
        $analyzed = !empty($creature['analyzed']);
        $features = Tutorial::features($state);
        $pending = GameQueries::pendingSignals($state);
        $hasPending = $pending !== [];
        $step = (string) ($state['tutorial']['step'] ?? '');

        if (GameQueries::needsRecovery($creature) && $analyzed) {
            return self::link(
                '?screen=bestiary',
                'Soigner au Bestiaire',
                'bestiary',
                'Les soins se font dans le bestiaire.'
            );
        }

        // Capture prioritaire seulement si le focus est un signal non pris
        if ($discovered && !$analyzed && !empty($features['analyze'])) {
            return self::capturePrimary($state);
        }

        // Tutoriel équipe (ne bloque pas muter/croiser une fois passé)
        if (!empty($state['tutorial']['active']) && $step === Tutorial::STEP_TEAM
            && empty($state['ui']['team_configured'])) {
            return self::link(
                '?screen=world#team',
                'Composer l’équipe d’exploration',
                'world',
                'Sur la carte : zone + effectifs + escorte.'
            );
        }

        $studyN = min(
            max(0, (int) ($state['expedition']['study_count'] ?? 0)),
            DroneYard::countReady($state, DroneYard::TYPE_STUDY)
        );
        $mutateCost = Economy::mutateCost();
        $crossCost = Economy::crossCost();
        $samples = (int) ($state['resources']['samples'] ?? 0);

        // Mutation : coût en prélèvements (organique réservé au croisement / soin)
        if (!empty($features['mutate']) && $analyzed && empty($creature['mutated'])
            && empty($creature['is_hybrid'])) {
            $opts = GameQueries::mutationOptions($creature);
            if ($opts !== []) {
                if ($samples >= $mutateCost) {
                    $mChance = Economy::mutateSuccessChance($creature, $studyN);

                    return [
                        'type' => 'choices',
                        'action' => 'mutate',
                        'label' => sprintf('Muter (~%d%% · −%d prél. · analyse ×%d)', $mChance, $mutateCost, $studyN),
                        'screen' => 'lab',
                        'choices' => [
                            'stats' => ($opts['stats']['label'] ?? 'Renforcer stats') . sprintf(' · ~%d%%', $mChance),
                            'ability' => ($opts['ability']['label'] ?? 'Nouvelle capacité') . sprintf(' · ~%d%%', $mChance),
                        ],
                        'choice_meta' => $opts,
                        'hint' => sprintf(
                            'Réussite ~%d%% (drones d’analyse ×%d). Échec = perte du spécimen. Coût : %d prélèvement(s).',
                            $mChance,
                            $studyN,
                            $mutateCost
                        ),
                        'mutate_chance' => $mChance,
                    ];
                }

                return self::post(
                    'recon',
                    sprintf('Muter : il faut %d prélèvement(s) (vous : %d)', $mutateCost, $samples),
                    'world',
                    'Explorez avec escorte ou capturez pour obtenir des prélèvements, puis muter.'
                );
            }
        }

        // Croisement : UI dans #lab-cross uniquement — pas de CTA « Croiser » en double
        if (!empty($features['cross']) && GameQueries::hasCrossParents($state)) {
            if ($organic < $crossCost) {
                return self::link(
                    '?screen=field',
                    sprintf('Mission pour organique (%d/%d)', $organic, $crossCost),
                    'field',
                    ''
                );
            }
            // Parents prêts : croisement géré par la section ; proposer la suite
            if (!empty($features['mission']) && $analyzed && empty($creature['mission_done'])
                && !GameQueries::needsRecovery($creature)) {
                return self::link('?screen=field', 'Aller au Terrain', 'field', '');
            }

            return self::link('?screen=world', 'Retour carte', 'world', '');
        }

        // Signal en attente si le focus n’est pas une science active
        $activeBlank = empty($creature['discovered']) && empty($creature['analyzed']);
        if ($hasPending && !empty($features['analyze']) && $activeBlank) {
            $firstId = (string) array_key_first($pending);
            $first = $pending[$firstId];
            $zoneName = $state['world']['zones'][(string) ($first['zone_id'] ?? '')]['name']
                ?? ($first['zone_id'] ?? 'zone');
            $n = count($pending);

            return [
                'type' => 'post',
                'action' => 'focus_signal',
                'creature_id' => $firstId,
                'label' => $n > 1
                    ? sprintf('Reprendre un signal (%d en attente)', $n)
                    : sprintf('Reprendre le signal (%s)', $zoneName),
                'screen' => 'lab',
                'hint' => 'Allouez l’effectif de capture, puis tentez la prise.',
            ];
        }

        // Étude / soin : Bestiaire — le labo reste capture / muter / croiser
        $knowledge = CreatureFactory::knowledge($creature);
        if ($analyzed && !empty($features['analyze']) && $knowledge < 100
            && !TaskRunner::hasRunningType($state, 'study')) {
            return self::link(
                '?screen=bestiary',
                sprintf('Étudier au Bestiaire (%d%%)', $knowledge),
                'bestiary',
                ''
            );
        }

        if (!empty($features['mission']) && $analyzed && empty($creature['mission_done'])
            && !GameQueries::needsRecovery($creature)) {
            return self::link('?screen=field', 'Aller au Terrain (mission)', 'field', '');
        }

        return self::link('?screen=world', 'Retour carte', 'world', '');
    }

    /** Bestiaire : soins, étude, focus spécimen. */
    private static function primaryBestiary(array $state): array
    {
        $creature = $state['creature'] ?? [];
        $organic = (int) ($state['resources']['organic'] ?? 0);
        $analyzed = !empty($creature['analyzed']);
        $features = Tutorial::features($state);
        $forms = GameQueries::analyzedCreatures($state);

        if ($forms === [] && empty($features['analyze'])) {
            return self::link('?screen=world', 'Explorer pour capturer', 'world', '');
        }
        if ($forms === []) {
            return self::link('?screen=lab', 'Capturer un signal au labo', 'lab', '');
        }

        if (GameQueries::needsRecovery($creature) && $analyzed) {
            $cost = GameQueries::recoverCost($creature);
            $st = (string) ($creature['status'] ?? '');
            if ($organic >= $cost) {
                return self::post(
                    'recover',
                    $st === 'blessée'
                        ? sprintf('Soigner (−%d organique)', $cost)
                        : sprintf('Restaurer (−%d organique)', $cost),
                    'bestiary',
                    $st === 'blessée'
                        ? 'Sans soin : pas de mission ni d’escorte.'
                        : 'Sans repos : pas d’escorte ni de mission.'
                );
            }

            return self::post(
                'recover',
                sprintf('Organique insuffisant (%d/%d)', $organic, $cost),
                'bestiary',
                sprintf('Il faut %d organique (mission terrain).', $cost)
            );
        }

        $knowledge = CreatureFactory::knowledge($creature);
        if ($analyzed && !empty($features['analyze']) && $knowledge < 100
            && !TaskRunner::hasRunningType($state, 'study')) {
            return self::post(
                'study',
                sprintf('Approfondir l’étude (%d%%)', $knowledge),
                'bestiary',
                ''
            );
        }

        if (!empty($features['mission']) && $analyzed && empty($creature['mission_done'])
            && !GameQueries::needsRecovery($creature)) {
            return self::link('?screen=field', 'Envoyer en mission', 'field', '');
        }

        if (!empty($features['mutate']) && $analyzed && empty($creature['mutated'])
            && empty($creature['is_hybrid'])) {
            return self::link('?screen=lab', 'Muter au laboratoire', 'lab', '');
        }

        if (!empty($features['cross']) && GameQueries::hasCrossParents($state)) {
            return self::link('?screen=lab', 'Croiser au laboratoire', 'lab', '');
        }

        return self::link('?screen=world', 'Retour carte', 'world', '');
    }

    private static function primaryField(array $state): array
    {
        $creature = $state['creature'] ?? [];
        $organic = (int) ($state['resources']['organic'] ?? 0);
        $features = Tutorial::features($state);

        if (GameQueries::needsRecovery($creature) && !empty($creature['analyzed'])) {
            $cost = GameQueries::recoverCost($creature);
            if ($organic >= $cost) {
                return self::post('recover', sprintf('Restaurer (−%d organique)', $cost), 'bestiary', 'Soin au bestiaire.');
            }

            return self::link('?screen=bestiary', 'Soin requis — Bestiaire', 'bestiary', '');
        }

        if (!empty($features['mission']) && !empty($creature['analyzed']) && empty($creature['mission_done'])) {
            $mCh = Economy::missionSuccessChance($creature);

            return self::post(
                'mission',
                sprintf('Mission terrain (~%d%% succès)', $mCh),
                'field',
                sprintf(
                    'Succès ~%d%% · sinon épuisement ou blessure. Soin au bestiaire (−%d org. si blessure).',
                    $mCh,
                    Economy::recoverCost('blessée')
                )
            );
        }

        if (!empty($features['cross']) && GameQueries::canCross($state)) {
            return self::link('?screen=lab', 'Croiser au laboratoire', 'lab', '');
        }

        return self::link('?screen=lab', 'Retour laboratoire', 'lab', '');
    }

    private static function primaryMuseum(array $state): array
    {
        $creature = $state['creature'] ?? [];
        $features = Tutorial::features($state);

        if (!empty($features['museum']) && !empty($creature['analyzed']) && !empty($creature['mission_done'])
            && !GameQueries::isExhibited($state, $creature)) {
            return self::post('exhibit', 'Exposer au musée', 'museum', '');
        }

        return self::link('?screen=world', 'Retour carte', 'world', '');
    }

    private static function explorePrimary(array $state, mixed $selected, int $logistics, array $features): array
    {
        $unlocked = self::unlockedZones($state);
        if ($unlocked === []) {
            return self::link('?screen=world', 'Carte encore opaque', 'world', '');
        }

        $step = (string) ($state['tutorial']['step'] ?? '');
        if ($step === Tutorial::STEP_RECON || $step === Tutorial::STEP_ZONE2) {
            $prefer = self::preferZoneId(
                $state,
                $unlocked,
                $step === Tutorial::STEP_RECON ? 'plaine' : null
            );
            if ($prefer !== null && (string) $selected !== $prefer) {
                $name = $state['world']['zones'][$prefer]['name'] ?? $prefer;

                return [
                    'type' => 'post',
                    'action' => 'select_zone',
                    'zone_id' => $prefer,
                    'label' => sprintf('Choisir : %s', $name),
                    'screen' => 'world',
                    'hint' => 'Ensuite : allouer les effectifs, puis lancer l’exploration.',
                ];
            }
        }

        if ($selected === null || $selected === '' || empty($state['world']['zones'][$selected]['unlocked'])) {
            return self::link(
                '?screen=world',
                'Choisir une zone sur la carte',
                'world',
                'Cliquez un biome pour ouvrir l’allocation d’effectifs.'
            );
        }

        $zone = $state['world']['zones'][$selected];
        $layer = (string) ($zone['layer'] ?? 'natal');
        if ($layer === 'orbital' && empty($state['export']['baie_unlocked'])) {
            return self::link('?screen=world', 'Orbite inaccessible', 'world', '');
        }

        $droneType = $layer === 'orbital'
            ? DroneYard::TYPE_ORBITAL
            : (string) ($state['expedition']['drone_type'] ?? DroneYard::TYPE_RECON);
        if ($droneType === DroneYard::TYPE_CAPTURE) {
            $droneType = DroneYard::TYPE_RECON;
        }

        if (DroneYard::countReady($state, $droneType) < 1) {
            return self::link(
                '?screen=institute',
                'Assembler des sondes à l’Institut',
                'institute',
                'Aucune sonde prête pour cette exploration.'
            );
        }

        $readyN = DroneYard::countReady($state, $droneType);
        $wantN = max(1, min(5, (int) ($state['expedition']['drone_count'] ?? 1)));
        $droneCount = min($wantN, max(1, $readyN));
        $cost = Economy::reconCost($zone, $droneCount);
        if ($logistics < $cost) {
            if (!empty($features['farm'])) {
                return self::link(
                    '?screen=institute',
                    'Logistique insuffisante — réappro à l’Institut',
                    'institute',
                    sprintf('Il faut %d logistique pour cette sortie.', $cost)
                );
            }

            return self::link('?screen=world', 'Logistique insuffisante', 'world', '');
        }

        $scan = (int) ($zone['scan'] ?? 0);
        $chance = GameEngine::explorationSuccessChance($state, $zone, [
            'drone_type' => $droneType,
            'drone_count' => $droneCount,
            'escort_id' => $state['expedition']['escort_creature_id'] ?? null,
        ]);
        $label = $scan > 0 && $scan < 100
            ? sprintf('Lancer l’exploration (×%d · scan %d%% · ~%d%% · −%d log)', $droneCount, $scan, $chance, $cost)
            : sprintf('Lancer l’exploration (×%d sondes · ~%d%% · −%d log)', $droneCount, $chance, $cost);

        return self::post(
            'recon',
            $label,
            'world',
            sprintf('Zone : %s. Effectifs réglés ci-dessous.', $zone['name'] ?? $selected)
        );
    }

    /**
     * Zone prioritaire pour le CTA (tutoriel / première vierge).
     */
    private static function preferZoneId(array $state, array $unlocked, ?string $forceId): ?string
    {
        if ($forceId !== null && isset($unlocked[$forceId])) {
            return $forceId;
        }
        foreach ($unlocked as $id => $z) {
            if (empty($z['explored']) && (($z['layer'] ?? 'natal') === 'natal')) {
                return (string) $id;
            }
        }
        $first = array_key_first($unlocked);

        return $first !== null ? (string) $first : null;
    }

    public static function unlockedZones(array $state): array
    {
        $out = [];
        foreach ($state['world']['zones'] ?? [] as $id => $z) {
            if (!empty($z['unlocked']) && empty($z['locked'])) {
                if (($z['layer'] ?? 'natal') === 'orbital' && empty($state['export']['baie_unlocked'])) {
                    continue;
                }
                $out[$id] = $z;
            }
        }

        return $out;
    }

    public static function missionRiskHint(array $creature): string
    {
        $chance = Economy::missionSuccessChance($creature);
        $fail = 100 - $chance;
        $heal = Economy::recoverCost('blessée');
        $rest = Economy::recoverCost('épuisée');

        return sprintf(
            'Mission terrain — le spécimen part seul. '
            . 'Succès estimé ~%d%% (pureté %d%%, statut %s). '
            . 'Sinon (~%d%%) : épuisement (−%d org.) ou blessure (−%d org.). Soin au Laboratoire.',
            $chance,
            (int) ($creature['purity'] ?? 100),
            $creature['status'] ?? 'prête',
            $fail,
            $rest,
            $heal
        );
    }

    public static function secondary(array $state): array
    {
        $actions = [];
        $features = Tutorial::features($state);
        $creature = $state['creature'] ?? [];
        $organic = (int) ($state['resources']['organic'] ?? 0);
        $primary = self::primary($state);
        $primaryAction = (string) ($primary['action'] ?? '');
        $screen = (string) ($state['screen'] ?? 'institute');
        $pending = GameQueries::pendingSignals($state);

        if ($screen === 'institute') {
            $biomass = (int) ($state['resources']['biomass'] ?? 0);

            // Organique = missions uniquement (pas de distillation)
            if (!empty($features['farm']) && !TaskRunner::hasRunningType($state, 'farm')
                && $primaryAction !== 'farm') {
                $actions[] = [
                    'type' => 'post',
                    'action' => 'farm',
                    'label' => sprintf('Récolter biomasse (~+%d)', Economy::farmBiomassYield($state)),
                    'screen' => 'institute',
                    'disabled' => false,
                ];
            }
            if (!empty($features['farm']) && !TaskRunner::hasRunningType($state, 'restock')
                && $biomass >= Economy::restockBiomassCost()
                && $primaryAction !== 'restock') {
                $actions[] = [
                    'type' => 'post',
                    'action' => 'restock',
                    'label' => sprintf(
                        'Réappro (−%d bio → +%d log)',
                        Economy::restockBiomassCost(),
                        Economy::restockLogisticsGain()
                    ),
                    'screen' => 'institute',
                    'disabled' => false,
                ];
            }
        }

        if ($screen === 'lab') {
            $studyN = min(
                max(0, (int) ($state['expedition']['study_count'] ?? 0)),
                DroneYard::countReady($state, DroneYard::TYPE_STUDY)
            );
            $mutateCost = Economy::mutateCost();
            $crossCost = Economy::crossCost();

            if (GameQueries::needsRecovery($creature) && !empty($creature['analyzed']) && $primaryAction !== 'recover') {
                $actions[] = [
                    'type' => 'link',
                    'href' => '?screen=bestiary',
                    'label' => 'Soigner au Bestiaire',
                    'screen' => 'bestiary',
                    'disabled' => false,
                ];
            }

            // Mutation en secondaire si éligible (coût prélèvements)
            if (!empty($features['mutate']) && !empty($creature['analyzed']) && empty($creature['mutated'])
                && empty($creature['is_hybrid']) && $primaryAction !== 'mutate') {
                $opts = GameQueries::mutationOptions($creature);
                $samplesLab = (int) ($state['resources']['samples'] ?? 0);
                if ($opts !== []) {
                    $mChance = Economy::mutateSuccessChance($creature, $studyN);
                    $canMut = $samplesLab >= $mutateCost;
                    if ($canMut) {
                        $actions[] = [
                            'type' => 'choices',
                            'action' => 'mutate',
                            'label' => sprintf('Muter (~%d%% · −%d prél. · analyse ×%d)', $mChance, $mutateCost, $studyN),
                            'screen' => 'lab',
                            'choices' => [
                                'stats' => ($opts['stats']['label'] ?? 'Stats') . sprintf(' · ~%d%%', $mChance),
                                'ability' => ($opts['ability']['label'] ?? 'Capacité') . sprintf(' · ~%d%%', $mChance),
                            ],
                            'choice_meta' => $opts,
                            'hint' => sprintf(
                                'Échec = perte (~%d%%). Coût %d prél. · analyse ×%d.',
                                100 - $mChance,
                                $mutateCost,
                                $studyN
                            ),
                            'mutate_chance' => $mChance,
                            'disabled' => false,
                        ];
                    } else {
                        $actions[] = [
                            'type' => 'link',
                            'href' => '?screen=world',
                            'label' => sprintf('Muter — besoin %d prélèvement(s)', $mutateCost),
                            'screen' => 'world',
                            'disabled' => false,
                        ];
                    }
                }
            }

            // Croisement : section #lab-cross uniquement (pas de secondaire doublon)

            // Étude : uniquement via la section labo (évite double bouton)
            // (primaryLab peut encore renvoyer « Approfondir » hors labo ou si section absente)
        }

        if ($screen === 'bestiary') {
            $knowledge = CreatureFactory::knowledge($creature);
            if (!empty($creature['analyzed']) && $knowledge < 100
                && !TaskRunner::hasRunningType($state, 'study')
                && $primaryAction !== 'study') {
                $actions[] = [
                    'type' => 'post',
                    'action' => 'study',
                    'label' => sprintf('Approfondir (%d%%)', $knowledge),
                    'screen' => 'bestiary',
                    'disabled' => false,
                ];
            }
            if (!empty($features['lab']) || !empty($features['nav_lab'])) {
                $actions[] = [
                    'type' => 'link',
                    'href' => '?screen=lab',
                    'label' => 'Laboratoire',
                    'screen' => 'lab',
                    'disabled' => false,
                ];
            }
            if (!empty($features['mission']) && !empty($creature['analyzed'])
                && empty($creature['mission_done']) && !GameQueries::needsRecovery($creature)
                && $primaryAction !== 'mission') {
                $actions[] = [
                    'type' => 'link',
                    'href' => '?screen=field',
                    'label' => 'Terrain',
                    'screen' => 'field',
                    'disabled' => false,
                ];
            }
        }

        if ($screen === 'field' && GameQueries::needsRecovery($creature) && $primaryAction !== 'recover') {
            $actions[] = [
                'type' => 'link',
                'href' => '?screen=bestiary',
                'label' => 'Soigner au Bestiaire',
                'screen' => 'bestiary',
                'disabled' => false,
            ];
        }

        return array_values(array_filter(
            $actions,
            static fn (array $a): bool => ($a['action'] ?? '') !== $primaryAction
                && ($a['href'] ?? '') !== ($primary['href'] ?? '__none__')
        ));
    }

    private static function capturePrimary(array $state): array
    {
        $readyCap = DroneYard::countReady($state, DroneYard::TYPE_CAPTURE);
        if ($readyCap < 1) {
            return self::link(
                '?screen=institute',
                'Assembler un drone de capture (Institut)',
                'institute',
                'Le signal reste en attente. Assemblez des drones de prise, puis revenez.'
            );
        }
        $want = max(1, min(5, (int) ($state['expedition']['capture_count'] ?? 1)));
        $capN = min($want, $readyCap);
        $capCh = Economy::captureSuccessChance($state, $capN);

        return self::post(
            'analyze',
            sprintf('Tenter la capture (~%d%% · ×%d)', $capCh, $capN),
            'lab',
            sprintf(
                'Échec = drone + ressources perdus, signal fuit. Pour monter le %% : Monde → zone → effectif capture, puis revenir.',
                $capCh
            )
        );
    }

    private static function post(string $action, string $label, string $screen, string $hint): array
    {
        return [
            'type' => 'post',
            'action' => $action,
            'label' => $label,
            'screen' => $screen,
            'hint' => $hint,
        ];
    }

    private static function link(string $href, string $label, string $screen, string $hint): array
    {
        return [
            'type' => 'link',
            'href' => $href,
            'label' => $label,
            'screen' => $screen,
            'hint' => $hint,
        ];
    }
}
