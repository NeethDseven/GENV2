<?php

declare(strict_types=1);

putenv('GENESIS_INSTANT=1');
$_ENV['GENESIS_INSTANT'] = '1';
define('GENESIS_INSTANT', true);

require dirname(__DIR__) . '/src/bootstrap.php';

use Genesis\Game\DroneYard;
use Genesis\Game\GameEngine;
use Genesis\Game\StateFactory;
use Genesis\Game\Tutorial;

session_id('test-craft-' . bin2hex(random_bytes(4)));
session_start();
$_SESSION = [];

$s = StateFactory::initial();
// Tutoriel actif dès le début
$s['tutorial'] = Tutorial::defaultState();
$s['resources']['biomass'] = 40;
$s['resources']['logistics'] = 20;

// Welcome : aucun craft type
expect(!DroneYard::canCraft($s, DroneYard::TYPE_RECON), 'welcome: no recon craft');
expect(!DroneYard::canCraft($s, DroneYard::TYPE_CAPTURE), 'welcome: no capture craft');
expect(!DroneYard::canCraft($s, DroneYard::TYPE_STUDY), 'welcome: no study craft');

// Première exploration : sondes
$s['tutorial']['step'] = Tutorial::STEP_RECON;
expect(DroneYard::canCraft($s, DroneYard::TYPE_RECON), 'recon step: recon craft');
expect(!DroneYard::canCraft($s, DroneYard::TYPE_CAPTURE), 'recon step: no capture craft');
expect(!DroneYard::canCraft($s, DroneYard::TYPE_STUDY), 'recon step: no study craft');

// Première capture : drones de prise
$s['tutorial']['step'] = Tutorial::STEP_ANALYZE;
expect(DroneYard::canCraft($s, DroneYard::TYPE_RECON), 'analyze: recon still');
expect(DroneYard::canCraft($s, DroneYard::TYPE_CAPTURE), 'analyze: capture craft');
expect(!DroneYard::canCraft($s, DroneYard::TYPE_STUDY), 'analyze: no study yet');

// Mutation : drones d’analyse
$s['tutorial']['step'] = Tutorial::STEP_MUTATE;
expect(DroneYard::canCraft($s, DroneYard::TYPE_STUDY), 'mutate: study craft');
expect(!DroneYard::canCraft($s, DroneYard::TYPE_ORBITAL), 'mutate: no orbital');

// Libre jeu
$s['tutorial']['active'] = false;
expect(DroneYard::canCraft($s, DroneYard::TYPE_ORBITAL) || Tutorial::can($s, 'craft_orbital'), 'free: orbital feature');

// Process refuse craft study trop tôt
$s = StateFactory::initial();
$s['tutorial'] = ['step' => Tutorial::STEP_RECON, 'completed' => [], 'active' => true];
$s['resources']['biomass'] = 40;
$s['resources']['logistics'] = 20;
$_SESSION['genesis_game'] = $s;
GameEngine::process([
    'action' => 'craft_drone',
    'drone_type' => DroneYard::TYPE_STUDY,
]);
$after = $_SESSION['genesis_game'];
$studyN = DroneYard::countTotal($after, DroneYard::TYPE_STUDY);
if ($studyN > 0) {
    fwrite(STDERR, "FAIL study craft should be blocked early, total=$studyN\n");
    exit(1);
}

// Catalog marks
$s['tutorial']['step'] = Tutorial::STEP_ANALYZE;
$cat = DroneYard::recipesForState($s);
if (empty($cat[DroneYard::TYPE_CAPTURE]['unlocked']) || !empty($cat[DroneYard::TYPE_STUDY]['unlocked'])) {
    fwrite(STDERR, "FAIL recipesForState flags at analyze\n");
    exit(1);
}

echo "OK — craft unlock follows tutorial\n";

function expect(bool $cond, string $msg): void
{
    if (!$cond) {
        fwrite(STDERR, "FAIL $msg\n");
        exit(1);
    }
}
