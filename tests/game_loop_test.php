<?php

declare(strict_types=1);

putenv('GENESIS_INSTANT=1');
$_ENV['GENESIS_INSTANT'] = '1';
define('GENESIS_INSTANT', true);

require dirname(__DIR__) . '/src/bootstrap.php';

use Genesis\Game\GameEngine;
use Genesis\Game\GameQueries;
use Genesis\Game\IntentResolver;
use Genesis\Game\StateFactory;
use Genesis\Game\MveEvaluator;
use Genesis\Game\Tutorial;
use Genesis\Game\Economy;

function assertTrue(bool $ok, string $msg): void
{
    if (!$ok) {
        throw new RuntimeException($msg);
    }
}

/** Débloque le tutoriel pour les tests de mécanique pure. */
function freePlay(array &$state): void
{
    Tutorial::skip($state);
    // Débloque toute la carte natale pour tests bulk
    foreach ($state['world']['zones'] as $id => $z) {
        if (($z['layer'] ?? 'natal') === 'natal') {
            $state['world']['zones'][$id]['unlocked'] = true;
            $state['world']['zones'][$id]['locked'] = false;
        }
    }
}

session_id('genesis-game-' . bin2hex(random_bytes(4)));
session_start();
$_SESSION = [];

// ========== Fresh start + progressive zones ==========
$state = StateFactory::initial();
assertTrue(($state['resources']['logistics'] ?? 0) >= 3, 'Start with logistics for multi-zone science.');
assertTrue(isset($state['world']['zones']['plaine']), 'Zones loaded.');
assertTrue(!empty($state['world']['zones']['plaine']['unlocked']), 'Plaine starter unlocked.');
assertTrue(empty($state['world']['zones']['crete']['unlocked']), 'Crete locked at start.');
assertTrue(empty($state['world']['zones']['orbite_a']['unlocked']), 'Orbital locked at start.');
assertTrue(!empty($state['tutorial']['active']), 'Tutorial active on new game.');

// Intent at start: intro tutoriel puis exploration
$primary = IntentResolver::primary($state);
assertTrue(
    ($primary['action'] ?? '') === 'continue_tutorial'
    || ($primary['action'] ?? '') === 'recon'
    || str_contains((string) ($primary['href'] ?? ''), 'world'),
    'Start intent should push tutorial intro or exploration.'
);
GameEngine::apply($state, 'continue_tutorial');
assertTrue(($state['tutorial']['step'] ?? '') === Tutorial::STEP_RECON, 'Intro advances to first recon step.');

// Locked zone reject
GameEngine::apply($state, 'select_zone', ['zone_id' => 'crete']);
assertTrue(($state['world']['selected_zone'] ?? '') === 'plaine', 'Cannot select locked crete; stay on plaine.');

// Loop: plaine → discover → analyze
GameEngine::apply($state, 'select_zone', ['zone_id' => 'plaine']);
GameEngine::apply($state, 'recon');
assertTrue(!empty($state['creature']['discovered']), 'Recon discovers signal.');
assertTrue(($state['creature']['species_key'] ?? '') === 'thermidé', 'Plaine = Thermidé.');
assertTrue(!empty($state['world']['zones']['crete']['unlocked']), 'Crete unlocked after plaine recon.');
assertTrue(!empty($state['world']['zones']['marais']['unlocked']), 'Marais unlocked after plaine recon.');

GameEngine::apply($state, 'analyze');
assertTrue($state['creature']['analyzed'] === true, 'Analyze completes.');
assertTrue($state['creature']['species'] === 'Thermidé', 'Species revealed.');
assertTrue(($state['tutorial']['step'] ?? '') === Tutorial::STEP_ZONE2, 'Tutorial advances to second zone.');

// After 1 species: no mutate primary, no cross
$primary = IntentResolver::primary($state);
assertTrue(($primary['action'] ?? '') !== 'mutate', 'Mutation must not be primary after first analysis.');
assertTrue(($primary['action'] ?? '') !== 'cross', 'Cross locked by tutorial after 1 species.');
assertTrue(!Tutorial::can($state, 'mutate'), 'Mutate feature still locked.');
assertTrue(!Tutorial::can($state, 'cross'), 'Cross feature still locked.');

