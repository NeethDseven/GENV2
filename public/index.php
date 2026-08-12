<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use Genesis\Game\GameEngine;
use Genesis\Game\GameQueries;
use Genesis\Game\Economy;
use Genesis\Game\Data\Catalog;
use Genesis\Game\VisualTheme;
use Genesis\Game\CreatureFactory;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $screen = GameEngine::process($_POST);
    header('Location: ?screen=' . rawurlencode($screen));
    exit;
}

$state = GameEngine::view($_GET);
$view = $state['view'];
$screen = $state['screen'] ?? 'institute';
$creature = $state['creature'] ?? [];
$resources = $state['resources'] ?? [];
$drones = $state['drones'] ?? [];
$campus = $state['campus'] ?? [];
$genesis = $state['ui']['genesis'] ?? ['marker' => 'Observation', 'message' => ''];
// Événement one-shot (vu via view, déjà effacé de la session)
$event = $view['latest_event'] ?? $state['ui']['latest_event'] ?? ['label' => '', 'message' => ''];
$memories = array_reverse($state['heritage']['memories'] ?? []);
$exhibits = $state['museum']['exhibits'] ?? [];
$primary = $view['primary'];
$secondary = $view['secondary'];
$status = (string) ($creature['status'] ?? 'prête');
$statusClass = match ($status) {
    'blessée' => 'chip--danger',
    'épuisée' => 'chip--warn',
    default => 'chip--ok',
};
$analyzed = $view['analyzed'];
$activeId = (string) ($creature['id'] ?? '');
$selectedZone = $state['world']['selected_zone'] ?? null;
$stats = $creature['stats'] ?? [];
$mve = $view['mve'] ?? ['score' => '0/0', 'checks' => [], 'complete' => false, 'ready' => false];
$saves = $view['saves'] ?? [];
$export = $view['export'] ?? [];
$synergiesLog = $view['synergies_log'] ?? [];
$manifest = is_array($export['manifest'] ?? null) ? $export['manifest'] : [];
$codex = $view['codex'] ?? [];
$codexFound = (int) ($view['codex_found'] ?? 0);
$codexTotal = (int) ($view['codex_total'] ?? 0);
$tips = $view['playtest_tips'] ?? [];
$tipsDismissed = !empty($view['tips_dismissed']);
$economy = $view['economy'] ?? [];
$tasks = $view['tasks'] ?? [];
$tasksNow = (int) ($view['tasks_server_now'] ?? time());
$droneSummary = $view['drone_summary'] ?? [];
$droneRecipes = $view['drone_recipes'] ?? [];
$lastReport = $view['last_report'] ?? null;
$baieUnlocked = !empty($view['baie_unlocked']);
$missionRisk = $view['mission_risk'] ?? null;
$features = $view['features'] ?? [];
$assistant = $view['assistant'] ?? ['title' => 'Assistant', 'message' => $genesis['message'] ?? '', 'marker' => $genesis['marker'] ?? 'Observation', 'goal' => ''];
$tutorial = $view['tutorial'] ?? ['active' => false, 'step' => 'free_play'];
$expedition = $view['expedition'] ?? [];
$teamChance = (int) ($view['team_chance'] ?? 0);
$farmPreview = (int) ($view['farm_yield_preview'] ?? 3);
$biomass = (int) ($view['biomass'] ?? ($resources['biomass'] ?? 0));
$tutorialActive = !empty($tutorial['active']);
$activeBiome = (string) ($creature['biome'] ?? ($state['world']['zones'][$selectedZone]['biome'] ?? ''));
$bodyBiome = VisualTheme::biomeSlug($activeBiome);

// ——— Séparation stricte des écrans ———
$showDecision = true;
$showWorldMap = ($screen === 'world');
// Panneau croisement dès 2 espèces distinctes (même sans organique — le bouton sera désactivé)
$showCross = ($screen === 'lab')
    && !empty($features['cross'])
    && !empty($view['has_cross_parents']);
$canCrossNow = !empty($view['can_cross']);
$crossCost = Economy::crossCost();
$mutateCost = Economy::mutateCost();
$organicHave = (int) ($resources['organic'] ?? 0);
$samplesHave = (int) ($resources['samples'] ?? 0);
$canMutateNow = $samplesHave >= $mutateCost;
$hasSignal = !empty($creature['discovered']) || !empty($creature['analyzed']);
// Spécimen détaillé : bestiaire (+ terrain si mission)
$showSpecimenFull = in_array($screen, ['bestiary', 'field'], true) && $hasSignal
    && (empty($creature['discovered']) || !empty($creature['analyzed']) || $screen === 'field');
// Sur bestiaire : toujours afficher le focus s’il y a une forme capturée
if ($screen === 'bestiary' && !empty($creature['analyzed'])) {
    $showSpecimenFull = true;
}
// Carte compacte retirée (spécimen = full sur bestiaire / terrain)
$showSpecimenCompact = false;
// Bestiaire : page dédiée
$showBestiary = ($screen === 'bestiary') && !empty($features['panel_bestiary']);
// Flotte + craft : institut seulement
$showFleet = ($screen === 'institute');
// Effectifs d’exploration : monde, seulement si zone choisie
$showDeploy = ($screen === 'world') && $selectedZone !== null && $selectedZone !== '';
// Labo : Capture · Analyse (mut/croix) · Mutation · Croisement
$showCaptureDeploy = ($screen === 'lab') && !empty($creature['discovered']) && empty($creature['analyzed']);
$showMutateSection = ($screen === 'lab')
    && !empty($features['mutate'])
    && !empty($creature['analyzed'])
    && empty($creature['mutated'])
    && empty($creature['is_hybrid']);
// Étude approfondie : bestiaire (fiche spécimen) — pas de carte Étude au labo
$showDeepenStudy = ($screen === 'bestiary')
    && !empty($creature['analyzed'])
    && CreatureFactory::knowledge($creature) < 100
    && !empty($features['analyze']);
// Drones d’analyse : intégrés dans Mutation / Croisement (labo)
$showStudyDeploy = ($screen === 'lab') && ($showMutateSection || $showCross);
$showStudyCard = false; // carte labo retirée
$showBestiaryStudyCard = false; // pas de double carte étude (actions sur fiche + primary)
// Compat filtres action-row
$showStudySection = $showCaptureDeploy || $showDeepenStudy;
// Créatures d’appui capture
$captureEscortIds = Economy::captureEscortIds($state);
$captureEscortMax = Economy::captureEscortMax();
// Signaux en attente : labo seulement
$showPendingSignals = ($screen === 'lab');
// Campus retiré (redondant)
$showCampus = false;
$showReport = $lastReport && in_array($screen, ['institute', 'lab', 'field'], true);
$showMve = ($screen === 'institute') && !empty($features['mve_panel']);
$showSynergies = ($screen === 'lab' || $screen === 'codex') && $synergiesLog !== [] && !empty($features['cross']);
// Journal de bord scientifique : institut, dès qu’il y a des traces (pas un wiki)
$memoryJournal = $view['memory_journal'] ?? ['entries' => [], 'total' => 0, 'empty' => true];
$showMemory = ($screen === 'institute')
    && empty($memoryJournal['empty'])
    && (
        empty($tutorial['active'])
        || !empty($features['panel_memory'])
        || (int) ($memoryJournal['total'] ?? 0) >= 2
    );
$showMuseumPanel = ($screen === 'museum') && ((!empty($features['panel_museum']) || $exhibits !== []));
$showArchives = ($screen === 'institute');
$showMissionRisk = ($screen === 'field') && $missionRisk;
$showInstituteSummary = ($screen === 'institute');
$hud = $view['hud'] ?? [
    'recon_ready' => 0, 'recon_total' => 0, 'recon_busy' => 0,
    'capture_ready' => 0, 'capture_total' => 0, 'capture_busy' => 0,
    'orbital_ready' => 0, 'orbital_total' => 0, 'species' => 0,
];
$reconChanceTable = $view['recon_chance_table'] ?? [];
$captureChanceTable = $view['capture_chance_table'] ?? [];
$deploy = $view['deploy'] ?? [];
$reconReady = (int) ($hud['recon_ready'] ?? 0);
$reconTotal = (int) ($hud['recon_total'] ?? $reconReady);
$captureReady = (int) ($hud['capture_ready'] ?? 0);
$captureTotal = (int) ($hud['capture_total'] ?? $captureReady);
$studyReady = (int) ($hud['study_ready'] ?? 0);
$studyTotal = (int) ($hud['study_total'] ?? $studyReady);
$reconWant = max(1, min(5, (int) ($expedition['drone_count'] ?? 1)));
$captureWant = max(1, min(5, (int) ($expedition['capture_count'] ?? 1)));
$studyWant = max(0, min(5, (int) ($expedition['study_count'] ?? 0)));
$flash = $view['flash'] ?? ($state['ui']['flash'] ?? null);
$mutateChance = $view['mutate_chance'] ?? null;
$crossChance = $view['cross_chance'] ?? null;
$captureChance = $view['capture_chance'] ?? null;
$pendingSignals = $view['pending_signals'] ?? [];
$progress = $view['progress'] ?? [];
$studyChanceTable = $view['study_chance_table'] ?? [];

/** Secondaires déjà filtrés par IntentResolver selon l’écran */
$secondaryForScreen = static function (array $sec, string $screen): bool {
    return true;
};

$nav = [
    'institute' => 'Institut',
    'world' => 'Horizon',
];
if (!empty($features['nav_lab'])) {
    $nav['lab'] = 'Labo';
}
if (!empty($features['nav_bestiary'])) {
    $nav['bestiary'] = 'Bestiaire';
}
if (!empty($features['nav_field'])) {
    $nav['field'] = 'Terrain';
}
if (!empty($features['nav_museum'])) {
    $nav['museum'] = 'Musée';
}
if (!empty($features['nav_codex'])) {
    $nav['codex'] = 'Codex';
}

?><!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GENESIS — Découverte & croisement</title>
    <link rel="stylesheet" href="assets/styles.css?v=aethel-33">
</head>
<body class="biome-body biome-body--<?php echo htmlspecialchars($bodyBiome, ENT_QUOTES, 'UTF-8'); ?> screen-<?php echo htmlspecialchars($screen, ENT_QUOTES, 'UTF-8'); ?>"
      data-screen="<?php echo htmlspecialchars($screen, ENT_QUOTES, 'UTF-8'); ?>">
