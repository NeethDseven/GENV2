<?php

declare(strict_types=1);

/**
 * API soft — actions sans rechargement complet de page.
 * POST action=set_team (et assimilés) → JSON snapshot UI.
 */

require dirname(__DIR__) . '/src/bootstrap.php';

use Genesis\Game\GameEngine;
use Genesis\Game\DroneYard;
use Genesis\Game\Economy;

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'POST only'], JSON_UNESCAPED_UNICODE);
    exit;
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$softActions = ['set_team'];
$action = (string) ($_POST['action'] ?? '');
if (!in_array($action, $softActions, true)) {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'error' => 'action_not_soft',
        'reload' => true,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $preferredScreen = (string) ($_POST['ui_screen'] ?? $_POST['return_screen'] ?? '');
    $resultScreen = GameEngine::process($_POST);

    // Rester sur l’écran UI demandé si l’action soft le permet
    $viewScreen = $preferredScreen !== '' ? $preferredScreen : $resultScreen;
    $state = GameEngine::view(['screen' => $viewScreen]);
    $view = $state['view'] ?? [];
    $resources = $state['resources'] ?? [];
    $expedition = $state['expedition'] ?? [];
    $hud = $view['hud'] ?? [];
    $event = $view['latest_event'] ?? null;
    $flash = $view['flash'] ?? null;

    $readyCap = (int) ($hud['capture_ready'] ?? DroneYard::countReady($state, DroneYard::TYPE_CAPTURE));
    $readyStudy = (int) ($hud['study_ready'] ?? DroneYard::countReady($state, DroneYard::TYPE_STUDY));
    $capWant = max(1, min(5, (int) ($expedition['capture_count'] ?? 1)));
    $studyWant = max(0, min(5, (int) ($expedition['study_count'] ?? 0)));
    $capEscorts = Economy::captureEscortIds($state);

    $toast = '';
    if (is_array($event) && trim((string) ($event['message'] ?? '')) !== '') {
        $toast = trim((string) ($event['label'] ?? '')) . ' — ' . trim((string) ($event['message'] ?? ''));
        $toast = trim($toast, " —");
    }

    echo json_encode([
        'ok' => true,
        'screen' => $viewScreen,
        'resources' => [
            'organic' => (int) ($resources['organic'] ?? 0),
            'biomass' => (int) ($resources['biomass'] ?? 0),
            'samples' => (int) ($resources['samples'] ?? 0),
            'logistics' => (int) ($resources['logistics'] ?? 0),
        ],
        'resource_tips' => $view['resource_tips'] ?? [],
        'hud' => [
            'recon_ready' => (int) ($hud['recon_ready'] ?? 0),
            'recon_total' => (int) ($hud['recon_total'] ?? 0),
            'capture_ready' => $readyCap,
            'capture_total' => (int) ($hud['capture_total'] ?? 0),
            'study_ready' => $readyStudy,
            'study_total' => (int) ($hud['study_total'] ?? 0),
            'species' => (int) ($hud['species'] ?? 0),
        ],
        'expedition' => [
            'drone_count' => max(1, min(5, (int) ($expedition['drone_count'] ?? 1))),
            'capture_count' => $capWant,
            'study_count' => $studyWant,
            'escort_creature_id' => $expedition['escort_creature_id'] ?? null,
            'capture_escorts' => $capEscorts,
        ],
        'capture_chance' => $view['capture_chance'] ?? null,
        'capture_chance_table' => $view['capture_chance_table'] ?? [],
        'mutate_chance' => $view['mutate_chance'] ?? null,
        'cross_chance' => $view['cross_chance'] ?? null,
        'study_chance_table' => $view['study_chance_table'] ?? [],
        'recon_chance_table' => $view['recon_chance_table'] ?? [],
        'toast' => $toast,
        'event' => is_array($event) ? $event : null,
    ], JSON_UNESCAPED_UNICODE);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => 'server',
        'message' => $e->getMessage(),
        'reload' => true,
    ], JSON_UNESCAPED_UNICODE);
}