// Farm yield after first analyze
$bioBefore = (int) ($state['resources']['biomass'] ?? 0);
GameEngine::apply($state, 'farm');
$bioAfter = (int) ($state['resources']['biomass'] ?? 0);
$expectedYield = Economy::farmBiomassYield($state);
assertTrue($bioAfter === $bioBefore + $expectedYield, sprintf(
    'Farm must increase biomass by %d (%d → %d).',
    $expectedYield,
    $bioBefore,
    $bioAfter
));

// Zone B → second species (now unlocked)
GameEngine::apply($state, 'select_zone', ['zone_id' => 'crete']);
GameEngine::apply($state, 'recon');
GameEngine::apply($state, 'analyze');
assertTrue($state['creature']['species'] === 'Salinide', 'Second species Salinide.');
assertTrue(count(GameQueries::analyzedCreatures($state)) >= 2, 'Two analyzed species.');
assertTrue(($state['tutorial']['step'] ?? '') === Tutorial::STEP_MUTATE, 'Tutorial asks for mutate.');
assertTrue(Tutorial::can($state, 'mutate'), 'Mutate unlocked.');
assertTrue(!Tutorial::can($state, 'cross'), 'Cross still locked until mission path.');

// Mutate to advance tutorial
$state['resources']['organic'] = max(2, (int) $state['resources']['organic']);
GameEngine::apply($state, 'mutate', ['mutation_choice' => 'stats']);
assertTrue(!empty($state['creature']['mutated']), 'Mutated.');
assertTrue(($state['tutorial']['step'] ?? '') === Tutorial::STEP_TEAM, 'Tutorial → team.');

// Compose team
GameEngine::apply($state, 'set_team', [
    'drone_type' => 'recon',
    'escort_creature_id' => (string) $state['creature']['id'],
]);
assertTrue(!empty($state['ui']['team_configured']), 'Team configured.');
assertTrue(($state['tutorial']['step'] ?? '') === Tutorial::STEP_MISSION, 'Tutorial → mission.');

// Mission
$state['ui']['force_mission_outcome'] = 'success';
GameEngine::apply($state, 'mission');
assertTrue(!empty($state['creature']['mission_done']), 'Mission done.');
assertTrue(($state['tutorial']['step'] ?? '') === Tutorial::STEP_CROSS, 'Tutorial → cross.');
assertTrue(Tutorial::can($state, 'cross'), 'Cross unlocked after mission.');

// Cross
$pool = GameQueries::analyzedCreatures($state);
$ids = array_keys($pool);
// Need organic for cross; mission may have granted some
$state['resources']['organic'] = max(2, (int) $state['resources']['organic']);
GameEngine::apply($state, 'cross', ['parent_a' => $ids[0], 'parent_b' => $ids[1] ?? $ids[0]]);
// if same species parents issue - get distinct
if (empty($state['creature']['is_hybrid'])) {
    $distinct = array_values(GameQueries::distinctAnalyzedSpecies($state));
    if (count($distinct) >= 2) {
        $state['resources']['organic'] = 5;
        GameEngine::apply($state, 'cross', [
            'parent_a' => (string) $distinct[0]['id'],
            'parent_b' => (string) $distinct[1]['id'],
        ]);
    }
}
assertTrue(!empty($state['creature']['is_hybrid']), 'Hybrid created.');
assertTrue(($state['tutorial']['step'] ?? '') === Tutorial::STEP_MUSEUM, 'Tutorial → museum.');

// Museum → free
GameEngine::apply($state, 'exhibit');
assertTrue(empty($state['tutorial']['active']) || ($state['tutorial']['step'] ?? '') === Tutorial::STEP_FREE, 'Tutorial complete.');

// ========== Free-play mechanics (skip tutorial) ==========
$fp = StateFactory::initial();
freePlay($fp);

// Intent at free start
$primary = IntentResolver::primary($fp);
assertTrue(
    str_contains((string) ($primary['href'] ?? ''), 'world')
    || ($primary['action'] ?? '') === 'recon'
    || str_contains((string) ($primary['label'] ?? ''), 'zone'),
    'Free play start exploration.'
);