<main class="shell" id="genesis-shell">
    <section class="hero">
        <div class="hero__eyebrow">GENESIS · Aster-0</div>
        <h1><?php echo htmlspecialchars($view['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <?php if (!empty($view['subtitle'])): ?>
            <p class="hero__subtitle"><?php echo htmlspecialchars($view['subtitle'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <div class="hero__meta" id="genesis-hud">
            <span id="hud-sondes" class="hud-chip hud-chip--fleet" data-tip="Sondes d’exploration — prêtes / total">
                Sondes <?php echo (int) ($hud['recon_ready'] ?? 0); ?>/<?php echo (int) ($hud['recon_total'] ?? 0); ?>
            </span>
            <span id="hud-filets" class="hud-chip hud-chip--fleet" data-tip="Filets de capture — prêtes / total">
                Filets <?php echo $captureReady; ?>/<?php echo $captureTotal; ?>
            </span>
            <span id="hud-lentilles" class="hud-chip hud-chip--fleet" data-tip="Lentilles d’analyse — stabilisent mutation et croisement">
                Lentilles <?php echo $studyReady; ?>/<?php echo $studyTotal; ?>
            </span>
            <?php if (!empty($features['baie']) || !empty($view['baie_unlocked'])): ?>
                <span class="hud-chip hud-chip--fleet" data-tip="Sondes orbitales">
                    Orbite <?php echo (int) ($hud['orbital_ready'] ?? 0); ?>/<?php echo (int) ($hud['orbital_total'] ?? 0); ?>
                </span>
            <?php endif; ?>
            <span id="hud-formes" class="hud-chip" data-tip="Formes que tu as retenues">
                Formes <?php echo (int) ($hud['species'] ?? count($analyzed)); ?>
            </span>
            <?php if (!empty($features['codex'])): ?>
                <span class="hud-chip" data-tip="Synergies nommées découvertes">
                    Noms <?php echo $codexFound; ?>/<?php echo $codexTotal; ?>
                </span>
            <?php endif; ?>
            <?php foreach (($view['resource_tips'] ?? []) as $res): ?>
                <span class="hud-chip hud-chip--res hud-chip--res-<?php echo htmlspecialchars((string) ($res['key'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                      data-tip="<?php echo htmlspecialchars((string) ($res['tip'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars((string) ($res['label'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                    <strong><?php echo (int) ($res['value'] ?? 0); ?></strong>
                </span>
            <?php endforeach; ?>
            <button type="button" id="audio-toggle" class="hud-chip audio-toggle" aria-pressed="false" title="Son">
                <span class="audio-toggle__icon" aria-hidden="true">♪</span>
                <span class="audio-toggle__label">Son</span>
            </button>
        </div>

        <?php if (is_array($flash) && !empty($flash['message'])): ?>
            <?php
            // Horizon (choix de zone) : plus long pour cliquer ; toasts simples ~3s
            $flashZonesRaw = !empty($flash['zones']) && is_array($flash['zones']) ? $flash['zones'] : [];
            // Dédupliquer + normaliser id / nom
            $flashZones = [];
            $seenFz = [];
            foreach ($flashZonesRaw as $fz) {
                if (is_array($fz)) {
                    $zid = (string) ($fz['id'] ?? $fz['zone_id'] ?? '');
                    $zname = (string) ($fz['name'] ?? '');
                } else {
                    $zid = (string) $fz;
                    $zname = '';
                }
                if ($zid === '' || isset($seenFz[$zid])) {
                    continue;
                }
                $seenFz[$zid] = true;
                if ($zname === '') {
                    $zname = (string) ($state['world']['zones'][$zid]['name'] ?? $zid);
                }
                $flashZones[] = ['id' => $zid, 'name' => $zname];
            }
            $hasFlashZones = $flashZones !== [];
            $flashCtaLab = !empty($flash['cta_lab']);
            $flashMs = $hasFlashZones || $flashCtaLab ? 10000 : 3200;
            ?>
            <div class="flash-banner flash-banner--ephemeral flash-banner--<?php echo htmlspecialchars((string) ($flash['type'] ?? 'discovery'), ENT_QUOTES, 'UTF-8'); ?><?php echo $hasFlashZones || $flashCtaLab ? ' flash-banner--horizon' : ''; ?>"
                 role="status"
                 data-dismiss-ms="<?php echo (int) $flashMs; ?>">
                <button type="button" class="flash-banner__close" aria-label="Fermer">×</button>
                <strong><?php echo htmlspecialchars((string) ($flash['title'] ?? 'Découverte'), ENT_QUOTES, 'UTF-8'); ?></strong>
                <span><?php echo htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8'); ?></span>
                <?php if ($hasFlashZones || $flashCtaLab): ?>
                    <div class="flash-banner__zones">
                        <?php if ($flashCtaLab): ?>
                            <a class="button button--primary button--sm" href="?screen=lab">
                                <?php echo htmlspecialchars((string) ($flash['cta_lab_label'] ?? 'Préparer la capture'), ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        <?php endif; ?>
                        <?php foreach ($flashZones as $fz): ?>
                            <form method="post" class="flash-banner__zone-form">
                                <input type="hidden" name="action" value="select_zone">
                                <input type="hidden" name="zone_id" value="<?php echo htmlspecialchars($fz['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="button button--secondary button--sm"
                                    title="<?php echo htmlspecialchars('Sélectionner ' . $fz['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                    Explorer : <?php echo htmlspecialchars($fz['name'], ENT_QUOTES, 'UTF-8'); ?>
                                </button>
                            </form>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($tasks !== []): ?>
            <div class="task-dock" id="task-dock" data-server-now="<?php echo $tasksNow; ?>">
                <?php foreach ($tasks as $task): ?>
                    <div class="task-chip"
                         data-started="<?php echo (int) ($task['started_at'] ?? 0); ?>"
                         data-duration="<?php echo (int) ($task['duration'] ?? 1); ?>">
                        <div class="donut" style="--p: <?php echo (int) ($task['percent'] ?? 0); ?>"></div>
                        <div class="task-chip__text">
                            <strong class="task-chip__label"><?php echo htmlspecialchars((string) ($task['label'] ?? '…'), ENT_QUOTES, 'UTF-8'); ?></strong>
                            <span class="task-chip__eta"><?php echo (int) ($task['remaining'] ?? 0); ?>s</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php
        $assistMsg = trim((string) ($assistant['message'] ?? ''));
        // Ignorer l’ancienne phrase stockée en session si l’assistant est silencieux
        if ($assistMsg === '') {
            $assistMsg = '';
        }
        ?>
        <?php if ($assistMsg !== ''): ?>
        <div class="assistant-panel <?php echo $tutorialActive ? 'is-tutorial' : ''; ?>">
            <div class="assistant-panel__head">
                <span class="assistant-panel__avatar" aria-hidden="true">G</span>
                <div class="assistant-panel__identity">
                    <span class="assistant-panel__badge"><?php echo htmlspecialchars((string) ($assistant['name'] ?? 'GENESIS'), ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="assistant-panel__marker"><?php echo htmlspecialchars((string) ($assistant['marker'] ?? 'Observation'), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </div>
            <p class="assistant-panel__message"><?php echo htmlspecialchars($assistMsg, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <?php endif; ?>

        <?php
        $eventMsg = trim((string) ($event['message'] ?? ''));
        $flashMsg = is_array($flash) ? trim((string) ($flash['message'] ?? '')) : '';
        // Ne pas doubler flash + event ; one-shot uniquement
        $showEvent = $eventMsg !== ''
            && $eventMsg !== $assistMsg
            && ($flashMsg === '' || !str_contains($flashMsg, $eventMsg) && !str_contains($eventMsg, $flashMsg))
            && !str_starts_with((string) ($event['label'] ?? ''), 'Assistant');
        ?>
        <?php if ($showEvent): ?>
        <div class="hero__event hero__event--ephemeral" data-dismiss-ms="3200" role="status">
            <button type="button" class="flash-banner__close" aria-label="Fermer">×</button>
            <strong><?php echo htmlspecialchars((string) ($event['label'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></strong>
            <span><?php echo htmlspecialchars($eventMsg, ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <?php endif; ?>

        <nav class="topnav">
            <?php foreach ($nav as $key => $label): ?>
                <a class="topnav__item <?php echo $screen === $key ? 'is-active' : ''; ?>"
                   href="?screen=<?php echo urlencode($key); ?>">
                    <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </section>

    <section class="grid grid--<?php echo htmlspecialchars($screen, ENT_QUOTES, 'UTF-8'); ?>">
            <?php if ($screen === 'codex' && !empty($features['codex'])): ?>
            <article class="panel panel--codex panel--accent panel--wide">
                <h2>Codex des synergies</h2>
                <p class="panel__lead">Découvertes : <?php echo $codexFound; ?> / <?php echo $codexTotal; ?> paires nommées</p>
                <p class="panel__text">Croisez des espèces de biomes différents pour débloquer les entrées. Les croix génériques (Écho mixte) ne remplissent pas le codex.</p>
                <div class="codex-grid">
                    <?php foreach ($codex as $entry): ?>
                        <?php $open = !empty($entry['discovered']); ?>
                        <div class="codex-card <?php echo $open ? 'is-open' : 'is-locked'; ?>">
                            <?php if ($open): ?>
                                <div class="codex-card__title"><?php echo htmlspecialchars((string) ($entry['title'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="codex-card__biomes">
                                    <?php echo htmlspecialchars(implode(' × ', $entry['biomes'] ?? []), ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                                <div class="codex-card__ability">
                                    <?php echo htmlspecialchars((string) ($entry['ability'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                    <?php
                                    $bonusBits = [];
                                    foreach ($entry['stat_bonus'] ?? [] as $stat => $delta) {
                                        $bonusBits[] = strtoupper(substr((string) $stat, 0, 1)) . '+' . (int) $delta;
                                    }
                                    if ($bonusBits !== []):
                                    ?>
                                        · <?php echo htmlspecialchars(implode(' ', $bonusBits), ENT_QUOTES, 'UTF-8'); ?>
                                    <?php endif; ?>
                                </div>
                                <p class="codex-card__flavor"><?php echo htmlspecialchars((string) ($entry['flavor'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php else: ?>
                                <div class="codex-card__title">???</div>
                                <div class="codex-card__biomes">
                                    <?php echo htmlspecialchars(implode(' × ', $entry['biomes'] ?? []), ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                                <p class="codex-card__flavor">Non découverte — croisez ces deux biomes.</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </article>
            <?php endif; ?>

            <?php if ($screen === 'departure' && !empty($export['departed']) && !empty($manifest)): ?>
            <article class="panel panel--departure panel--accent panel--wide">
                <h2>Spatio-port — manifeste</h2>
                <p class="panel__lead"><?php echo htmlspecialchars((string) ($manifest['summary'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="panel__text">L’orbite est ouverte. Continuez la science sur Aster-0 ou lancez des recon orbitales.</p>

                <div class="departure-grid">
                    <div>
                        <h3 class="zone-picker__title">Espèces emportées</h3>
                        <ul class="steps">
                            <?php foreach ($manifest['species'] ?? [] as $sp): ?>
                                <?php if (is_array($sp)): ?>
                                    <li>
                                        <strong><?php echo htmlspecialchars((string) ($sp['name'] ?? $sp['species'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></strong>
                                        — <?php echo htmlspecialchars((string) ($sp['species'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                        · <?php echo htmlspecialchars((string) ($sp['rarity'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                        <?php if (!empty($sp['synergy_title'])): ?>
                                            · <em><?php echo htmlspecialchars((string) $sp['synergy_title'], ENT_QUOTES, 'UTF-8'); ?></em>
                                        <?php endif; ?>
                                    </li>
                                <?php else: ?>
                                    <li><?php echo htmlspecialchars((string) $sp, ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div>
                        <h3 class="zone-picker__title">Hybrides</h3>
                        <?php if (empty($manifest['hybrids'])): ?>
                            <p class="panel__text">Aucun hybride dans le manifeste.</p>
                        <?php else: ?>
                            <ul class="steps">
                                <?php foreach ($manifest['hybrids'] as $h): ?>
                                    <li>
                                        <strong><?php echo htmlspecialchars((string) ($h['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></strong>
                                        <?php if (!empty($h['parents'])): ?>
                                            · <?php echo htmlspecialchars(implode(' × ', $h['parents']), ENT_QUOTES, 'UTF-8'); ?>
                                        <?php endif; ?>
                                        <?php if (!empty($h['synergy'])): ?>
                                            · <?php echo htmlspecialchars((string) $h['synergy'], ENT_QUOTES, 'UTF-8'); ?>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h3 class="zone-picker__title">Musée</h3>
                        <p class="panel__text"><?php echo (int) ($manifest['exhibit_count'] ?? 0); ?> pièce(s) · témoignage, pas trophée.</p>
                        <ul class="steps">
                            <?php foreach (array_slice($manifest['exhibits'] ?? [], 0, 5) as $ex): ?>
                                <li><?php echo htmlspecialchars((string) ($ex['species'] ?? $ex['label'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div>
                        <h3 class="zone-picker__title">Synergies</h3>
                        <?php if (empty($manifest['synergies'])): ?>
                            <p class="panel__text">Aucune synergie nommée exportée.</p>
                        <?php else: ?>
                            <ul class="steps">
                                <?php foreach ($manifest['synergies'] as $syn): ?>
                                    <li><?php echo htmlspecialchars((string) ($syn['title'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

            </article>
            <?php endif; ?>

        <?php if ($screen !== 'bestiary'): ?>
        <article class="panel panel--decision panel--main">
            <h2><?php
                echo match ($screen) {
                    'world' => 'Carte du berceau',
                    'lab' => 'Tables froides',
                    'field' => 'Seuil du monde',
                    'museum' => 'Salle de mémoire',
                    'codex' => 'Noms secrets',
                    default => 'Salle de l’Institut',
                };
            ?></h2>
            <?php
            $screenHint = match ($screen) {
                'world' => 'Choisis une terre. Alloue les sondes. Le monde ne se vide pas d’un passage — il se lit.',
                'lab' => 'Prendre. Tordre. Unir. Ici, le vivant répond — ou se brise.',
                'field' => 'Un seul spécimen, sans escorte. La terre rend de l’organique… ou du sang.',
                'museum' => 'Ce que tu exposes reste. Ce n’est pas un trophée — c’est une trace.',
                default => '', // Institut : journal + actions suffisent
            };
            ?>
            <?php if ($screenHint !== ''): ?>
                <p class="screen-hint screen-hint--lore"><?php echo htmlspecialchars($screenHint, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <?php if ($showInstituteSummary): ?>
                <?php
                $progStages = $progress['stages'] ?? [];
                $progCurrent = (string) ($progress['current'] ?? '—');
                $pendingN = count($pendingSignals);
                $analyzedN = count($analyzed);
                ?>
                <div class="institute-summary">
                    <h3 class="zone-picker__title">Journal de bord</h3>

                    <div class="summary-grid">
                        <div class="summary-row">
                            <span class="summary-row__k">Fil</span>
                            <span class="summary-row__v"><?php echo htmlspecialchars($progCurrent, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-row__k">Formes</span>
                            <span class="summary-row__v"><?php echo $analyzedN; ?> retenues</span>
                        </div>
                        <?php if ($pendingN > 0): ?>
                        <div class="summary-row">
                            <span class="summary-row__k">Échos</span>
                            <span class="summary-row__v">
                                <?php echo $pendingN; ?> signal<?php echo $pendingN > 1 ? 's' : ''; ?>
                                · <a href="?screen=lab">au labo</a>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($progStages !== [] && !empty($tutorial['active'])): ?>
                        <div class="summary-progress">
                            <span class="summary-res__label">Fil du berceau</span>
                            <div class="progress-track__steps progress-track__steps--compact">
                                <?php foreach ($progStages as $st): ?>
                                    <span class="progress-step <?php echo !empty($st['done']) ? 'is-done' : ''; ?>">
                                        <?php echo htmlspecialchars((string) ($st['label'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    $freeplay = $view['freeplay'] ?? [];
                    $fpItems = is_array($freeplay['items'] ?? null) ? $freeplay['items'] : [];
                    $fpActive = !empty($freeplay['active']) && empty($tutorial['active']) && $fpItems !== [];
                    ?>
                    <?php if ($fpActive): ?>
                        <div class="freeplay-goals" id="fil-libre">
                            <div class="freeplay-goals__head">
                                <h3 class="zone-picker__title">Fil libre</h3>
                                <span class="freeplay-goals__score"><?php echo htmlspecialchars((string) ($freeplay['score'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <p class="screen-hint screen-hint--lore">
                                Plus de tutoriel — seulement des fils à tirer. Récompenses soft, jamais de grind.
                            </p>
                            <ul class="freeplay-goals__list">
                                <?php foreach ($fpItems as $g): ?>
                                    <li class="freeplay-goal <?php echo !empty($g['done']) ? 'is-done' : ''; ?>">
                                        <span class="freeplay-goal__mark" aria-hidden="true"><?php echo !empty($g['done']) ? '✓' : '·'; ?></span>
                                        <div class="freeplay-goal__body">
                                            <strong><?php echo htmlspecialchars((string) ($g['title'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></strong>
                                            <?php if (empty($g['done'])): ?>
                                                <em><?php echo htmlspecialchars((string) ($g['hint'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></em>
                                                <?php if (!empty($g['screen']) && !empty($g['cta'])): ?>
                                                    <a class="freeplay-goal__cta" href="?screen=<?php echo urlencode((string) $g['screen']); ?>">
                                                        <?php echo htmlspecialchars((string) $g['cta'], ENT_QUOTES, 'UTF-8'); ?>
                                                    </a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (!empty($freeplay['pride'])): ?>
                                <p class="freeplay-goals__pride"><?php echo htmlspecialchars((string) $freeplay['pride'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

            <?php if ($showPendingSignals && $pendingSignals !== []): ?>
                <div class="pending-signals" id="signals">
                    <h3 class="zone-picker__title">Échos encore vivants</h3>
                    <p class="screen-hint screen-hint--lore">
                        Ils n’ont pas fui. Prépare le filet — une prise ratée les fait disparaître.
                    </p>
                    <div class="pending-signals__list">
                        <?php foreach ($pendingSignals as $sig): ?>
                            <?php
                            $sid = (string) ($sig['id'] ?? '');
                            $sz = (string) ($sig['zone_id'] ?? '');
                            $szName = $state['world']['zones'][$sz]['name'] ?? $sz;
                            $isActiveSig = $sid !== '' && $sid === $activeId && !empty($creature['discovered']) && empty($creature['analyzed']);
                            // Aperçu de chance si on activait ce signal (même économie globale)
                            $capPreview = Economy::captureSuccessChance(
                                $state,
                                min(5, max(1, (int) ($expedition['capture_count'] ?? 1)), max(1, $captureReady))
                            );
                            ?>
                            <div class="pending-signal <?php echo $isActiveSig ? 'is-active' : ''; ?>">
                                <div class="pending-signal__body">
                                    <strong><?php echo $isActiveSig ? 'Signal actif' : 'Signal en attente'; ?></strong>
                                    <span>
                                        <?php echo htmlspecialchars((string) $szName, ENT_QUOTES, 'UTF-8'); ?>
                                        <?php if (!empty($sig['biome'])): ?>
                                            · <?php echo htmlspecialchars((string) $sig['biome'], ENT_QUOTES, 'UTF-8'); ?>
                                        <?php endif; ?>
                                        · prise ~<?php echo (int) $capPreview; ?>% avec effectif actuel
                                    </span>
                                </div>
                                <?php if ($isActiveSig): ?>
                                    <form method="post">
                                        <input type="hidden" name="action" value="analyze">
                                        <button class="button button--primary button--sm" type="submit" <?php echo $captureReady < 1 ? 'disabled' : ''; ?>>
                                            Tenter (~<?php echo (int) $capPreview; ?>%)
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="post">
                                        <input type="hidden" name="action" value="focus_signal">
                                        <input type="hidden" name="creature_id" value="<?php echo htmlspecialchars($sid, ENT_QUOTES, 'UTF-8'); ?>">
                                        <button class="button button--secondary button--sm" type="submit">Reprendre</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($showWorldMap): ?>
                <div class="zone-picker">
                    <h3 class="zone-picker__title">Terres connues</h3>
                    <div class="zone-picker__list zone-picker__list--map">
                        <?php foreach (($view['zones'] ?? []) as $zone): ?>
                            <?php
                            $zid = (string) ($zone['id'] ?? '');
                            $explored = !empty($zone['explored']);
                            $foundKeys = $zone['species_found'] ?? [];
                            $foundNames = [];
                            if (is_array($foundKeys)) {
                                foreach ($foundKeys as $fk) {
                                    $sp = Catalog::speciesByKey((string) $fk);
                                    if ($sp) {
                                        $foundNames[] = $sp['species'] ?? $fk;
                                    }
                                }
                            }
                            $poolCount = (int) ($zone['species_count'] ?? count($zone['species_pool'] ?? [1]));
                            $reconCost = (int) ($zone['recon_cost'] ?? Economy::reconCost(is_array($zone) ? $zone : []));
                            $canAfford = (int) ($resources['logistics'] ?? 0) >= $reconCost;
                            $layer = (string) ($zone['layer'] ?? 'natal');
                            $biome = (string) ($zone['biome'] ?? '');
                            $biomeSlug = VisualTheme::biomeSlug($biome);
                            $riskSlug = VisualTheme::riskSlug((string) ($zone['risk'] ?? ''));
                            $hover = (string) ($zone['hover'] ?? '');
                            $isNew = !empty($zone['newly_unlocked']);
                            $succCh = (int) ($zone['success_chance'] ?? 0);
                            $specCh = (int) ($zone['specimen_chance'] ?? 0);
                            $affNote = (string) ($zone['affinity'] ?? '');
                            ?>
                            <form method="post" class="zone-card zone-card--<?php echo htmlspecialchars($biomeSlug, ENT_QUOTES, 'UTF-8'); ?> risk--<?php echo htmlspecialchars($riskSlug, ENT_QUOTES, 'UTF-8'); ?> <?php echo $selectedZone === $zid ? 'is-selected' : ''; ?> <?php echo $explored ? 'is-explored' : 'is-unknown'; ?> <?php echo $layer === 'orbital' ? 'is-orbital' : ''; ?> <?php echo $canAfford ? '' : 'is-expensive'; ?> <?php echo $isNew ? 'is-new-zone' : ''; ?>" title="<?php echo htmlspecialchars($hover, ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="action" value="select_zone">
                                <input type="hidden" name="zone_id" value="<?php echo htmlspecialchars($zid, ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="zone-card__button" title="<?php echo htmlspecialchars($hover, ENT_QUOTES, 'UTF-8'); ?>">
                                    <span class="zone-card__sigil"><?php echo VisualTheme::biomeSvg($biome); ?></span>
                                    <span class="zone-card__body">
                                        <span class="zone-card__name">
                                            <?php echo htmlspecialchars($zone['name'] ?? '?', ENT_QUOTES, 'UTF-8'); ?>
                                            <?php if ($isNew): ?><span class="zone-card__tag zone-card__tag--new">NOUVEAU</span><?php endif; ?>
                                            <?php if ($layer === 'orbital'): ?><span class="zone-card__tag">orbite</span><?php endif; ?>
                                        </span>
                                        <span class="zone-card__meta">
                                            <span class="zone-card__biome"><?php echo htmlspecialchars(VisualTheme::biomeLabel($biome), ENT_QUOTES, 'UTF-8'); ?></span>
                                            <span class="zone-card__risk risk-chip risk-chip--<?php echo htmlspecialchars($riskSlug, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string) ($zone['risk'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                                        </span>
                                        <?php $scanPct = (int) ($zone['scan'] ?? ($explored ? 100 : 0)); ?>
                                        <span class="zone-card__signal">
                                            Scan <?php echo $scanPct; ?>%
                                            · ~<?php echo $succCh; ?>% tenue
                                            · ~<?php echo $specCh; ?>% contact
                                            <?php if ($foundNames !== []): ?>
                                                · capturé : <?php echo htmlspecialchars(implode(' · ', $foundNames), ENT_QUOTES, 'UTF-8'); ?>
                                                <?php if ($poolCount > count($foundNames)): ?>
                                                    <em class="zone-card__more">+ possibles</em>
                                                <?php endif; ?>
                                            <?php elseif ($scanPct === 0): ?>
                                                · <em>non lue</em>
                                            <?php else: ?>
                                                · <em>en lecture</em>
                                            <?php endif; ?>
                                        </span>
                                        <?php if ($affNote !== '' && $affNote !== 'Aucune escorte — bonus d’affinité null'): ?>
                                            <span class="zone-card__affinity"><?php echo htmlspecialchars($affNote, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <?php endif; ?>
                                    </span>
                                </button>
                                <div class="zone-card__tooltip" role="tooltip">
                                    <?php foreach (preg_split('/\n+/', $hover) as $hline): ?>
                                        <?php if (trim((string) $hline) !== ''): ?>
                                            <div><?php echo htmlspecialchars((string) $hline, ENT_QUOTES, 'UTF-8'); ?></div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </form>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if (!$showDeploy): ?>
                    <p class="status-banner status-banner--warn">Pose le doigt sur une terre. Ensuite seulement, les sondes s’éveillent.</p>
                <?php else: ?>
                <div class="team-panel deploy-panel" id="team">
                    <h3 class="zone-picker__title">
                        Vers
                        <?php echo htmlspecialchars((string) ($state['world']['zones'][$selectedZone]['name'] ?? $selectedZone), ENT_QUOTES, 'UTF-8'); ?>
                    </h3>
                    <p class="screen-hint screen-hint--lore">Combien de sondes ? Quelle escorte ? Puis lâche-les dans le silence.</p>

                    <form method="post" class="team-form" id="recon-deploy-form">
                        <input type="hidden" name="action" value="set_team">
                        <input type="hidden" name="deploy_mode" value="full">
                        <input type="hidden" name="return_screen" value="world">
                        <?php /* valeur par défaut si on ne change que l’escorte ; un clic ×n l’écrase (bouton après ce champ) */ ?>
                        <input type="hidden" name="drone_count" id="recon-drone-count" value="<?php echo (int) $reconWant; ?>">

                        <div class="force-row">
                            <span class="force-row__label">Sondes d’exploration
                                <em><?php echo $reconReady; ?> prêtes · alloué ×<?php echo $reconWant; ?></em>
                            </span>
                            <div class="force-picks" role="group" aria-label="Nombre de sondes">
                                <?php for ($n = 1; $n <= 5; $n++):
                                    $row = null;
                                    foreach ($reconChanceTable as $r) {
                                        if ((int) ($r['n'] ?? 0) === $n) {
                                            $row = $r;
                                            break;
                                        }
                                    }
                                    $tip = (string) ($row['tooltip'] ?? '');
                                    $ok = $n <= max(0, $reconReady);
                                ?>
                                    <button type="submit" name="drone_count" value="<?php echo $n; ?>"
                                        class="force-pick <?php echo $reconWant === $n ? 'is-on' : ''; ?>"
                                        data-tip="<?php echo htmlspecialchars($tip, ENT_QUOTES, 'UTF-8'); ?>"
                                        <?php echo $ok ? '' : 'disabled'; ?>>
                                        <span class="force-pick__n">×<?php echo $n; ?></span>
                                        <span class="force-pick__pct">~<?php echo (int) ($row['chance'] ?? 0); ?>%</span>
                                    </button>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <label class="deploy-escort">
                            Escorte (bonus si biome adapté)
                            <select name="escort_creature_id" <?php echo $analyzed === [] ? 'disabled' : ''; ?> onchange="this.form.submit()">
                                <option value="none">Aucune</option>
                                <?php foreach ($analyzed as $entry): ?>
                                    <?php
                                    $eid = (string) ($entry['id'] ?? '');
                                    $escSel = (($expedition['escort_creature_id'] ?? '') === $eid) ? 'selected' : '';
                                    $ready = (($entry['status'] ?? 'prête') === 'prête');
                                    $eBiome = (string) ($entry['biome'] ?? '');
                                    ?>
                                    <option value="<?php echo htmlspecialchars($eid, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $escSel; ?> <?php echo $ready ? '' : 'disabled'; ?>>
                                        <?php echo htmlspecialchars(($entry['name'] ?? $entry['species'] ?? $eid), ENT_QUOTES, 'UTF-8'); ?>
                                        <?php if ($eBiome !== ''): ?> · <?php echo htmlspecialchars($eBiome, ENT_QUOTES, 'UTF-8'); ?><?php endif; ?>
                                        <?php if (!$ready): ?> · <?php echo htmlspecialchars((string) ($entry['status'] ?? ''), ENT_QUOTES, 'UTF-8'); ?><?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <?php if ($reconReady < 1): ?>
                            <p class="status-banner status-banner--warn">Aucune sonde prête — <a href="?screen=institute">assembler à l’Institut</a>.</p>
                        <?php endif; ?>
                    </form>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="action-row">
                <?php
                $primaryAction = (string) ($primary['action'] ?? '');
                $primaryLabel = (string) ($primary['label'] ?? '');
                $labCardsOpen = $showCaptureDeploy || $showMutateSection || $showCross;
                // Tâche en cours déjà visible dans le dock (ex. « Mutation contrôlée… »)
                $primaryIsBusy = $tasks !== []
                    || str_ends_with($primaryLabel, '…')
                    || str_ends_with($primaryLabel, '...');
                // Bestiaire / labo avec cartes / busy : pas de CTA redondant ici
                if ($screen === 'bestiary' || ($screen === 'lab' && ($labCardsOpen || $primaryIsBusy))
                    || ($screen === 'institute' && $primaryIsBusy)):
                    // vide (dock affiche déjà la tâche)
                else:
                $primaryIsMutate = $primaryAction === 'mutate'
                    || (($primary['type'] ?? '') === 'choices' && ($primaryAction === 'mutate' || $primaryAction === '') && $showMutateSection);
                $primaryIsStudy = $primaryAction === 'study' && $showDeepenStudy;
                $primaryIsCross = $primaryAction === 'cross' && $showCross;
                $primaryIsCapture = $showCaptureDeploy && in_array($primaryAction, ['analyze', 'capture'], true);
                if (($showMutateSection && $primaryIsMutate) || $primaryIsStudy || $primaryIsCross || $primaryIsCapture):
                    // déjà sur cartes labo
                elseif (($primary['type'] ?? '') === 'choices'): ?>
                    <div class="primary-choices">
                        <p class="panel__text"><?php echo htmlspecialchars((string) ($primary['label'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php if (!empty($primary['hint'])): ?>
                            <p class="risk-line"><?php echo htmlspecialchars((string) $primary['hint'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                        <?php foreach (($primary['choices'] ?? []) as $ck => $cl): ?>
                            <?php
                            $desc = $primary['choice_meta'][$ck]['description'] ?? '';
                            $mCh = (int) ($primary['mutate_chance'] ?? $mutateChance ?? 0);
                            $tip = $mCh > 0
                                ? sprintf('Réussite ~%d%%. Échec = perte définitive du spécimen.', $mCh)
                                : '';
                            ?>
                            <form method="post">
                                <input type="hidden" name="action" value="<?php echo htmlspecialchars((string) ($primary['action'] ?? 'mutate'), ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="mutation_choice" value="<?php echo htmlspecialchars((string) $ck, ENT_QUOTES, 'UTF-8'); ?>">
                                <button class="button button--primary button--block" type="submit" title="<?php echo htmlspecialchars($tip, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars((string) $cl, ENT_QUOTES, 'UTF-8'); ?>
                                    <?php if ($desc !== ''): ?>
                                        <span class="button__hint"><?php echo htmlspecialchars((string) $desc, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php endif; ?>
                                </button>
                            </form>
                        <?php endforeach; ?>
                    </div>
                <?php elseif (($primary['type'] ?? '') === 'post'): ?>
                    <?php if (!empty($primary['hint'])): ?>
                        <p class="risk-line"><?php echo htmlspecialchars((string) $primary['hint'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                    <form method="post">
                        <input type="hidden" name="action" value="<?php echo htmlspecialchars($primary['action'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php if (!empty($primary['drone_type'])): ?>
                            <input type="hidden" name="drone_type" value="<?php echo htmlspecialchars((string) $primary['drone_type'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php endif; ?>
                        <?php if (!empty($primary['zone_id'])): ?>
                            <input type="hidden" name="zone_id" value="<?php echo htmlspecialchars((string) $primary['zone_id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php endif; ?>
                        <?php if (!empty($primary['creature_id'])): ?>
                            <input type="hidden" name="creature_id" value="<?php echo htmlspecialchars((string) $primary['creature_id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php endif; ?>
                        <?php if (!empty($primary['refine_source'])): ?>
                            <input type="hidden" name="refine_source" value="<?php echo htmlspecialchars((string) $primary['refine_source'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php endif; ?>
                        <button class="button button--primary" type="submit" title="<?php echo htmlspecialchars((string) ($primary['hint'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($primary['label'], ENT_QUOTES, 'UTF-8'); ?>
                        </button>
                    </form>
                <?php else: ?>
                    <a class="button button--primary" href="<?php echo htmlspecialchars($primary['href'] ?? '?screen=world', ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($primary['label'] ?? 'Continuer', ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                <?php endif; ?>
                <?php endif; // fin hors-bestiaire ?>
            </div>

            <?php
            $secondaryFiltered = [];
            if ($screen !== 'bestiary') {
                $secondaryFiltered = array_values(array_filter(
                    $secondary,
                    static function (array $sec) use ($screen, $showMutateSection, $showCross, $showStudySection, $secondaryForScreen): bool {
                        if (!$secondaryForScreen($sec, $screen)) {
                            return false;
                        }
                        if ($showMutateSection && (
                            ($sec['action'] ?? '') === 'mutate'
                            || isset($sec['mutate_chance'])
                            || str_starts_with((string) ($sec['label'] ?? ''), 'Muter')
                        )) {
                            return false;
                        }
                        if ($showCross && (
                            ($sec['action'] ?? '') === 'cross'
                            || str_starts_with((string) ($sec['label'] ?? ''), 'Croiser')
                        )) {
                            return false;
                        }
                        if ($showStudySection && (
                            ($sec['action'] ?? '') === 'study'
                            || str_starts_with((string) ($sec['label'] ?? ''), 'Étudier')
                            || str_starts_with((string) ($sec['label'] ?? ''), 'Approfondir')
                        )) {
                            return false;
                        }

                        return true;
                    }
                ));
                if ($screen === 'lab' || $screen === 'institute') {
                    $primaryActionFilter = (string) ($primary['action'] ?? '');
                    $secondaryFiltered = array_values(array_filter(
                        $secondaryFiltered,
                        static function (array $sec) use ($screen, $primaryActionFilter): bool {
                            $a = (string) ($sec['action'] ?? '');
                            $l = (string) ($sec['label'] ?? '');
                            // Jamais de synthèse organique
                            if ($a === 'refine_organic' || str_contains($l, 'Org.') || str_contains($l, 'biomasse (−')) {
                                return false;
                            }
                            // Pas de doublon du CTA principal
                            if ($a !== '' && $a === $primaryActionFilter) {
                                return false;
                            }
                            if ($screen === 'lab') {
                                if (in_array($a, ['mutate', 'cross', 'study', 'recover'], true)) {
                                    return false;
                                }
                                if (str_starts_with($l, 'Muter') || str_starts_with($l, 'Croiser')
                                    || str_starts_with($l, 'Étudier') || str_starts_with($l, 'Approfondir')
                                    || str_starts_with($l, 'Soigner') || str_starts_with($l, 'Restaurer')) {
                                    return false;
                                }
                            }

                            return true;
                        }
                    ));
                }
            }
            ?>
            <?php if ($secondaryFiltered !== []): ?>
                <div class="secondary-actions">
                    <h3 class="secondary-actions__title">Autres voies</h3>
                    <?php foreach ($secondaryFiltered as $sec): ?>
                        <?php if (($sec['type'] ?? '') === 'choices'): ?>
                            <p class="panel__text"><?php echo htmlspecialchars($sec['label'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php if (!empty($sec['hint'])): ?>
                                <p class="risk-line"><?php echo htmlspecialchars($sec['hint'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                            <?php foreach (($sec['choices'] ?? []) as $ck => $cl): ?>
                                <?php
                                $desc = $sec['choice_meta'][$ck]['description'] ?? '';
                                $secMCh = (int) ($sec['mutate_chance'] ?? $mutateChance ?? 0);
                                $secTip = $secMCh > 0
                                    ? sprintf('Réussite ~%d%%. Échec = perte définitive du spécimen.', $secMCh)
                                    : (string) ($sec['hint'] ?? '');
                                ?>
                                <form method="post">
                                    <input type="hidden" name="action" value="mutate">
                                    <input type="hidden" name="mutation_choice" value="<?php echo htmlspecialchars((string) $ck, ENT_QUOTES, 'UTF-8'); ?>">
                                    <button class="button button--secondary button--block" type="submit" title="<?php echo htmlspecialchars($secTip, ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars((string) $cl, ENT_QUOTES, 'UTF-8'); ?>
                                        <?php if ($desc !== ''): ?>
                                            <span class="button__hint"><?php echo htmlspecialchars((string) $desc, ENT_QUOTES, 'UTF-8'); ?><?php if ($secMCh > 0): ?> · risque ~<?php echo 100 - $secMCh; ?>% perte<?php endif; ?></span>
                                        <?php endif; ?>
                                    </button>
                                </form>
                            <?php endforeach; ?>
                        <?php elseif (($sec['type'] ?? '') === 'link'): ?>
                            <a class="button button--secondary" href="<?php echo htmlspecialchars((string) ($sec['href'] ?? '?screen=world'), ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars((string) ($sec['label'] ?? 'Continuer'), ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        <?php elseif (empty($sec['disabled'])): ?>
                            <form method="post">
                                <input type="hidden" name="action" value="<?php echo htmlspecialchars((string) ($sec['action'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                <?php if (!empty($sec['drone_type'])): ?>
                                    <input type="hidden" name="drone_type" value="<?php echo htmlspecialchars((string) $sec['drone_type'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php endif; ?>
                                <?php if (!empty($sec['creature_id'])): ?>
                                    <input type="hidden" name="creature_id" value="<?php echo htmlspecialchars((string) $sec['creature_id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php endif; ?>
                                <?php if (!empty($sec['refine_source'])): ?>
                                    <input type="hidden" name="refine_source" value="<?php echo htmlspecialchars((string) $sec['refine_source'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php endif; ?>
                                <button class="button button--secondary" type="submit">
                                    <?php echo htmlspecialchars((string) ($sec['label'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="button button--secondary" type="button" disabled>
                                <?php echo htmlspecialchars((string) ($sec['label'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </article>
        <?php endif; // hors bestiaire (pas de panneau décision redondant) ?>

        <?php
        // ——— Cartes labo séparées (grille) ———
        $creaturePickLabel = static function (array $entry): string {
            $species = trim((string) ($entry['species'] ?? ''));
            $name = trim((string) ($entry['name'] ?? ''));
            $biome = trim((string) ($entry['biome'] ?? ''));
            if ($name === '' || $name === $species) {
                $base = $species !== '' ? $species : '?';
            } elseif ($species !== '' && str_contains($name, $species)) {
                $base = $name;
            } elseif ($species !== '') {
                $base = $species . ' · ' . $name;
            } else {
                $base = $name;
            }

            return $biome !== '' ? $base . ' (' . $biome . ')' : $base;
        };
        ?>

        <?php if ($showCaptureDeploy): ?>
        <?php
        $capEscortN = count($captureEscortIds);
        $captureSupport = [];
        foreach ($analyzed as $entry) {
            $eid = (string) ($entry['id'] ?? '');
            if ($eid === '' || $eid === $activeId) {
                continue;
            }
            $slot = array_search($eid, $captureEscortIds, true);
            $info = Economy::captureEscortBonus($state, $entry, $slot === false ? 0 : (int) $slot);
            $captureSupport[] = [
                'id' => $eid,
                'entry' => $entry,
                'on' => $slot !== false,
                'bonus' => (int) $info['bonus'],
                'ready' => !empty($info['ready']),
            ];
        }
        ?>
        <article class="panel panel--lab-card panel--lab-capture" id="lab-capture">
            <h2>Capture</h2>
            <div class="lab-card__costs">
                <span class="lab-cost">
                    <em>Filets</em>
                    <strong data-live="capture-count">×<?php echo (int) $captureWant; ?></strong>
                    <small><?php echo (int) $captureReady; ?> prêts</small>
                </span>
                <span class="lab-cost">
                    <em>Appuis</em>
                    <strong data-live="capture-escorts-n" data-max="<?php echo (int) $captureEscortMax; ?>"><?php echo (int) $capEscortN; ?>/<?php echo (int) $captureEscortMax; ?></strong>
                    <small>créatures</small>
                </span>
                <span class="lab-cost lab-cost--highlight">
                    <em>Chance</em>
                    <strong data-live="capture-chance">~<?php echo (int) ($captureChance ?? 0); ?>%</strong>
                </span>
            </div>
            <p class="lab-card__label">Filets déployés</p>
            <form method="post" class="team-form" id="capture-deploy">
                <input type="hidden" name="action" value="set_team">
                <input type="hidden" name="deploy_mode" value="capture">
                <div class="force-picks">
                    <?php for ($n = 1; $n <= 5; $n++):
                        $row = null;
                        foreach ($captureChanceTable as $r) {
                            if ((int) ($r['n'] ?? 0) === $n) {
                                $row = $r;
                                break;
                            }
                        }
                        $tip = (string) ($row['tooltip'] ?? '');
                        $ok = $n <= max(0, $captureReady);
                    ?>
                        <button type="submit" name="capture_count" value="<?php echo $n; ?>"
                            class="force-pick <?php echo $captureWant === $n ? 'is-on' : ''; ?>"
                            data-tip="<?php echo htmlspecialchars($tip, ENT_QUOTES, 'UTF-8'); ?>"
                            <?php echo $ok ? '' : 'disabled'; ?>>
                            <span class="force-pick__n">×<?php echo $n; ?></span>
                            <span class="force-pick__pct">~<?php echo (int) ($row['chance'] ?? 0); ?>%</span>
                        </button>
                    <?php endfor; ?>
                </div>
            </form>

            <p class="lab-card__label">Chair d’appui (max <?php echo (int) $captureEscortMax; ?>)</p>
            <?php if ($captureSupport === []): ?>
                <p class="panel__text">Pas encore d’allié vivant. Prends d’abord une forme — elle pourra en tenir une autre.</p>
            <?php else: ?>
                <div class="capture-support">
                    <?php foreach ($captureSupport as $sup): ?>
                        <?php
                        $se = $sup['entry'];
                        $on = !empty($sup['on']);
                        $ready = !empty($sup['ready']);
                        $full = !$on && $capEscortN >= $captureEscortMax;
                        $label = (string) ($se['name'] ?? $se['species'] ?? '?');
                        $tip = $ready
                            ? sprintf('%s · %s · %s%d%%', $label, (string) ($se['status'] ?? ''), $on ? '' : '+', (int) $sup['bonus'])
                            : $label . ' — pas prête (malus si ajoutée)';
                        ?>
                        <form method="post" class="capture-support__form">
                            <input type="hidden" name="action" value="set_team">
                            <input type="hidden" name="deploy_mode" value="capture">
                            <input type="hidden" name="capture_escort_toggle" value="<?php echo htmlspecialchars((string) $sup['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit"
                                class="capture-support__btn <?php echo $on ? 'is-on' : ''; ?> <?php echo $ready ? '' : 'is-hurt'; ?>"
                                data-tip="<?php echo htmlspecialchars($tip, ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo $full ? 'disabled' : ''; ?>>
                                <span class="capture-support__name"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="capture-support__mod">
                                    <?php echo $ready ? sprintf('%s%d%%', $on ? '' : '+', (int) $sup['bonus']) : '—'; ?>
                                </span>
                            </button>
                        </form>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" class="lab-card__cta" data-soft="0">
                <input type="hidden" name="action" value="analyze">
                <button class="button button--primary button--block" type="submit"
                    data-live="capture-cta"
                    <?php echo $captureReady < 1 ? 'disabled' : ''; ?>
                    data-tip="<?php echo $captureReady < 1
                        ? 'Aucun filet prêt'
                        : sprintf('~%d%% · filets ×%d · appuis ×%d', (int) ($captureChance ?? 0), $captureWant, $capEscortN); ?>">
                    Lancer la prise (~<?php echo (int) ($captureChance ?? 0); ?>%
                    · ×<?php echo (int) $captureWant; ?>
                    <?php if ($capEscortN > 0): ?> · <?php echo (int) $capEscortN; ?> appui<?php echo $capEscortN > 1 ? 's' : ''; ?><?php endif; ?>)
                </button>
            </form>
            <?php if ($captureReady < 1): ?>
                <p class="status-banner status-banner--warn">Plus de filet prêt — assemble-en à l’Institut.</p>
            <?php endif; ?>
        </article>
        <?php endif; ?>

        <?php
        // Lentilles : une seule fois si muter et/ou croiser
        $showLabStudyPicks = ($showMutateSection || $showCross);
        $mutateTaskRunning = false;
        $crossTaskRunning = false;
        foreach ($tasks as $t) {
            $tl = (string) ($t['label'] ?? '');
            $tt = (string) ($t['type'] ?? '');
            if ($tt === 'mutate' || str_contains($tl, 'Mutation')) {
                $mutateTaskRunning = true;
            }
            if ($tt === 'cross' || str_contains($tl, 'Croisement') || str_contains($tl, 'Union')) {
                $crossTaskRunning = true;
            }
        }
        ?>

        <?php if ($showLabStudyPicks): ?>
        <article class="panel panel--lab-card panel--lab-study" id="lab-lenses">
            <h2>Lentilles</h2>
            <p class="screen-hint screen-hint--lore">
                Plus elles scrutent, moins le vivant se brise.
                Alloué <span data-live="study-count">×<?php echo (int) $studyWant; ?></span>
                · <span data-live="study-ready"><?php echo (int) $studyReady; ?> prêtes</span>
                <?php if ($mutateChance !== null): ?> · muter <span data-live="mutate-chance">~<?php echo (int) $mutateChance; ?>%</span><?php endif; ?>
                <?php if ($crossChance !== null): ?> · croiser <span data-live="cross-chance">~<?php echo (int) $crossChance; ?>%</span><?php endif; ?>
            </p>
            <form method="post" class="team-form">
                <input type="hidden" name="action" value="set_team">
                <input type="hidden" name="deploy_mode" value="study">
                <div class="force-picks force-picks--compact">
                    <?php for ($n = 0; $n <= 5; $n++):
                        $row = null;
                        foreach ($studyChanceTable as $r) {
                            if ((int) ($r['n'] ?? 0) === $n) {
                                $row = $r;
                                break;
                            }
                        }
                        $tip = (string) ($row['tooltip'] ?? '');
                        $ok = $n === 0 || $n <= max(0, $studyReady);
                        $mPct = (int) ($row['mutate'] ?? 0);
                        $cPct = (int) ($row['cross'] ?? 0);
                    ?>
                        <button type="submit" name="study_count" value="<?php echo $n; ?>"
                            class="force-pick <?php echo $studyWant === $n ? 'is-on' : ''; ?>"
                            data-tip="<?php echo htmlspecialchars($tip !== '' ? $tip : sprintf('×%d · mut ~%d%% · croix ~%d%%', $n, $mPct, $cPct), ENT_QUOTES, 'UTF-8'); ?>"
                            <?php echo $ok ? '' : 'disabled'; ?>>
                            <span class="force-pick__n">×<?php echo $n; ?></span>
                        </button>
                    <?php endfor; ?>
                </div>
            </form>
            <?php if ($studyReady < 1): ?>
                <p class="panel__text">Aucune lentille prête — forger au Hangar de l’Institut.</p>
            <?php endif; ?>
        </article>
        <?php endif; ?>

        <?php if ($showMutateSection): ?>
        <?php
        $mutOpts = GameQueries::mutationOptions($creature);
        $mCh = (int) ($mutateChance ?? 0);
        ?>
        <article class="panel panel--lab-card panel--lab-mutate" id="lab-mutate">
            <h2>Mutation</h2>
            <div class="lab-card__costs">
                <span class="lab-cost lab-cost--highlight">
                    <em>Prix</em>
                    <strong>−<?php echo (int) $mutateCost; ?> prél.</strong>
                    <small><?php echo (int) $samplesHave; ?> en réserve</small>
                </span>
                <span class="lab-cost">
                    <em>Filet</em>
                    <strong data-live="mutate-chance">~<?php echo $mCh > 0 ? $mCh : '—'; ?>%</strong>
                    <small>lentilles <span data-live="study-count">×<?php echo (int) $studyWant; ?></span> · échec = perte</small>
                </span>
            </div>
            <?php if ($mutateTaskRunning): ?>
                <p class="status-banner status-banner--warn">Le vivant se tord encore…</p>
            <?php elseif ($mutOpts !== [] && $canMutateNow): ?>
                <div class="primary-choices primary-choices--compact">
                    <?php foreach ($mutOpts as $ck => $meta): ?>
                        <?php
                        $cl = (string) ($meta['label'] ?? $ck);
                        // Retirer un éventuel « · ~N% » déjà collé au label
                        $cl = preg_replace('/\s*·\s*~\d+%\s*$/u', '', $cl) ?? $cl;
                        $tip = sprintf('Voie « %s » — ~%d%% · −%d prél. · échec = effacé', $cl, $mCh, $mutateCost);
                        ?>
                        <form method="post">
                            <input type="hidden" name="action" value="mutate">
                            <input type="hidden" name="mutation_choice" value="<?php echo htmlspecialchars((string) $ck, ENT_QUOTES, 'UTF-8'); ?>">
                            <button class="button button--primary button--block" type="submit"
                                data-tip="<?php echo htmlspecialchars($tip, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($cl, ENT_QUOTES, 'UTF-8'); ?>
                            </button>
                        </form>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($mutOpts !== []): ?>
                <p class="status-banner status-banner--warn">
                    Pas assez de prélèvements (<?php echo (int) $samplesHave; ?>/<?php echo (int) $mutateCost; ?>).
                </p>
            <?php else: ?>
                <p class="panel__text">Cette forme n’offre aucune voie de torsion.</p>
            <?php endif; ?>
        </article>
        <?php endif; ?>

        <?php if ($showCross): ?>
        <?php
        $preview = $view['cross_preview'] ?? null;
        $selA = (string) ($view['preview_parent_a'] ?? '');
        $selB = (string) ($view['preview_parent_b'] ?? '');
        ?>
        <article class="panel panel--lab-card panel--lab-cross" id="lab-cross">
            <h2>Croisement</h2>
            <div class="lab-card__costs">
                <span class="lab-cost lab-cost--highlight">
                    <em>Prix</em>
                    <strong>−<?php echo (int) $crossCost; ?> org.</strong>
                    <small><?php echo (int) $organicHave; ?> en réserve</small>
                </span>
                <span class="lab-cost">
                    <em>Filet</em>
                    <strong data-live="cross-chance">~<?php echo $crossChance !== null ? (int) $crossChance : '—'; ?>%</strong>
                    <small>lentilles <span data-live="study-count">×<?php echo (int) $studyWant; ?></span> · échec = un parent meurt</small>
                </span>
            </div>
            <?php if ($crossTaskRunning): ?>
                <p class="status-banner status-banner--warn">Deux sangs cherchent encore à s’unir…</p>
            <?php endif; ?>

            <form method="get" class="cross-form">
                <input type="hidden" name="screen" value="lab">
                <label>
                    Parent A
                    <select name="parent_a" onchange="this.form.submit()">
                        <?php foreach ($analyzed as $entry): ?>
                            <?php $eid = (string) ($entry['id'] ?? ''); ?>
                            <option value="<?php echo htmlspecialchars($eid, ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo $selA === $eid ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($creaturePickLabel($entry), ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    Parent B
                    <select name="parent_b" onchange="this.form.submit()">
                        <?php foreach ($analyzed as $entry): ?>
                            <?php $eid = (string) ($entry['id'] ?? ''); ?>
                            <option value="<?php echo htmlspecialchars($eid, ENT_QUOTES, 'UTF-8'); ?>"
                                <?php echo $selB === $eid ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($creaturePickLabel($entry), ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <noscript><button class="button button--secondary" type="submit">Aperçu</button></noscript>
            </form>

            <?php if (is_array($preview)): ?>
                <div class="hybrid-preview hybrid-preview--compact">
                    <?php if (!empty($preview['synergy_title'])): ?>
                        <div class="hybrid-preview__synergy"><?php echo htmlspecialchars((string) $preview['synergy_title'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                    <div class="hybrid-preview__name"><?php echo htmlspecialchars((string) $preview['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="hybrid-preview__stats">
                        F<?php echo (int) $preview['stats']['force']; ?>
                        · V<?php echo (int) $preview['stats']['vitesse']; ?>
                        · R<?php echo (int) $preview['stats']['resistance']; ?>
                        · I<?php echo (int) $preview['stats']['intelligence']; ?>
                        · <?php echo (int) $preview['purity']; ?>%
                    </div>
                    <?php if (!empty($preview['abilities'])): ?>
                        <div class="hybrid-preview__abilities">
                            <?php echo htmlspecialchars(implode(' · ', $preview['abilities'] ?? []), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!$crossTaskRunning): ?>
            <form method="post" class="cross-form lab-card__cta">
                <input type="hidden" name="action" value="cross">
                <input type="hidden" name="parent_a" value="<?php echo htmlspecialchars($selA !== '' ? $selA : (string) ($analyzed[0]['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="parent_b" value="<?php echo htmlspecialchars($selB !== '' ? $selB : (string) ($analyzed[1]['id'] ?? $analyzed[0]['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                <button class="button button--primary button--block" type="submit" <?php echo $canCrossNow ? '' : 'disabled'; ?>
                    data-tip="<?php echo $canCrossNow
                        ? sprintf('−%d org. · lentilles ×%d · ~%d%%', $crossCost, $studyWant, (int) ($crossChance ?? 0))
                        : 'Il faut de l’organique — une mission réussie en apporte.'; ?>">
                    <?php echo $canCrossNow
                        ? 'Unir ces deux formes'
                        : sprintf('Organique manquant (%d/%d)', $organicHave, $crossCost); ?>
                </button>
            </form>
            <?php endif; ?>
        </article>
        <?php endif; ?>

        <?php if ($showSpecimenCompact): ?>
        <article class="panel panel--specimen-compact">
            <h2><?php echo !empty($creature['analyzed']) ? 'Spécimen' : 'Signal capté'; ?></h2>
            <div class="specimen-compact">
                <?php echo VisualTheme::creaturePortrait($creature); ?>
                <div>
                    <strong><?php echo htmlspecialchars(CreatureFactory::displayName($creature), ENT_QUOTES, 'UTF-8'); ?></strong>
                    <span>
                        <?php if (!empty($creature['analyzed'])): ?>
                            <?php echo htmlspecialchars(CreatureFactory::displaySpecies($creature), ENT_QUOTES, 'UTF-8'); ?>
                            · <?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>
                            · <?php echo CreatureFactory::knowledge($creature); ?>% connu
                        <?php else: ?>
                            Non encore pris — capture risquée au laboratoire
                            <?php if ($captureChance !== null): ?>
                                · ~<?php echo (int) $captureChance; ?>%
                            <?php endif; ?>
                        <?php endif; ?>
                    </span>
                    <div class="specimen-compact__actions">
                        <?php if (!empty($creature['analyzed'])): ?>
                            <a class="button button--ghost" href="?screen=bestiary">Bestiaire</a>
                            <a class="button button--ghost" href="?screen=lab">Laboratoire</a>
                        <?php else: ?>
                            <a class="button button--ghost" href="?screen=lab">Tenter la capture</a>
                        <?php endif; ?>
                        <?php if (GameQueries::needsRecovery($creature) && !empty($creature['analyzed'])): ?>
                            <?php
                            $healCostC = GameQueries::recoverCost($creature);
                            $canHealC = (int) ($resources['organic'] ?? 0) >= $healCostC;
                            ?>
                            <form method="post" class="heal-form">
                                <input type="hidden" name="action" value="recover">
                                <button class="button button--primary button--sm" type="submit" <?php echo $canHealC ? '' : 'disabled'; ?>>
                                    <?php echo ($status === 'blessée' ? 'Soigner' : 'Restaurer'); ?>
                                    (−<?php echo $healCostC; ?> org.)
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </article>
        <?php endif; ?>

        <?php if ($showSpecimenFull): ?>
        <?php
        $cBiome = (string) ($creature['biome'] ?? '');
        $cBiomeSlug = VisualTheme::biomeSlug($cBiome);
        $cRarity = VisualTheme::raritySlug((string) ($creature['rarity'] ?? ''));
        $analyzedOk = !empty($creature['analyzed']);
        $displayName = (string) ($creature['name'] ?? '—');
        $speciesName = (string) ($creature['species'] ?? '—');
        $tipPurity = VisualTheme::traitHelp('purity');
        $tipBiome = VisualTheme::traitHelp('biome');
        $tipRarity = VisualTheme::traitHelp('rarity');
        $tipStatus = VisualTheme::traitHelp('status');
        $tipAbility = VisualTheme::traitHelp('ability');
        ?>
        <article class="panel panel--specimen panel--focus biome-panel--<?php echo htmlspecialchars($cBiomeSlug, ENT_QUOTES, 'UTF-8'); ?>">
            <h2><?php echo $screen === 'bestiary' ? 'Sous le regard' : 'Spécimen'; ?></h2>
            <?php
            $vis = CreatureFactory::visibility($creature);
            $k = CreatureFactory::knowledge($creature);
            ?>
            <div class="specimen-card">
                <div class="specimen-card__portrait">
                    <?php echo VisualTheme::creaturePortrait($creature); ?>
                </div>
                <div class="specimen-card__info">
                    <div class="specimen-card__name"><?php echo htmlspecialchars(CreatureFactory::displayName($creature), ENT_QUOTES, 'UTF-8'); ?></div>

                    <?php if (!$analyzedOk): ?>
                        <p class="specimen-card__subtitle">
                            Signal vivant — pas encore en main
                            <?php if ($captureChance !== null): ?>
                                · prise ~<?php echo (int) $captureChance; ?>%
                            <?php endif; ?>
                        </p>
                        <p class="panel__text">Échec de capture : perte possible d’un drone de prise et de ressources.</p>
                    <?php else: ?>
                        <p class="specimen-card__subtitle">
                            <?php echo htmlspecialchars(CreatureFactory::displaySpecies($creature), ENT_QUOTES, 'UTF-8'); ?>
                            <?php if (!empty($creature['is_hybrid']) && !empty($creature['parents'])): ?>
                                · <span class="has-tip" title="<?php echo htmlspecialchars(VisualTheme::traitHelp('hybrid'), ENT_QUOTES, 'UTF-8'); ?>">hybride</span>
                            <?php endif; ?>
                            · <span class="has-tip" title="<?php echo htmlspecialchars(VisualTheme::traitHelp('knowledge'), ENT_QUOTES, 'UTF-8'); ?>"><?php echo $k; ?>% connu</span>
                        </p>

                        <dl class="specimen-meta">
                            <?php if (!empty($vis['biome'])): ?>
                            <div class="specimen-meta__row has-tip" title="<?php echo htmlspecialchars($tipBiome, ENT_QUOTES, 'UTF-8'); ?>">
                                <dt>Biome</dt>
                                <dd class="chip chip--biome chip-biome--<?php echo htmlspecialchars($cBiomeSlug, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(VisualTheme::biomeLabel($cBiome), ENT_QUOTES, 'UTF-8'); ?></dd>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($vis['rarity'])): ?>
                            <div class="specimen-meta__row has-tip" title="<?php echo htmlspecialchars($tipRarity, ENT_QUOTES, 'UTF-8'); ?>">
                                <dt>Rareté</dt>
                                <dd class="chip-rarity chip-rarity--<?php echo htmlspecialchars($cRarity, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string) ($creature['rarity'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></dd>
                            </div>
                            <?php else: ?>
                            <div class="specimen-meta__row"><dt>Rareté</dt><dd>???</dd></div>
                            <?php endif; ?>
                            <div class="specimen-meta__row has-tip" title="<?php echo htmlspecialchars($tipStatus, ENT_QUOTES, 'UTF-8'); ?>">
                                <dt>Statut</dt>
                                <dd class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></dd>
                            </div>
                            <?php if (!empty($vis['purity'])): ?>
                            <div class="specimen-meta__row has-tip" title="<?php echo htmlspecialchars($tipPurity, ENT_QUOTES, 'UTF-8'); ?>">
                                <dt>Pureté</dt>
                                <dd><?php echo (int) ($creature['purity'] ?? 100); ?>%</dd>
                            </div>
                            <?php else: ?>
                            <div class="specimen-meta__row has-tip" title="<?php echo htmlspecialchars($tipPurity, ENT_QUOTES, 'UTF-8'); ?>"><dt>Pureté</dt><dd>???</dd></div>
                            <?php endif; ?>
                        </dl>

                        <?php if (!empty($vis['synergy']) && !empty($creature['synergy_title'])): ?>
                            <p class="specimen-card__synergy"><?php echo htmlspecialchars((string) $creature['synergy_title'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>

                        <div class="specimen-stats">
                            <div class="specimen-stats__title">Caractéristiques</div>
                            <?php if (!empty($vis['stats'])): ?>
                                <?php echo VisualTheme::statBars($stats); ?>
                            <?php else: ?>
                                <p class="panel__text">Encore voilées. Le regard n’a pas fini de creuser.</p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($vis['abilities']) && !empty($creature['abilities'])): ?>
                            <div class="specimen-abilities">
                                <div class="specimen-stats__title has-tip" title="<?php echo htmlspecialchars($tipAbility, ENT_QUOTES, 'UTF-8'); ?>">Capacité</div>
                                <ul class="specimen-abilities__list">
                                    <?php foreach ($creature['abilities'] as $ab): ?>
                                        <li class="has-tip" title="<?php echo htmlspecialchars($tipAbility, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string) $ab, ENT_QUOTES, 'UTF-8'); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php elseif ($analyzedOk): ?>
                            <p class="panel__text">Ses pouvoirs dorment encore (<?php echo $k; ?>%).</p>
                        <?php endif; ?>

                        <?php if ($screen === 'bestiary' && $analyzedOk && $k < 100 && !empty($features['analyze'])): ?>
                            <?php
                            $studyRunning = false;
                            foreach ($tasks as $t) {
                                if (($t['type'] ?? '') === 'study' || str_contains((string) ($t['label'] ?? ''), 'Étude')) {
                                    $studyRunning = true;
                                    break;
                                }
                            }
                            ?>
                            <?php if ($studyRunning): ?>
                                <p class="status-banner status-banner--warn">Le regard travaille encore…</p>
                            <?php else: ?>
                                <form method="post" class="lab-card__cta">
                                    <input type="hidden" name="action" value="study">
                                    <button class="button button--primary button--block" type="submit">
                                        Creuser la lecture (<?php echo (int) $k; ?>%)
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (GameQueries::needsRecovery($creature)): ?>
                        <?php
                        $healCost = GameQueries::recoverCost($creature);
                        $organicHave = (int) ($resources['organic'] ?? 0);
                        $canHeal = $organicHave >= $healCost;
                        ?>
                        <div class="status-banner status-banner--<?php echo $status === 'blessée' ? 'danger' : 'warn'; ?> status-banner--heal">
                            <p>
                                <?php
                                echo $status === 'blessée'
                                    ? sprintf('Il saigne. −%d organique pour le ramener.', $healCost)
                                    : sprintf('Il ne tient plus. −%d organique pour le relever.', $healCost);
                                ?>
                            </p>
                            <form method="post" class="heal-form">
                                <input type="hidden" name="action" value="recover">
                                <button class="button button--primary button--sm" type="submit" <?php echo $canHeal ? '' : 'disabled'; ?>>
                                    <?php echo $status === 'blessée'
                                        ? sprintf('Soigner (−%d org.)', $healCost)
                                        : sprintf('Restaurer (−%d org.)', $healCost); ?>
                                </button>
                            </form>
                            <?php if (!$canHeal): ?>
                                <p class="panel__text">L’essence manque. Ramène-en du terrain.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </article>
        <?php endif; ?>

        <?php if ($showBestiary): ?>
        <article class="panel panel--bestiary-list panel--wide">
            <h2>Formes captives</h2>
            <p class="screen-hint screen-hint--lore">Pour tordre ou unir — le <a href="?screen=lab">laboratoire</a>.</p>
            <?php if ($analyzed === []): ?>
                <p class="panel__text">Vide. Le berceau n’a encore rien cédé.</p>
            <?php else: ?>
            <div class="bestiary-list bestiary-list--cards">
                <?php foreach ($analyzed as $entry): ?>
                    <?php
                    $eid = (string) ($entry['id'] ?? '');
                    $eBiome = VisualTheme::biomeSlug((string) ($entry['biome'] ?? ''));
                    $eRarity = VisualTheme::raritySlug((string) ($entry['rarity'] ?? ''));
                    $eStatus = (string) ($entry['status'] ?? 'prête');
                    $eKnow = CreatureFactory::knowledge($entry);
                    $needsHeal = in_array($eStatus, ['blessée', 'épuisée'], true);
                    $isActiveE = $eid === $activeId;
                    ?>
                    <div class="bestiary-item bestiary-item--card biome-panel--<?php echo htmlspecialchars($eBiome, ENT_QUOTES, 'UTF-8'); ?> <?php echo $isActiveE ? 'is-active' : ''; ?>">
                        <div class="bestiary-item__portrait">
                            <?php echo VisualTheme::creaturePortrait($entry); ?>
                        </div>
                        <div class="bestiary-item__body">
                            <strong><?php echo htmlspecialchars($entry['name'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></strong>
                            <span>
                                <?php echo htmlspecialchars($entry['species'] ?? '—', ENT_QUOTES, 'UTF-8'); ?>
                                · <span class="chip-rarity chip-rarity--<?php echo htmlspecialchars($eRarity, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars((string) ($entry['rarity'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                                · <?php echo htmlspecialchars($eStatus, ENT_QUOTES, 'UTF-8'); ?>
                                · <?php echo (int) $eKnow; ?>%
                                <?php if (!empty($entry['biome'])): ?> · <?php echo htmlspecialchars((string) $entry['biome'], ENT_QUOTES, 'UTF-8'); ?><?php endif; ?>
                                <?php if (!empty($entry['is_hybrid'])): ?> · hybride<?php endif; ?>
                            </span>
                            <?php if ($needsHeal): ?>
                                <span class="chip chip--danger">Soin requis</span>
                            <?php endif; ?>
                        </div>
                        <div class="bestiary-item__actions">
                            <?php if ($isActiveE): ?>
                                <span class="chip chip--ok">Active</span>
                            <?php else: ?>
                                <form method="post">
                                    <input type="hidden" name="action" value="focus_creature">
                                    <input type="hidden" name="creature_id" value="<?php echo htmlspecialchars($eid, ENT_QUOTES, 'UTF-8'); ?>">
                                    <button class="button button--ghost button--sm" type="submit">Activer</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </article>
        <?php endif; ?>

        <?php if ($screen === 'field' && $showMissionRisk): ?>
        <article class="panel panel--mission-guide panel--side">
            <h2>Avant le seuil</h2>
            <p class="screen-hint screen-hint--lore">
                <?php echo htmlspecialchars((string) $missionRisk, ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <p class="panel__text">
                Il part seul. S’il revient intact, l’organique suit.
                S’il rentre brisé — le <a href="?screen=bestiary">bestiaire</a> le remet debout… ou non.
            </p>
        </article>
        <?php endif; ?>

        <?php if ($showFleet): ?>
        <article class="panel panel--fleet panel--side">
            <h2>Hangar</h2>
            <p class="screen-hint screen-hint--lore">
                Ce qui vole encore. Assemble ici, envoie depuis l’<a href="?screen=world">horizon</a>.
            </p>
            <div class="fleet-board fleet-board--side">
                <?php foreach ($droneSummary as $row): ?>
                    <?php
                    $rtype = (string) ($row['type'] ?? '');
                    $recipeMeta = $droneRecipes[$rtype] ?? [];
                    $typeUnlocked = !empty($recipeMeta['unlocked']) || (int) ($row['total'] ?? 0) > 0;
                    if (!$typeUnlocked) {
                        continue;
                    }
                    if ($rtype === 'orbital' && !$baieUnlocked && empty($features['baie']) && empty($recipeMeta['unlocked'])) {
                        continue;
                    }
                    ?>
                    <div class="fleet-card fleet-card--<?php echo htmlspecialchars($rtype, ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="fleet-card__label"><?php echo htmlspecialchars((string) ($row['label'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="fleet-card__count">
                            <strong><?php echo (int) ($row['ready'] ?? 0); ?></strong>
                            <span>/ <?php echo (int) ($row['total'] ?? 0); ?></span>
                        </div>
                        <div class="fleet-card__meta">veille / total</div>
                        <?php if ((int) ($row['busy'] ?? 0) > 0): ?>
                            <div class="fleet-card__busy"><?php echo (int) $row['busy']; ?> dehors</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (!empty($features['craft'])): ?>
                <div class="craft-inline">
                    <span class="secondary-actions__title">Forger</span>
                    <div class="craft-inline__list">
                    <?php foreach ($droneRecipes as $dtype => $recipe): ?>
                        <?php
                        $unlocked = !empty($recipe['unlocked']);
                        $needsBaie = !empty($recipe['requires_baie']) && !$baieUnlocked;
                        $canBuild = $unlocked && !$needsBaie;
                        $label = (string) ($recipe['label'] ?? $dtype);
                        ?>
                        <?php if ($canBuild): ?>
                            <form method="post" class="craft-inline__form">
                                <input type="hidden" name="action" value="craft_drone">
                                <input type="hidden" name="drone_type" value="<?php echo htmlspecialchars((string) $dtype, ENT_QUOTES, 'UTF-8'); ?>">
                                <button class="button button--secondary button--sm button--craft" type="submit"
                                    data-tip="<?php echo htmlspecialchars((string) ($recipe['desc'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                    + <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                                    <span class="button__cost">−<?php echo (int) ($recipe['biomass'] ?? 0); ?> bio · −<?php echo (int) ($recipe['logistics'] ?? 0); ?> log</span>
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="craft-locked"
                                 data-tip="<?php echo htmlspecialchars(
                                     $needsBaie
                                         ? 'Spatio-port requis (fin de carte + MVE).'
                                         : (string) ($recipe['lock_hint'] ?? 'Encore verrouillé'),
                                     ENT_QUOTES,
                                     'UTF-8'
                                 ); ?>">
                                <span class="craft-locked__label"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></span>
                                <span class="craft-locked__hint">
                                    <?php
                                    echo $needsBaie
                                        ? 'Spatio-port requis'
                                        : htmlspecialchars((string) ($recipe['lock_hint'] ?? 'Verrouillé'), ENT_QUOTES, 'UTF-8');
                                    ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </article>
        <?php endif; ?>

        <?php if ($showMissionRisk): ?>
        <article class="panel">
            <h2>Risque de mission</h2>
            <p class="panel__lead"><?php echo htmlspecialchars((string) $missionRisk, ENT_QUOTES, 'UTF-8'); ?></p>
        </article>
        <?php endif; ?>

        <?php if ($showReport): ?>
        <article class="panel panel--report panel--wide">
            <div class="report-header">
                <h2>Dernier rapport</h2>
                <span class="report-header__stamp">Institut · Aster-0</span>
            </div>
            <pre class="report-block"><?php echo htmlspecialchars((string) $lastReport, ENT_QUOTES, 'UTF-8'); ?></pre>
        </article>
        <?php endif; ?>

        <?php if ($showMve): ?>
        <?php
        $phase2 = $view['phase2'] ?? ($mve['orbital'] ?? []);
        $deepSignal = !empty($view['deep_space_signal']);
        ?>
        <article class="panel panel--mve panel--side<?php echo !empty($mve['complete']) ? ' panel--mve-ready' : ''; ?>">
            <h2>Spatio-port</h2>
            <?php if (!$baieUnlocked): ?>
                <p class="screen-hint screen-hint--lore">
                    Seuil de fierté — pas un trophée. Cartographie + patrimoine digne, puis le ciel s’ouvre.
                </p>
                <p class="panel__lead"><?php echo htmlspecialchars((string) ($mve['score'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="mve-checks">
                    <?php foreach ($mve['checks'] ?? [] as $check): ?>
                        <div class="mve-check <?php echo !empty($check['done']) ? 'is-done' : ''; ?>">
                            <span class="mve-check__mark"><?php echo !empty($check['done']) ? '✓' : '·'; ?></span>
                            <span>
                                <strong><?php echo htmlspecialchars((string) ($check['label'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></strong>
                                <em><?php echo htmlspecialchars((string) ($check['detail'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></em>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (!empty($mve['ready']) && !empty($features['baie'])): ?>
                    <div class="status-banner status-banner--ok">Berceau digne. Ouvre — si tu l’oses.</div>
                    <form method="post" class="action-row">
                        <input type="hidden" name="action" value="open_baie">
                        <button class="button button--primary button--block" type="submit">Ouvrir le spatio-port</button>
                    </form>
                <?php elseif (!empty($mve['phrase'])): ?>
                    <p class="panel__text"><?php echo htmlspecialchars((string) $mve['phrase'], ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
            <?php else: ?>
                <p class="panel__lead">
                    Orbites
                    <?php echo (int) ($phase2['done'] ?? 0); ?>/<?php echo (int) ($phase2['total'] ?? 0); ?>
                    · ouvertes <?php echo (int) ($phase2['unlocked'] ?? 0); ?>
                </p>
                <?php if ($deepSignal): ?>
                    <div class="status-banner status-banner--ok">Signal lointain — d’autres mondes existent. Galaxie : plus tard.</div>
                <?php else: ?>
                    <p class="panel__text">Chaque orbite en révèle une autre. Les sondes extra-planétaires sont requises.</p>
                <?php endif; ?>
            <?php endif; ?>
        </article>
        <?php endif; ?>

        <?php if ($showSynergies): ?>
        <article class="panel">
            <h2>Synergies</h2>
            <div class="status-list status-list--compact">
                <?php foreach (array_reverse($synergiesLog) as $syn): ?>
                    <div>
                        <strong><?php echo htmlspecialchars((string) ($syn['title'] ?? 'Synergie'), ENT_QUOTES, 'UTF-8'); ?></strong>
                        — <?php echo htmlspecialchars((string) ($syn['hybrid'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </article>
        <?php endif; ?>

        <?php if ($showMemory): ?>
        <?php
        $mjEntries = $memoryJournal['entries'] ?? [];
        $mjTotal = (int) ($memoryJournal['total'] ?? count($mjEntries));
        $mjHighlight = $memoryJournal['highlight'] ?? null;
        ?>
        <article class="panel panel--memory panel--side" id="memoire">
            <h2>Mémoire</h2>
            <p class="screen-hint screen-hint--lore">
                Ce que le berceau a retenu — <?php echo $mjTotal; ?> trace<?php echo $mjTotal > 1 ? 's' : ''; ?>.
            </p>
            <?php if (is_array($mjHighlight) && !empty($mjHighlight['text'])): ?>
                <div class="memory-highlight memory-highlight--<?php echo htmlspecialchars((string) ($mjHighlight['tone'] ?? 'muted'), ENT_QUOTES, 'UTF-8'); ?>">
                    <span class="memory-tag memory-tag--<?php echo htmlspecialchars((string) ($mjHighlight['tone'] ?? 'muted'), ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars((string) ($mjHighlight['label'] ?? 'Note'), ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <p><?php echo htmlspecialchars((string) $mjHighlight['text'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            <?php endif; ?>
            <ol class="memory-timeline">
                <?php foreach ($mjEntries as $i => $entry): ?>
                    <?php if ($i === 0) {
                        continue;
                    } // highlight déjà affiché ?>
                    <li class="memory-item memory-item--<?php echo htmlspecialchars((string) ($entry['tone'] ?? 'muted'), ENT_QUOTES, 'UTF-8'); ?>">
                        <span class="memory-tag memory-tag--<?php echo htmlspecialchars((string) ($entry['tone'] ?? 'muted'), ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars((string) ($entry['label'] ?? 'Note'), ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                        <span class="memory-item__text"><?php echo htmlspecialchars((string) ($entry['text'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></span>
                    </li>
                <?php endforeach; ?>
            </ol>
            <?php if ($mjTotal > count($mjEntries)): ?>
                <p class="panel__text panel__text--mt">… et <?php echo $mjTotal - count($mjEntries); ?> plus anciennes, archivées.</p>
            <?php endif; ?>
        </article>
        <?php endif; ?>

        <?php if ($showMuseumPanel && $exhibits !== []): ?>
        <article class="panel panel--accent">
            <h2>Collection</h2>
            <ul class="exhibit-list">
                <?php foreach ($exhibits as $ex): ?>
                    <li class="exhibit-card">
                        <div class="exhibit-card__label"><?php echo htmlspecialchars((string) ($ex['label'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="exhibit-card__meta">
                            <?php echo htmlspecialchars((string) ($ex['species'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                            · <?php echo htmlspecialchars((string) ($ex['rarity'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </article>
        <?php elseif ($showMuseumPanel): ?>
        <article class="panel panel--accent">
            <h2>Collection</h2>
            <p class="panel__text">Encore vide. Exposez une création digne de mémoire.</p>
        </article>
        <?php endif; ?>

        <?php if ($showArchives): ?>
        <article class="panel panel--archives panel--side">
            <h2>Archives de l’Institut</h2>
            <?php
            $dbDriver = (string) ($view['db_driver'] ?? '—');
            $dbGameId = (int) ($view['db_game_id'] ?? 0);
            ?>
            <p class="panel__text panel__text--mt">
                Mémoire <?php echo $dbDriver === 'mysql' ? 'MySQL' : ($dbDriver === 'sqlite' ? 'locale' : 'session'); ?>
                <?php if ($dbGameId > 0): ?> · partie #<?php echo $dbGameId; ?><?php endif; ?>
            </p>
            <form method="post" class="save-form">
                <input type="hidden" name="action" value="save_game">
                <input type="text" name="save_label" placeholder="Nom d’archive (optionnel)" maxlength="80">
                <button class="button button--secondary" type="submit">Archiver la partie</button>
            </form>
            <?php if ($saves !== []): ?>
                <div class="save-list">
                    <?php foreach ($saves as $save): ?>
                        <div class="save-item">
                            <div class="bestiary-item__body">
                                <strong><?php echo htmlspecialchars((string) ($save['label'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></strong>
                                <span>
                                    <?php echo htmlspecialchars((string) ($save['saved_at'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                    · <?php echo (int) ($save['species'] ?? 0); ?> espèce(s)
                                    <?php if (!empty($save['is_active'])): ?> · active<?php endif; ?>
                                </span>
                            </div>
                            <div class="save-item__actions">
                                <form method="post">
                                    <input type="hidden" name="action" value="load_game">
                                    <input type="hidden" name="save_id" value="<?php echo htmlspecialchars((string) ($save['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                    <button class="button button--ghost" type="submit">Reprendre</button>
                                </form>
                                <form method="post" data-confirm="Effacer cette archive ?">
                                    <input type="hidden" name="action" value="delete_save">
                                    <input type="hidden" name="save_id" value="<?php echo htmlspecialchars((string) ($save['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                    <button class="button button--ghost" type="submit">Effacer</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form method="post" class="reset-quiet" data-confirm="Recommencer depuis le début ?">
                <input type="hidden" name="action" value="reset">
                <button class="button button--ghost" type="submit">Nouvelle partie</button>
            </form>
        </article>
        <?php endif; ?>
    </section>
</main>
<script src="assets/genesis-audio.js?v=aethel-33" defer></script>
<script src="assets/genesis-ui.js?v=aethel-33" defer></script>
</body>
</html>
