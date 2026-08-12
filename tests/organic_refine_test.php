<?php

declare(strict_types=1);

putenv('GENESIS_INSTANT=1');
$_ENV['GENESIS_INSTANT'] = '1';
define('GENESIS_INSTANT', true);

require dirname(__DIR__) . '/src/bootstrap.php';

use Genesis\Game\Economy;
use Genesis\Game\GameEngine;
use Genesis\Game\StateFactory;
use Genesis\Game\Tutorial;

session_id('test-org-' . bin2hex(random_bytes(4)));
session_start();
$_SESSION = [];

// Synthèse biomasse / prélèvements → organique retirée
$s = StateFactory::initial();
Tutorial::skip($s);
$s['resources']['organic'] = 0;
$s['resources']['samples'] = 12;
$s['resources']['biomass'] = 20;
$_SESSION['genesis_game'] = $s;

GameEngine::process([
    'action' => 'refine_organic',
    'refine_source' => 'biomass',
]);
$s2 = $_SESSION['genesis_game'];
if ((int) ($s2['resources']['organic'] ?? 0) !== 0) {
    fwrite(STDERR, "FAIL biomass refine should be disabled\n");
    exit(1);
}
if ((int) ($s2['resources']['biomass'] ?? 0) !== 20) {
    fwrite(STDERR, "FAIL biomass should be unchanged\n");
    exit(1);
}

$tips = Economy::resourceTips($s2['resources']);
if (count($tips) !== 4 || !str_contains($tips[0]['tip'], 'mission')) {
    fwrite(STDERR, "FAIL resource tips should mention mission\n");
    exit(1);
}

echo "OK — organic only from missions · refine disabled\n";
