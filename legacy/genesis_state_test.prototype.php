<?php

declare(strict_types=1);

require __DIR__ . '/../src/app.php';

function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

session_id('genesis-test-' . bin2hex(random_bytes(4)));
session_start();
$_SESSION = [];

$state = genesis_default_state();
assertTrue($state['screen'] === 'institute', 'The default screen should start on institute.');
assertTrue(($state['resources']['logistics'] ?? 0) === 3, 'Default logistics should be 3 (P1 multi-zone).');
assertTrue(isset($state['drones']['recon']), 'Default drones should include recon class.');
assertTrue(($state['campus']['baie']['status'] ?? '') === 'verrouillée', 'Baie should start locked.');
assertTrue(isset($state['creature']), 'Default state should include a creature.');
assertTrue(($state['creature']['analyzed'] ?? true) === false, 'Creature should start unanalyzed.');
assertTrue(isset($state['museum']['exhibits']), 'Default state should include museum exhibits.');
assertTrue(count($state['world']['zones'] ?? []) >= 2, 'World should expose at least 2 zones.');
assertTrue(isset(genesis_species_catalog()['thermidé']), 'Species catalog should include Thermidé.');

// Legacy branch → creature migration
$legacyState = [
    'screen' => 'institute',
    'world' => ['name' => 'Aster-0', 'recon_done' => false],
    'branch' => ['name' => 'Spécimen d’Origine', 'nature' => 'Lave', 'role' => 'Éclaireur'],
    'heritage' => ['memories' => [], 'inscriptions' => []],
    'ui' => ['latest_event' => ['label' => 'Arrivée', 'message' => 'L’Institut se prépare à lire le monde natal.']],
];
$normalized = genesis_normalize_state($legacyState);
assertTrue(isset($normalized['creature']), 'Legacy branch state should migrate to creature.');
assertTrue(($normalized['creature']['mission_done'] ?? true) === false, 'Legacy state should normalize mission_done.');
assertTrue(isset($normalized['resources']['organic']), 'Legacy state should gain resources.');
assertTrue(isset($normalized['ui']['genesis']['marker']), 'Legacy state should gain GENESIS voice.');
assertTrue(isset($normalized['campus']['musee']), 'Legacy galerie should become musée.');
assertTrue(isset($normalized['world']['zones']['plaine']), 'Legacy state should gain zones.');

$state = genesis_state_view([]);
assertTrue(($state['screen_meta']['title'] ?? '') === 'Institut', 'The initial screen should be Institut.');

// P1.1 — select zone then recon
$redirect = genesis_process_post(['action' => 'select_zone', 'zone_id' => 'plaine', 'screen' => 'world']);
assertTrue($redirect === 'world', 'Select zone should stay on world.');
$state = genesis_state();
assertTrue(($state['world']['selected_zone'] ?? '') === 'plaine', 'Selected zone should be plaine.');

$redirect = genesis_process_post(['action' => 'recon', 'screen' => 'world']);
assertTrue($redirect === 'expedition', 'Recon should move the player to expedition.');

$state = genesis_state();
assertTrue($state['world']['recon_done'] === true, 'Recon should mark the world as explored.');
assertTrue($state['creature']['discovered'] === true, 'Recon should discover a creature signal.');
assertTrue((int) $state['resources']['logistics'] === 2, 'Recon should spend 1 logistics.');
assertTrue((int) $state['drones']['recon']['deployed'] >= 1, 'Recon should deploy a drone.');
assertTrue(($state['world']['signal'] ?? null) !== null, 'Recon should reveal a living signal.');
assertTrue(($state['ui']['genesis']['marker'] ?? '') !== '', 'GENESIS should speak after recon.');
assertTrue(($state['creature']['species_key'] ?? '') === 'thermidé', 'Plaine recon should target thermidé.');
assertTrue(!empty($state['world']['zones']['plaine']['explored']), 'Plaine should be marked explored.');

$progress = genesis_progress($state);
assertTrue($progress['current'] === 'Analyser', 'Progress should advance to Analyser after discovery.');

$redirect = genesis_process_post(['action' => 'analyze', 'screen' => 'expedition']);
assertTrue($redirect === 'expedition', 'Analyze should keep the player on expedition.');