GameEngine::apply($fp, 'select_zone', ['zone_id' => 'plaine']);
GameEngine::apply($fp, 'recon');
GameEngine::apply($fp, 'analyze');
GameEngine::apply($fp, 'select_zone', ['zone_id' => 'crete']);
GameEngine::apply($fp, 'recon');
GameEngine::apply($fp, 'analyze');
assertTrue(GameQueries::canCross($fp), 'Can cross with two species + organic.');
assertTrue(GameQueries::hasCrossParents($fp), 'Has cross parents with two species.');

// Labo : muter est prioritaire si le spécimen n’est pas muté ; forcer muté pour tester croiser
$fp['screen'] = 'lab';
foreach ($fp['bestiary'] as $id => $c) {
    $fp['bestiary'][$id]['mutated'] = true;
}
if (!empty($fp['creature']['id'])) {
    $fp['creature']['mutated'] = true;
}
$primary = IntentResolver::primary($fp);
// Croisement = section #lab-cross (plus de CTA principal « croiser » en double)
$pAct = (string) ($primary['action'] ?? '');
$pHref = (string) ($primary['href'] ?? '');
assertTrue(
    $pAct === 'cross'
    || str_contains($pHref, 'field')
    || str_contains($pHref, 'world')
    || str_contains($pHref, 'bestiary')
    || str_contains($pHref, 'lab'),
    'Primary remains useful once two species exist (free play); croisement is the lab section.'
);

$pool = GameQueries::analyzedCreatures($fp);
$ids = array_keys($pool);
$preview = \Genesis\Game\CreatureFactory::hybridPreview($pool[$ids[0]], $pool[$ids[1]]);
assertTrue(isset($preview['name'], $preview['stats']['force']), 'Hybrid preview has name and stats.');
assertTrue(($preview['synergy_id'] ?? '') === 'dune_armure', 'Chaleur×minéral should be Dune d’armure synergy.');

GameEngine::apply($fp, 'cross', ['parent_a' => $ids[0], 'parent_b' => $ids[1]]);
assertTrue(!empty($fp['creature']['is_hybrid']), 'Hybrid created free play.');
assertTrue(($fp['creature']['synergy_title'] ?? '') === 'Dune d’armure', 'Hybrid carries synergy title.');

$fp['ui']['force_mission_outcome'] = 'success';
GameEngine::apply($fp, 'mission');
assertTrue($fp['creature']['mission_done'] === true, 'Mission completed.');
GameEngine::apply($fp, 'exhibit');
assertTrue(count($fp['museum']['exhibits']) >= 1, 'Museum stores discovery.');

// Recover path
$fp['creature']['status'] = 'blessée';
$fp['resources']['organic'] = 3;
GameEngine::apply($fp, 'recover');
assertTrue($fp['creature']['status'] === 'prête', 'Recover heals injury.');

// Escort with team
$st = StateFactory::initial();
freePlay($st);
$st['resources']['logistics'] = 20;
GameEngine::apply($st, 'select_zone', ['zone_id' => 'plaine']);
GameEngine::apply($st, 'recon');
GameEngine::apply($st, 'analyze');
GameEngine::apply($st, 'select_zone', ['zone_id' => 'crete']);
GameEngine::apply($st, 'recon');
GameEngine::apply($st, 'analyze');
GameEngine::apply($st, 'cross');
assertTrue(!empty($st['creature']['is_hybrid']), 'Hybrid for escort.');
$hybridId = (string) $st['creature']['id'];
GameEngine::apply($st, 'set_team', ['drone_type' => 'recon', 'escort_creature_id' => $hybridId]);
$samplesBefore = (int) $st['resources']['samples'];
// Escorte prête pour le bonus
if (($st['creature']['status'] ?? '') !== 'prête') {
    $st['creature']['status'] = 'prête';
    $st['bestiary'][(string) $st['creature']['id']] = $st['creature'];
}
GameEngine::apply($st, 'select_zone', ['zone_id' => 'foret']);
$st['ui']['force_mission_outcome'] = 'success';
GameEngine::apply($st, 'recon');
assertTrue((int) $st['resources']['samples'] > $samplesBefore, 'Hybrid escort yields extra sample.');
assertTrue(($st['creature']['species_key'] ?? '') === 'cryopode', 'New discovery after escort.');
assertTrue(str_contains((string) ($st['world']['last_report'] ?? ''), 'Équipe :'), 'Success report lists team.');

