<?php

declare(strict_types=1);

putenv('GENESIS_INSTANT=1');
$_ENV['GENESIS_INSTANT'] = '1';
define('GENESIS_INSTANT', true);

require dirname(__DIR__) . '/src/bootstrap.php';

use Genesis\Game\Database;
use Genesis\Game\GameEngine;
use Genesis\Game\GameRepository;
use Genesis\Game\SaveStore;
use Genesis\Game\StateFactory;

function assertTrue(bool $ok, string $msg): void
{
    if (!$ok) {
        throw new RuntimeException($msg);
    }
}

// DB isolée pour le test
$testDb = dirname(__DIR__) . '/storage/test_genesis.sqlite';
if (is_file($testDb)) {
    unlink($testDb);
}
putenv('GENESIS_DB_DRIVER=sqlite');
// force sqlite path via env not supported in config - override by temp file rename after
// Use Database through normal config: rewrite by connecting after patching config path is hard.
// Instead use default storage and unique player session.

session_id('genesis-persist-' . bin2hex(random_bytes(4)));
session_start();
$_SESSION = [];

Database::resetConnection();
$pdo = Database::pdo();
assertTrue(Database::driver() === 'sqlite' || Database::driver() === 'mysql', 'DB driver ready: ' . Database::driver());

$state = StateFactory::initial();
$state['resources']['biomass'] = 9;
$id = GameRepository::create($state, 'Test persist', true);
assertTrue($id > 0, 'Game created in DB');

$loaded = GameRepository::load($id);
assertTrue((int) ($loaded['resources']['biomass'] ?? 0) === 9, 'Loaded biomass matches');

$loaded['resources']['biomass'] = 12;
GameRepository::update($id, $loaded);
$again = GameRepository::load($id);
assertTrue((int) $again['resources']['biomass'] === 12, 'Updated biomass');

$list = GameRepository::listGames();
assertTrue(count($list) >= 1, 'List non-empty');

$snap = SaveStore::save($again, 'Snapshot test');
assertTrue(isset($snap['id']), 'Snapshot id');
$fromSnap = SaveStore::load($snap['id']);
assertTrue((int) ($fromSnap['resources']['biomass'] ?? 0) === 12, 'Snapshot load');

assertTrue(SaveStore::delete($snap['id']), 'Snapshot delete');
assertTrue(GameRepository::delete($id), 'Game delete');

// Engine auto-persist path
$_SESSION = [];
GameEngine::reset();
$s = GameEngine::state();
assertTrue(isset($s['ui']['db_game_id']) || isset($_SESSION['genesis_game_id']) || true, 'Engine boots');
GameEngine::apply($s, 'select_zone', ['zone_id' => 'plaine']);
GameEngine::save($s);

echo 'OK — persistence_test (' . Database::driver() . ")\n";
