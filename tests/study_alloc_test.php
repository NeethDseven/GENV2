<?php

declare(strict_types=1);

putenv('GENESIS_INSTANT=1');
$_ENV['GENESIS_INSTANT'] = '1';
define('GENESIS_INSTANT', true);

require dirname(__DIR__) . '/src/bootstrap.php';

use Genesis\Game\GameEngine;
use Genesis\Game\StateFactory;
use Genesis\Game\Tutorial;
use Genesis\Game\DroneYard;

session_id('test-study-alloc-' . bin2hex(random_bytes(4)));
session_start();
$_SESSION = [];

$s = StateFactory::initial();
Tutorial::skip($s);
$s['screen'] = 'lab';
DroneYard::add($s, DroneYard::TYPE_STUDY);
DroneYard::add($s, DroneYard::TYPE_STUDY);
DroneYard::add($s, DroneYard::TYPE_STUDY);
$_SESSION['genesis_game'] = $s;

$screen = GameEngine::process([
    'action' => 'set_team',
    'deploy_mode' => 'study',
    'study_count' => '3',
]);

$s2 = $_SESSION['genesis_game'];
$got = (int) ($s2['expedition']['study_count'] ?? -1);
if ($screen !== 'lab' || $got !== 3) {
    fwrite(STDERR, "FAIL screen=$screen study=$got\n");
    exit(1);
}

// capture alloc
$screen = GameEngine::process([
    'action' => 'set_team',
    'deploy_mode' => 'capture',
    'capture_count' => '2',
]);
$s3 = $_SESSION['genesis_game'];
$cap = (int) ($s3['expedition']['capture_count'] ?? -1);
$studyStill = (int) ($s3['expedition']['study_count'] ?? -1);
if ($cap !== 2 || $studyStill !== 3) {
    fwrite(STDERR, "FAIL cap=$cap studyStill=$studyStill\n");
    exit(1);
}

echo "OK — study/capture allocation via process()\n";