$state = genesis_state();
assertTrue($state['creature']['analyzed'] === true, 'Analyze should mark the creature as analyzed.');
assertTrue($state['creature']['species'] === 'Thermidé', 'Analyze should reveal the species.');
assertTrue((int) $state['creature']['stats']['force'] > 0, 'Analyze should reveal force stat.');
assertTrue((int) $state['resources']['samples'] >= 1, 'Analyze should yield a sample.');

// P1.3 — mutation choice: ability path (default / Peau de fardeau)
$organicBefore = (int) $state['resources']['organic'];
$redirect = genesis_process_post([
    'action' => 'mutate',
    'mutation_choice' => 'ability',
    'screen' => 'expedition',
]);
assertTrue($redirect === 'archives', 'Mutate should move the player to museum screen.');

$state = genesis_state();
assertTrue($state['creature']['mutated'] === true, 'Mutate should mark the creature as mutated.');
assertTrue((int) $state['resources']['organic'] === $organicBefore - 1, 'Mutate should spend organic matter.');
assertTrue(in_array('Peau de fardeau', $state['creature']['abilities'], true), 'Ability mutation should grant Peau de fardeau.');
assertTrue((int) $state['creature']['purity'] < 100, 'Mutate should reduce genetic purity.');

$redirect = genesis_process_post([
    'action' => 'mission',
    'screen' => 'archives',
    'force_mission_outcome' => 'injured',
]);
assertTrue($redirect === 'archives', 'Mission should keep the player in museum screen.');

$state = genesis_state();
assertTrue($state['creature']['mission_done'] === true, 'Mission should mark mission_done.');
assertTrue($state['creature']['status'] === 'blessée', 'Forced injured outcome should set status.');
assertTrue($state['world']['stable'] === false, 'Injury should unset cradle stability.');

// Fresh success path for museum exhibit + stats mutation
$_SESSION['genesis'] = genesis_default_state();
genesis_process_post(['action' => 'select_zone', 'zone_id' => 'plaine', 'screen' => 'world']);
genesis_process_post(['action' => 'recon', 'screen' => 'world']);
genesis_process_post(['action' => 'analyze', 'screen' => 'expedition']);
genesis_process_post([
    'action' => 'mutate',
    'mutation_choice' => 'stats',
    'screen' => 'expedition',
]);
$state = genesis_state();
assertTrue($state['creature']['mutated'] === true, 'Stats mutation should apply.');
assertTrue((int) $state['creature']['stats']['force'] >= 7, 'Stats mutation should boost force (4+3).');
assertTrue(!in_array('Peau de fardeau', $state['creature']['abilities'], true), 'Stats path should not add Peau de fardeau.');

genesis_process_post([
    'action' => 'mission',
    'screen' => 'archives',
    'force_mission_outcome' => 'success',
]);
$state = genesis_state();
assertTrue($state['creature']['status'] === 'prête', 'Forced success should keep status ready.');
assertTrue($state['world']['stable'] === true, 'Success should keep cradle stable.');

$redirect = genesis_process_post(['action' => 'exhibit', 'screen' => 'archives']);
assertTrue($redirect === 'archives', 'Exhibit should keep the player in museum screen.');

$state = genesis_state();
assertTrue(count($state['museum']['exhibits']) >= 1, 'Exhibit should add a museum piece.');
assertTrue(is_array($state['museum']['exhibits'][0]), 'Museum piece should be structured.');
assertTrue(($state['campus']['musee']['status'] ?? '') === 'collection ouverte', 'Musée should open after exhibit.');
assertTrue(($state['campus']['baie']['status'] ?? '') === 'verrouillée', 'Baie must stay locked in the slice.');
assertTrue(($state['ui']['genesis']['marker'] ?? '') === 'Continuité', 'GENESIS should close on Continuity after exhibit.');

