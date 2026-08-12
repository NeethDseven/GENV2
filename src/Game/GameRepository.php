<?php

declare(strict_types=1);

namespace Genesis\Game;

use PDO;

/**
 * Parties en base (joueur anonyme par cookie token).
 */
final class GameRepository
{
    public static function playerToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        // Session d’abord (CLI / même navigateur sans cookie encore écrit)
        $sessionTok = (string) ($_SESSION['genesis_player_token'] ?? '');
        if ($sessionTok !== '' && preg_match('/^[a-f0-9]{32,64}$/', $sessionTok)) {
            return $sessionTok;
        }
        $cookie = (string) ($_COOKIE['genesis_player'] ?? '');
        if ($cookie !== '' && preg_match('/^[a-f0-9]{32,64}$/', $cookie)) {
            $_SESSION['genesis_player_token'] = $cookie;

            return $cookie;
        }
        $token = bin2hex(random_bytes(16));
        $_SESSION['genesis_player_token'] = $token;
        if (PHP_SAPI !== 'cli' && !headers_sent()) {
            setcookie('genesis_player', $token, [
                'expires' => time() + 60 * 60 * 24 * 400,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
        $_COOKIE['genesis_player'] = $token;

        return $token;
    }

    public static function ensurePlayer(string $token): int
    {
        $pdo = Database::pdo();
        $now = date('c');
        $stmt = $pdo->prepare('SELECT id FROM players WHERE token = ?');
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        if ($row) {
            $pdo->prepare('UPDATE players SET last_seen_at = ? WHERE id = ?')->execute([$now, (int) $row['id']]);

            return (int) $row['id'];
        }
        $pdo->prepare('INSERT INTO players (token, created_at, last_seen_at) VALUES (?, ?, ?)')
            ->execute([$token, $now, $now]);

        return (int) $pdo->lastInsertId();
    }

    public static function currentPlayerId(): int
    {
        return self::ensurePlayer(self::playerToken());
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function listGames(?int $playerId = null): array
    {
        $playerId ??= self::currentPlayerId();
        $stmt = Database::pdo()->prepare(
            'SELECT id, label, mve_score, species_count, tutorial_step, baie_unlocked, is_active, created_at, updated_at
             FROM games WHERE player_id = ? ORDER BY updated_at DESC LIMIT 40'
        );
        $stmt->execute([$playerId]);
        $out = [];
        foreach ($stmt->fetchAll() as $row) {
            $out[] = [
                'id' => (string) $row['id'],
                'label' => (string) $row['label'],
                'saved_at' => (string) $row['updated_at'],
                'mve_score' => (string) $row['mve_score'],
                'species' => (int) $row['species_count'],
                'tutorial_step' => (string) ($row['tutorial_step'] ?? ''),
                'baie_unlocked' => !empty($row['baie_unlocked']),
                'is_active' => !empty($row['is_active']),
            ];
        }

        return $out;
    }

    public static function load(int $gameId, ?int $playerId = null): array
    {
        $playerId ??= self::currentPlayerId();
        $stmt = Database::pdo()->prepare(
            'SELECT state_json FROM games WHERE id = ? AND player_id = ?'
        );
        $stmt->execute([$gameId, $playerId]);
        $row = $stmt->fetch();
        if (!$row) {
            throw new \RuntimeException('Partie introuvable.');
        }
        $state = json_decode((string) $row['state_json'], true);
        if (!is_array($state)) {
            throw new \RuntimeException('Partie corrompue.');
        }

        return self::sanitizeState($state);
    }

    public static function create(array $state, string $label = '', bool $setActive = true): int
    {
        $playerId = self::currentPlayerId();
        $pdo = Database::pdo();
        if ($setActive) {
            $pdo->prepare('UPDATE games SET is_active = 0 WHERE player_id = ?')->execute([$playerId]);
        }
        $meta = self::meta($state);
        $now = date('c');
        $label = $label !== '' ? $label : ('Institut — ' . date('d/m H:i'));
        $pdo->prepare(
            'INSERT INTO games (player_id, label, state_json, mve_score, species_count, tutorial_step, baie_unlocked, is_active, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute([
            $playerId,
            $label,
            json_encode(self::sanitizeState($state), JSON_UNESCAPED_UNICODE),
            $meta['mve_score'],
            $meta['species_count'],
            $meta['tutorial_step'],
            $meta['baie_unlocked'],
            $setActive ? 1 : 0,
            $now,
            $now,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $gameId, array $state, ?string $label = null): void
    {
        $playerId = self::currentPlayerId();
        $meta = self::meta($state);
        $now = date('c');
        if ($label !== null && $label !== '') {
            Database::pdo()->prepare(
                'UPDATE games SET label = ?, state_json = ?, mve_score = ?, species_count = ?, tutorial_step = ?, baie_unlocked = ?, updated_at = ?
                 WHERE id = ? AND player_id = ?'
            )->execute([
                $label,
                json_encode(self::sanitizeState($state), JSON_UNESCAPED_UNICODE),
                $meta['mve_score'],
                $meta['species_count'],
                $meta['tutorial_step'],
                $meta['baie_unlocked'],
                $now,
                $gameId,
                $playerId,
            ]);
        } else {
            Database::pdo()->prepare(
                'UPDATE games SET state_json = ?, mve_score = ?, species_count = ?, tutorial_step = ?, baie_unlocked = ?, updated_at = ?
                 WHERE id = ? AND player_id = ?'
            )->execute([
                json_encode(self::sanitizeState($state), JSON_UNESCAPED_UNICODE),
                $meta['mve_score'],
                $meta['species_count'],
                $meta['tutorial_step'],
                $meta['baie_unlocked'],
                $now,
                $gameId,
                $playerId,
            ]);
        }
    }

    public static function setActive(int $gameId): void
    {
        $playerId = self::currentPlayerId();
        $pdo = Database::pdo();
        $pdo->prepare('UPDATE games SET is_active = 0 WHERE player_id = ?')->execute([$playerId]);
        $pdo->prepare('UPDATE games SET is_active = 1, updated_at = ? WHERE id = ? AND player_id = ?')
            ->execute([date('c'), $gameId, $playerId]);
    }

    public static function delete(int $gameId): bool
    {
        $playerId = self::currentPlayerId();
        $stmt = Database::pdo()->prepare('DELETE FROM games WHERE id = ? AND player_id = ?');
        $stmt->execute([$gameId, $playerId]);

        return $stmt->rowCount() > 0;
    }

    public static function findActiveId(?int $playerId = null): ?int
    {
        $playerId ??= self::currentPlayerId();
        $stmt = Database::pdo()->prepare(
            'SELECT id FROM games WHERE player_id = ? AND is_active = 1 ORDER BY updated_at DESC LIMIT 1'
        );
        $stmt->execute([$playerId]);
        $row = $stmt->fetch();

        return $row ? (int) $row['id'] : null;
    }

    /**
     * @return array{mve_score:string,species_count:int,tutorial_step:?string,baie_unlocked:int}
     */
    private static function meta(array $state): array
    {
        $mve = MveEvaluator::evaluate($state);

        return [
            'mve_score' => (string) ($mve['score'] ?? '—'),
            'species_count' => count(GameQueries::analyzedCreatures($state)),
            'tutorial_step' => (string) ($state['tutorial']['step'] ?? ''),
            'baie_unlocked' => !empty($state['export']['baie_unlocked']) ? 1 : 0,
        ];
    }

    private static function sanitizeState(array $state): array
    {
        if (isset($state['ui']['force_mission_outcome'])) {
            $state['ui']['force_mission_outcome'] = null;
        }
        unset($state['view']);

        return $state;
    }
}
