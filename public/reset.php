<?php

declare(strict_types=1);

/**
 * Reset one-shot pour retester depuis le début.
 * Efface la session jeu puis redirige vers l’institut.
 */
require dirname(__DIR__) . '/src/bootstrap.php';

use Genesis\Game\GameEngine;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

unset($_SESSION['genesis_game']);
GameEngine::reset();

header('Location: index.php?screen=institute&fresh=1');
exit;