// --- Save / load file ---
$saveState = StateFactory::initial();
freePlay($saveState);
GameEngine::apply($saveState, 'select_zone', ['zone_id' => 'plaine']);
GameEngine::apply($saveState, 'recon');
GameEngine::apply($saveState, 'analyze');
$meta = \Genesis\Game\SaveStore::save($saveState, 'Test auto');
assertTrue(isset($meta['id']) && $meta['id'] !== '', 'Save written to DB.');
$loaded = \Genesis\Game\SaveStore::load($meta['id']);
assertTrue(($loaded['creature']['species'] ?? '') === 'Thermidé', 'Loaded state has analyzed Thermidé.');
assertTrue(\Genesis\Game\SaveStore::delete($meta['id']), 'Save deleted.');

// --- MVE + Baie (cartographie natale complète, unlock chain) ---
$mveState = StateFactory::initial();
Tutorial::skip($mveState);
$mveState['resources']['logistics'] = 40;
$mveState['resources']['organic'] = 20;
$mveState['resources']['biomass'] = 20;
$mveState['ui']['force_mission_outcome'] = 'success';

// BFS unlock all natal via recon
$queue = ['plaine'];
$seen = [];
while ($queue !== []) {
    $zid = array_shift($queue);
    if (isset($seen[$zid])) {
        continue;
    }
    $seen[$zid] = true;
    if (!isset($mveState['world']['zones'][$zid])) {
        continue;
    }
    if (($mveState['world']['zones'][$zid]['layer'] ?? 'natal') !== 'natal') {
        continue;
    }
    // Unlock if locked (neighbor from previous)
    $mveState['world']['zones'][$zid]['unlocked'] = true;
    $mveState['world']['zones'][$zid]['locked'] = false;
    if (empty($mveState['world']['zones'][$zid]['explored'])) {
        GameEngine::apply($mveState, 'select_zone', ['zone_id' => $zid]);
        GameEngine::apply($mveState, 'recon');
        if (!empty($mveState['creature']['discovered']) && empty($mveState['creature']['analyzed'])) {
            // ensure capture drone
            if (\Genesis\Game\DroneYard::countReady($mveState, \Genesis\Game\DroneYard::TYPE_CAPTURE) < 1) {
                $mveState['resources']['biomass'] = 20;
                $mveState['resources']['logistics'] = max(10, (int) $mveState['resources']['logistics']);
                GameEngine::apply($mveState, 'craft_drone', ['drone_type' => 'capture']);
            }
            GameEngine::apply($mveState, 'analyze');
        }
    }
    foreach ($mveState['world']['zones'][$zid]['unlocks'] ?? [] as $next) {
        if (!isset($seen[$next])) {
            $queue[] = $next;
        }
    }
}

assertTrue(count(GameQueries::analyzedCreatures($mveState)) >= 2, 'At least 2 analyzed for cross.');
$mveState['resources']['organic'] = 10;
GameEngine::apply($mveState, 'cross');
$mveState['ui']['force_mission_outcome'] = 'success';
GameEngine::apply($mveState, 'mission');
GameEngine::apply($mveState, 'exhibit');
while (count($mveState['heritage']['memories'] ?? []) < 3) {
    $mveState['heritage']['memories'][] = 'Mémoire de test MVE.';
}
$mve = MveEvaluator::evaluate($mveState);
assertTrue($mve['complete'] === true, 'MVE complete after full natal map + patrimoine.');

GameEngine::apply($mveState, 'open_baie');
assertTrue(!empty($mveState['export']['baie_unlocked']), 'Baie unlocked.');
assertTrue(($mveState['screen'] ?? '') === 'world', 'Spatio-port opens world for orbit.');
assertTrue(!empty($mveState['world']['zones']['orbite_a']['unlocked']), 'First orbital unlocked.');
assertTrue(empty($mveState['world']['zones']['orbite_a']['locked']), 'First orbital not locked.');
// Chaîne : les suivantes restent fermées
assertTrue(empty($mveState['world']['zones']['orbite_b']['unlocked']) || !empty($mveState['world']['zones']['orbite_b']['locked']), 'Second orbital still closed at baie open.');
assertTrue(isset($mveState['world']['zones']['orbite_c'], $mveState['world']['zones']['orbite_d']), 'Orbital chain c/d exist.');

