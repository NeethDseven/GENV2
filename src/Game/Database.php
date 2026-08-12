<?php

declare(strict_types=1);

namespace Genesis\Game;

use PDO;
use PDOException;

/**
 * Connexion PDO unique + migration schéma.
 */
final class Database
{
    private static ?PDO $pdo = null;
    private static string $driver = 'sqlite';

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $config = self::config();
        $want = (string) ($config['driver'] ?? 'auto');

        if ($want === 'mysql' || $want === 'auto') {
            try {
                self::$pdo = self::connectMysql($config['mysql'] ?? []);
                self::$driver = 'mysql';
                self::migrate(self::$pdo, 'mysql');

                return self::$pdo;
            } catch (\Throwable $e) {
                if ($want === 'mysql') {
                    throw $e;
                }
                // auto → sqlite
            }
        }

        self::$pdo = self::connectSqlite($config['sqlite']['path'] ?? '');
        self::$driver = 'sqlite';
        self::migrate(self::$pdo, 'sqlite');

        return self::$pdo;
    }

    public static function driver(): string
    {
        self::pdo();

        return self::$driver;
    }

    public static function resetConnection(): void
    {
        self::$pdo = null;
        self::$driver = 'sqlite';
    }

    private static function config(): array
    {
        $path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
        if (is_file($path)) {
            $cfg = require $path;

            return is_array($cfg) ? $cfg : [];
        }

        return ['driver' => 'sqlite', 'sqlite' => ['path' => dirname(__DIR__, 2) . '/storage/genesis.sqlite']];
    }

    private static function connectSqlite(string $path): PDO
    {
        if ($path === '') {
            $path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'genesis.sqlite';
        }
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $pdo = new PDO('sqlite:' . $path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON');

        return $pdo;
    }

    private static function connectMysql(array $cfg): PDO
    {
        $host = (string) ($cfg['host'] ?? '127.0.0.1');
        $port = (int) ($cfg['port'] ?? 3306);
        $db = (string) ($cfg['database'] ?? 'genesis');
        $user = (string) ($cfg['user'] ?? 'root');
        $pass = (string) ($cfg['password'] ?? '');
        $charset = (string) ($cfg['charset'] ?? 'utf8mb4');

        // Créer la base si absente
        $serverDsn = sprintf('mysql:host=%s;port=%d;charset=%s', $host, $port, $charset);
        $server = new PDO($serverDsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $server->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '``', $db) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $db, $charset);

        return new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    private static function migrate(PDO $pdo, string $driver): void
    {
        if ($driver === 'mysql') {
            $pdo->exec(
                'CREATE TABLE IF NOT EXISTS players (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    token VARCHAR(64) NOT NULL UNIQUE,
                    created_at DATETIME NOT NULL,
                    last_seen_at DATETIME NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
            );
            $pdo->exec(
                'CREATE TABLE IF NOT EXISTS games (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    player_id INT UNSIGNED NOT NULL,
                    label VARCHAR(120) NOT NULL,
                    state_json LONGTEXT NOT NULL,
                    mve_score VARCHAR(16) NOT NULL DEFAULT \'—\',
                    species_count INT UNSIGNED NOT NULL DEFAULT 0,
                    tutorial_step VARCHAR(64) NULL,
                    baie_unlocked TINYINT(1) NOT NULL DEFAULT 0,
                    is_active TINYINT(1) NOT NULL DEFAULT 0,
                    created_at DATETIME NOT NULL,
                    updated_at DATETIME NOT NULL,
                    INDEX idx_games_player (player_id),
                    INDEX idx_games_active (player_id, is_active),
                    CONSTRAINT fk_games_player FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
            );

            return;
        }

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS players (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                token TEXT NOT NULL UNIQUE,
                created_at TEXT NOT NULL,
                last_seen_at TEXT NOT NULL
            )'
        );
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS games (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                player_id INTEGER NOT NULL,
                label TEXT NOT NULL,
                state_json TEXT NOT NULL,
                mve_score TEXT NOT NULL DEFAULT \'—\',
                species_count INTEGER NOT NULL DEFAULT 0,
                tutorial_step TEXT,
                baie_unlocked INTEGER NOT NULL DEFAULT 0,
                is_active INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL,
                FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE
            )'
        );
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_games_player ON games(player_id)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_games_active ON games(player_id, is_active)');
    }
}
