<?php

declare(strict_types=1);

namespace Genesis\Game;

/**
 * Tâches temporisées (recon, analyse, croisement, farm, craft).
 * Progression = (now - started_at) / duration.
 */
final class TaskRunner
{
    public static function now(): int
    {
        return time();
    }

    public static function start(
        array &$state,
        string $type,
        string $label,
        int $durationSec,
        array $payload = []
    ): string {
        $id = 't' . (++$state['ui']['next_task_seq']);
        $state['tasks'][$id] = [
            'id' => $id,
            'type' => $type,
            'label' => $label,
            'started_at' => self::now(),
            'duration' => max(0, $durationSec),
            'payload' => $payload,
            'status' => 'running',
        ];

        return $id;
    }

    public static function hasRunningType(array $state, string $type): bool
    {
        foreach ($state['tasks'] ?? [] as $task) {
            if (($task['status'] ?? '') === 'running' && ($task['type'] ?? '') === $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function running(array $state): array
    {
        $now = self::now();
        $out = [];
        foreach ($state['tasks'] ?? [] as $task) {
            if (($task['status'] ?? '') !== 'running') {
                continue;
            }
            $duration = max(1, (int) ($task['duration'] ?? 1));
            $elapsed = max(0, $now - (int) ($task['started_at'] ?? $now));
            $pct = min(100, (int) floor(($elapsed / $duration) * 100));
            $task['elapsed'] = $elapsed;
            $task['percent'] = $pct;
            $task['remaining'] = max(0, $duration - $elapsed);
            $task['complete'] = $elapsed >= $duration;
            $out[] = $task;
        }

        return $out;
    }

    /**
     * Complète les tâches prêtes et renvoie les types complétés avec payload.
     *
     * @return list<array<string, mixed>>
     */
    public static function collectCompleted(array &$state): array
    {
        $now = self::now();
        $done = [];
        foreach ($state['tasks'] ?? [] as $id => $task) {
            if (($task['status'] ?? '') !== 'running') {
                continue;
            }
            $duration = max(0, (int) ($task['duration'] ?? 1));
            $started = (int) ($task['started_at'] ?? $now);
            if ($duration > 0 && ($now - $started) < $duration) {
                continue;
            }
            $state['tasks'][$id]['status'] = 'done';
            $state['tasks'][$id]['finished_at'] = $now;
            $done[] = $state['tasks'][$id];
        }

        // Purge old done tasks (keep last 8)
        $all = $state['tasks'] ?? [];
        if (count($all) > 20) {
            $running = [];
            $finished = [];
            foreach ($all as $id => $t) {
                if (($t['status'] ?? '') === 'running') {
                    $running[$id] = $t;
                } else {
                    $finished[$id] = $t;
                }
            }
            $finished = array_slice($finished, -8, null, true);
            $state['tasks'] = $running + $finished;
        }

        return $done;
    }
}