// P1.2 — second species via another zone
$_SESSION['genesis'] = genesis_default_state();
genesis_process_post(['action' => 'select_zone', 'zone_id' => 'plaine', 'screen' => 'world']);
genesis_process_post(['action' => 'recon', 'screen' => 'world']);
genesis_process_post(['action' => 'analyze', 'screen' => 'expedition']);
genesis_process_post(['action' => 'select_zone', 'zone_id' => 'crete', 'screen' => 'world']);
genesis_process_post(['action' => 'recon', 'screen' => 'world']);
$state = genesis_state();
assertTrue(($state['creature']['species_key'] ?? '') === 'salinide', 'Crete recon should discover Salinide.');
assertTrue(count($state['bestiary'] ?? []) >= 1, 'Previous creature should be archived in bestiary.');
genesis_process_post(['action' => 'analyze', 'screen' => 'expedition']);
$state = genesis_state();
assertTrue($state['creature']['species'] === 'Salinide', 'Analyze should reveal Salinide.');
assertTrue(count(genesis_analyzed_creatures($state)) >= 2, 'Two analyzed species should be available.');

// P1.4 — cross two analyzed creatures
$organicBefore = (int) $state['resources']['organic'];
$redirect = genesis_process_post(['action' => 'cross', 'screen' => 'expedition']);
assertTrue($redirect === 'expedition', 'Cross should stay on expedition with hybrid active.');
$state = genesis_state();
assertTrue((int) $state['resources']['organic'] === $organicBefore - 1, 'Cross should spend organic.');
assertTrue(!empty($state['creature']['parents']), 'Hybrid should record informative parents.');
assertTrue($state['creature']['analyzed'] === true, 'Hybrid should start analyzed.');
assertTrue($state['creature']['rarity'] === 'unique', 'Hybrid rarity should be unique.');
assertTrue((int) $state['creature']['purity'] === 70, 'Hybrid purity should start at 70.');

// Resource gate: recon blocked without logistics
$_SESSION['genesis'] = genesis_default_state();
$_SESSION['genesis']['resources']['logistics'] = 0;
genesis_process_post(['action' => 'recon', 'screen' => 'world']);
$state = genesis_state();
assertTrue($state['world']['recon_done'] === false, 'Recon without logistics must not complete.');

// Legacy action aliases still work
$_SESSION['genesis'] = genesis_default_state();
genesis_process_post(['action' => 'recon', 'screen' => 'world']);
genesis_process_post(['action' => 'analyze', 'screen' => 'expedition']);
genesis_process_post(['action' => 'transform', 'screen' => 'expedition']);
genesis_process_post([
    'action' => 'test',
    'screen' => 'archives',
    'force_test_outcome' => 'scar',
]);
$state = genesis_state();
assertTrue($state['creature']['mutated'] === true, 'Legacy transform alias should mutate.');
assertTrue($state['creature']['status'] === 'blessée', 'Legacy scar force should injure.');

// Forêt froide → cryopode
$_SESSION['genesis'] = genesis_default_state();
genesis_process_post(['action' => 'select_zone', 'zone_id' => 'foret', 'screen' => 'world']);
genesis_process_post(['action' => 'recon', 'screen' => 'world']);
genesis_process_post(['action' => 'analyze', 'screen' => 'expedition']);
$state = genesis_state();
assertTrue($state['creature']['species'] === 'Cryopode', 'Foret should yield Cryopode.');

// --- Recover status (blessée / épuisée) ---
$_SESSION['genesis'] = genesis_default_state();
genesis_process_post(['action' => 'recon', 'screen' => 'world']);
genesis_process_post(['action' => 'analyze', 'screen' => 'expedition']);
genesis_process_post(['action' => 'mutate', 'mutation_choice' => 'ability', 'screen' => 'expedition']);
genesis_process_post([
    'action' => 'mission',
    'screen' => 'archives',
    'force_mission_outcome' => 'injured',
]);
$state = genesis_state();
assertTrue($state['creature']['status'] === 'blessée', 'Injured mission sets blessée.');
$organicBefore = (int) $state['resources']['organic'];
genesis_process_post(['action' => 'recover', 'screen' => 'expedition']);
$state = genesis_state();
assertTrue($state['creature']['status'] === 'prête', 'Recover should set prête.');
assertTrue((int) $state['resources']['organic'] === $organicBefore - 2, 'Blessée recover costs 2 organic.');
assertTrue($state['world']['stable'] === true, 'Healing injury should restabilize cradle.');

