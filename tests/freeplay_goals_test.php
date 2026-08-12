<?php

declare(strict_types=1);

/**
 * Free-play goals : détection + récompense soft (pas de double claim).
 */

require dirname(__DIR__) . '/src/bootstrap.php';

use Genesis\Game\StateFactory;
use Genesis\Game\FreePlayGoals;
use Genesis\Game\DroneYard;
use Genesis\Game\GameEngine;
use Genesis\Game\Tutorial;

function assert_true(bool $cond, string $msg): void
{
    if (!$cond) {
        fwrite(STDERR, "FAIL — $msg\n");
        exit(1);
    }
}

$state = StateFactory::initial();
// Skip tuto
Tutorial::skip($state);
FreePlayGoals::ensure($state);

// 1) filets_trois : ajouter des capture drones
assert_true(!FreePlayGoals::isMet($state, 'filets_trois'), 'filets not met yet');
$state['drones']['fleet'][] = [
    'id' => 'dr_cap2',
    'type' => DroneYard::TYPE_CAPTURE,
    'label' => 'Filet',
    'status' => 'ready',
];
$state['drones']['fleet'][] = [
    'id' => 'dr_cap3',
    'type' => DroneYard::TYPE_CAPTURE,
    'label' => 'Filet',
    'status' => 'ready',
];
assert_true(FreePlayGoals::isMet($state, 'filets_trois'), 'filets met with 3');
FreePlayGoals::tick($state);
assert_true(in_array('filets_trois', $state['freeplay']['completed'], true), 'filets claimed');
$logAfter = (int) ($state['resources']['logistics'] ?? 0);
assert_true($logAfter >= 4, 'logistics reward applied'); // start 3 +1

// Double tick ne re-récompense pas
$logBefore = $logAfter;
FreePlayGoals::tick($state);
assert_true((int) ($state['resources']['logistics'] ?? 0) === $logBefore, 'no double reward');

// 2) soin_blessure flag
assert_true(!FreePlayGoals::isMet($state, 'soin_blessure'), 'heal not met');
FreePlayGoals::markHealedWounded($state);
assert_true(FreePlayGoals::isMet($state, 'soin_blessure'), 'heal met');
FreePlayGoals::tick($state);
assert_true(in_array('soin_blessure', $state['freeplay']['completed'], true), 'heal claimed');

// 3) nom_secret via heritage
$state['heritage']['synergies'][] = [
    'id' => 'thermocline',
    'title' => 'Thermocline vivante',
    'hybrid' => 'Test',
];
assert_true(FreePlayGoals::isMet($state, 'nom_secret'), 'named synergy met');
// biomes_croises aussi (named counts)
assert_true(FreePlayGoals::isMet($state, 'biomes_croises'), 'biome cross met via named');
FreePlayGoals::tick($state);
assert_true(in_array('nom_secret', $state['freeplay']['completed'], true), 'nom claimed');
assert_true(in_array('biomes_croises', $state['freeplay']['completed'], true), 'biomes claimed');

// 4) Vue
$view = FreePlayGoals::view($state);
assert_true($view['done'] >= 4, 'view done count');
assert_true($view['total'] === 5, '5 goals total');

// 5) Assistant hint
$hint = FreePlayGoals::assistantHint($state);
assert_true($hint !== null || $view['all_done'] || $view['next'] !== null, 'hint or next ok');

// 6) Process recover marks heal (fresh state)
$state2 = StateFactory::initial();
Tutorial::skip($state2);
$state2['creature'] = [
    'id' => 'c_hurt',
    'name' => 'Test blessé',
    'species' => 'Thermidé',
    'analyzed' => true,
    'discovered' => true,
    'status' => 'blessée',
    'stats' => ['force' => 4, 'vitesse' => 4, 'resistance' => 4, 'intelligence' => 4],
    'purity' => 80,
    'abilities' => [],
    'biome' => 'chaleur',
];
$state2['bestiary']['c_hurt'] = $state2['creature'];
$state2['resources']['organic'] = 10;
GameEngine::apply($state2, 'recover', []);
assert_true(!empty($state2['freeplay']['flags']['healed_wounded']), 'recover sets heal flag');
assert_true(
    in_array('soin_blessure', $state2['freeplay']['completed'] ?? [], true)
    || FreePlayGoals::isMet($state2, 'soin_blessure'),
    'heal goal after recover'
);

// 7) Synergie minéral|spore existe
$syn = \Genesis\Game\Data\Synergies::forBiomes('minéral', 'spore');
assert_true($syn !== null && ($syn['id'] ?? '') === 'sel_mycelien', 'sel mycélien synergy');

// 8) Nouvelles espèces
assert_true(\Genesis\Game\Data\Catalog::speciesByKey('tourbeille') !== null, 'tourbeille exists');
assert_true(\Genesis\Game\Data\Catalog::speciesByKey('cindrite') !== null, 'cindrite exists');

echo "OK — freeplay_goals_test\n";