// Craft orbital drone + explore chain
$mveState['resources']['biomass'] = 40;
$mveState['resources']['logistics'] = 40;
GameEngine::apply($mveState, 'craft_drone', ['drone_type' => 'orbital']);
$mveState['ui']['force_mission_outcome'] = 'success';
GameEngine::apply($mveState, 'select_zone', ['zone_id' => 'orbite_a']);
GameEngine::apply($mveState, 'recon');
assertTrue(!empty($mveState['world']['zones']['orbite_a']['explored']), 'Orbite A explored.');
assertTrue(!empty($mveState['world']['zones']['orbite_b']['unlocked']), 'Orbite B unlocked after A.');
assertTrue(($mveState['creature']['species_key'] ?? '') === 'astérion', 'Orbital A primary species Astérion (instant).');

// Continue chain to deep signal
foreach (['orbite_b', 'orbite_c', 'orbite_d'] as $oid) {
    if (\Genesis\Game\DroneYard::countReady($mveState, \Genesis\Game\DroneYard::TYPE_ORBITAL) < 1) {
        $mveState['resources']['biomass'] = 40;
        $mveState['resources']['logistics'] = 40;
        GameEngine::apply($mveState, 'craft_drone', ['drone_type' => 'orbital']);
    }
    $mveState['world']['zones'][$oid]['unlocked'] = true;
    $mveState['world']['zones'][$oid]['locked'] = false;
    $mveState['ui']['force_mission_outcome'] = 'success';
    GameEngine::apply($mveState, 'select_zone', ['zone_id' => $oid]);
    GameEngine::apply($mveState, 'recon');
}
assertTrue(!empty($mveState['export']['deep_space_signal']), 'Deep space signal after full orbital map.');
$p2 = \Genesis\Game\Phase2::orbitalProgress($mveState);
assertTrue($p2['complete'] === true, 'Orbital map complete.');

// Baie blocked if MVE incomplete
$blocked = StateFactory::initial();
Tutorial::skip($blocked);
GameEngine::apply($blocked, 'open_baie');
assertTrue(empty($blocked['export']['baie_unlocked']), 'Cannot open baie without MVE.');

// --- Codex ---
$codex = \Genesis\Game\Data\Synergies::codex([]);
assertTrue(count($codex) === \Genesis\Game\Data\Synergies::totalNamed(), 'Codex lists all named synergies.');
$codexOpen = \Genesis\Game\Data\Synergies::codex(['dune_armure']);
$foundOpen = false;
foreach ($codexOpen as $entry) {
    if (($entry['id'] ?? '') === 'dune_armure' && !empty($entry['discovered'])) {
        $foundOpen = true;
    }
}
assertTrue($foundOpen, 'Discovered synergy id unlocks codex entry.');

// --- Economy balance ---
assertTrue(Economy::reconCost(['risk' => 'modéré']) === 1, 'Modéré recon = 1.');
assertTrue(Economy::reconCost(['risk' => 'élevé']) === 2, 'Élevé recon = 2.');
assertTrue(Economy::reconCost(['risk' => 'extrême']) === 3, 'Extrême recon = 3.');

$bal = StateFactory::initial();
freePlay($bal);
GameEngine::apply($bal, 'select_zone', ['zone_id' => 'crete']);
$logBefore = (int) $bal['resources']['logistics'];
$bal['ui']['force_mission_outcome'] = 'success';
GameEngine::apply($bal, 'recon');
// Coût base élevé = 2 ; le butin de recon peut rendre +1 log → net variable
assertTrue((int) $bal['resources']['logistics'] < $logBefore, 'Crete recon spends logistics (net after terrain yield).');
assertTrue(Economy::reconCost(['risk' => 'élevé'], 1) === 2, 'Élevé recon base cost = 2.');

