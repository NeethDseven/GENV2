<?php

declare(strict_types=1);

require dirname(__DIR__) . '/src/bootstrap.php';

use Genesis\Game\MemoryJournal;

function assert_true(bool $c, string $m): void
{
    if (!$c) {
        fwrite(STDERR, "FAIL — $m\n");
        exit(1);
    }
}

$lines = [
    'Recon plaine : contact Thermidé (scan 40%).',
    'Capture : Thermidé (35% connu) · +1 prélèvement.',
    'Croisement : Thermidé × Cryopode → TherCryo.',
    'Synergie : Thermocline vivante',
    'Mission : succès.',
    'Mutation fatale : X perdu. La forme s’est effondrée.',
    'Fil libre · Trois filets en flotte — la capture n’est plus un hasard.',
    'Soin : blessée → prête (−2 organique).',
];

$entries = MemoryJournal::entries($lines, 10);
assert_true(count($entries) === 8, 'all entries');
assert_true($entries[0]['kind'] === 'heal', 'latest is heal (reversed)');
assert_true($entries[1]['kind'] === 'fil', 'fil libre');
assert_true($entries[2]['kind'] === 'loss', 'mutation fatale');
assert_true($entries[3]['kind'] === 'mission', 'mission');
assert_true($entries[4]['kind'] === 'synergy', 'synergy');
assert_true($entries[5]['kind'] === 'cross', 'cross');

$state = ['heritage' => ['memories' => $lines]];
$view = MemoryJournal::view($state, 5);
assert_true($view['total'] === 8, 'total raw');
assert_true(count($view['entries']) === 5, 'limited');
assert_true($view['highlight'] !== null, 'highlight');
assert_true($view['empty'] === false, 'not empty');

echo "OK — memory_journal_test\n";