$_SESSION['genesis'] = genesis_default_state();
genesis_process_post(['action' => 'recon', 'screen' => 'world']);
genesis_process_post(['action' => 'analyze', 'screen' => 'expedition']);
genesis_process_post(['action' => 'mutate', 'mutation_choice' => 'stats', 'screen' => 'expedition']);
genesis_process_post([
    'action' => 'mission',
    'screen' => 'archives',
    'force_mission_outcome' => 'exhausted',
]);
$state = genesis_state();
assertTrue($state['creature']['status'] === 'épuisée', 'Exhausted mission sets épuisée.');
$organicBefore = (int) $state['resources']['organic'];
genesis_process_post(['action' => 'recover', 'screen' => 'expedition']);
$state = genesis_state();
assertTrue($state['creature']['status'] === 'prête', 'Recover exhausted should set prête.');
assertTrue((int) $state['resources']['organic'] === $organicBefore - 1, 'Épuisée recover costs 1 organic.');

// --- Second loop: new_exploration ---
$_SESSION['genesis'] = genesis_default_state();
genesis_process_post(['action' => 'recon', 'screen' => 'world']);
genesis_process_post(['action' => 'analyze', 'screen' => 'expedition']);
genesis_process_post(['action' => 'mutate', 'mutation_choice' => 'ability', 'screen' => 'expedition']);
genesis_process_post([
    'action' => 'mission',
    'screen' => 'archives',
    'force_mission_outcome' => 'success',
]);
genesis_process_post(['action' => 'exhibit', 'screen' => 'archives']);
$state = genesis_state();
$logisticsBefore = (int) $state['resources']['logistics'];
$bestiaryCount = count($state['bestiary'] ?? []);
$redirect = genesis_process_post(['action' => 'new_exploration', 'screen' => 'world']);
assertTrue($redirect === 'world', 'New exploration should go to world.');
$state = genesis_state();
assertTrue(($state['creature']['discovered'] ?? true) === false, 'New exploration blanks active creature discovery.');
assertTrue(($state['world']['selected_zone'] ?? 'x') === null || ($state['world']['selected_zone'] ?? '') === '', 'Zone selection reset.');
assertTrue((int) $state['resources']['logistics'] === $logisticsBefore + 1, 'New exploration grants +1 logistics.');
assertTrue(count($state['bestiary'] ?? []) >= $bestiaryCount, 'Bestiary preserved after new exploration.');
assertTrue(count($state['museum']['exhibits'] ?? []) >= 1, 'Museum preserved after new exploration.');

// Second recon on another zone after new exploration
genesis_process_post(['action' => 'select_zone', 'zone_id' => 'crete', 'screen' => 'world']);
genesis_process_post(['action' => 'recon', 'screen' => 'world']);
$state = genesis_state();
assertTrue(($state['creature']['species_key'] ?? '') === 'salinide', 'Second loop can discover another species.');

// Focus creature from bestiary
genesis_process_post(['action' => 'analyze', 'screen' => 'expedition']);
$state = genesis_state();
$ids = array_keys(genesis_analyzed_creatures($state));
assertTrue(count($ids) >= 2, 'Need two analyzed for focus test.');
$focusId = $ids[0];
genesis_process_post(['action' => 'focus_creature', 'creature_id' => $focusId, 'screen' => 'expedition']);
$state = genesis_state();
assertTrue(($state['creature']['id'] ?? '') === $focusId, 'Focus should activate chosen creature.');

// Remission after recover
$_SESSION['genesis'] = genesis_default_state();
genesis_process_post(['action' => 'recon', 'screen' => 'world']);
genesis_process_post(['action' => 'analyze', 'screen' => 'expedition']);
genesis_process_post(['action' => 'mutate', 'mutation_choice' => 'ability', 'screen' => 'expedition']);
genesis_process_post([
    'action' => 'mission',
    'screen' => 'archives',
    'force_mission_outcome' => 'exhausted',
]);
genesis_process_post(['action' => 'recover', 'screen' => 'expedition']);
genesis_process_post(['action' => 'exhibit', 'screen' => 'archives']);
genesis_process_post([
    'action' => 'remission',
    'screen' => 'archives',
    'force_mission_outcome' => 'success',
]);
$state = genesis_state();
assertTrue($state['creature']['mission_done'] === true, 'Remission should complete another mission.');
assertTrue($state['creature']['status'] === 'prête', 'Successful remission keeps prête.');

echo "Genesis loop tests passed\n";