// Scorie too expensive if logistics low
$poor = StateFactory::initial();
freePlay($poor);
$poor['resources']['logistics'] = 2;
GameEngine::apply($poor, 'select_zone', ['zone_id' => 'scorie']);
GameEngine::apply($poor, 'recon');
assertTrue(empty($poor['creature']['discovered']), 'Cannot recon extrême with 2 logistics.');
assertTrue(empty($poor['world']['zones']['scorie']['explored']), 'Scorie stays unexplored if recon fails.');

// Session path (unlocked plaine only unless free)
GameEngine::reset();
$_SESSION['genesis_game'] = StateFactory::initial();
freePlay($_SESSION['genesis_game']);
$redirect = GameEngine::process([
    'action' => 'select_zone',
    'zone_id' => 'foret',
]);
assertTrue($redirect === 'world', 'Select zone lands on world.');
$_SESSION['genesis_game']['ui']['force_mission_outcome'] = 'success';
GameEngine::process(['action' => 'recon']);
$s = GameEngine::state();
assertTrue(($s['creature']['species_key'] ?? '') === 'cryopode', 'Foret discovers Cryopode.');
GameEngine::process(['action' => 'analyze']);
$s = GameEngine::state();
assertTrue($s['creature']['species'] === 'Cryopode', 'Cryopode analyzed via session.');

// Catalog content
assertTrue(\Genesis\Game\Data\Catalog::speciesByKey('lumivive') !== null, 'Lumivive in catalog.');
assertTrue(\Genesis\Game\Data\Catalog::zone('scorie') !== null, 'Scorie zone exists.');
assertTrue(\Genesis\Game\Data\Catalog::speciesByKey('mycorène') !== null, 'Mycorène in catalog.');
assertTrue(\Genesis\Game\Data\Catalog::zone('vents') !== null, 'Vents zone exists.');
assertTrue(\Genesis\Game\Data\Catalog::speciesByKey('sablex') !== null, 'Sablex in catalog.');
assertTrue(\Genesis\Game\Data\Catalog::speciesByKey('gravile') !== null, 'Gravile in catalog.');
assertTrue(\Genesis\Game\Data\Catalog::speciesByKey('nubivor') !== null, 'Nubivor in catalog.');
assertTrue(\Genesis\Game\Data\Catalog::speciesByKey('ombreline') !== null, 'Ombreline in catalog.');
assertTrue(count(\Genesis\Game\Data\Catalog::zoneSpeciesKeys(\Genesis\Game\Data\Catalog::zone('plaine'))) === 3, 'Plaine has 3 species pool.');
$synVent = \Genesis\Game\Data\Synergies::forBiomes('vent', 'chaleur');
assertTrue(($synVent['id'] ?? '') === 'simoun', 'Vent×chaleur synergy Simoun.');

// Replay secondary read
$rep = StateFactory::initial();
freePlay($rep);
$rep['resources']['logistics'] = 20;
GameEngine::apply($rep, 'select_zone', ['zone_id' => 'plaine']);
$rep['ui']['force_mission_outcome'] = 'success';
GameEngine::apply($rep, 'recon');
$rep['world']['zones']['plaine']['species_found'] = ['thermidé']; // force already known for next
// Instant pick always first key thermidé → replay
$samplesR = (int) $rep['resources']['samples'];
$orgR = (int) $rep['resources']['organic'];
// Need another recon with species already found - after first recon found has thermidé
if (!empty($rep['creature']['discovered'])) {
    GameEngine::apply($rep, 'analyze');
}
$rep['resources']['logistics'] = 20;
// craft recon if needed
GameEngine::apply($rep, 'select_zone', ['zone_id' => 'plaine']);
$rep['ui']['force_mission_outcome'] = 'success';
// Mark thermidé found so replay triggers when instant picks thermidé
$rep['world']['zones']['plaine']['species_found'] = ['thermidé'];
GameEngine::apply($rep, 'recon');
assertTrue(
    (int) $rep['resources']['samples'] > $samplesR
    || str_contains((string) ($rep['world']['last_report'] ?? ''), 'secondaire'),
    'Zone replay yields samples or secondary read note (no organic from explo).'
);
// L’exploration ne doit pas faire monter l’organique
assertTrue(
    (int) $rep['resources']['organic'] <= $orgR + 0,
    'Exploration must not grant organic (missions only).'
);

echo "OK — game_loop_test passed (tutorial + progressive unlock + farm + free play + content).\n";
