<?php

declare(strict_types=1);

namespace Genesis\Game;

/**
 * Façade de sauvegarde — BDD (GameRepository).
 * API conservée pour les tests / GameEngine.
 */
final class SaveStore
{
    /**
     * @return list<array{id:string,label:string,saved_at:string,mve_score:string,species:int}>
     */
    public static function list(): array
    {
        try {
            return GameRepository::listGames();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * @return array{id:string,label:string,saved_at:string,path?:string}
     */
    public static function save(array $state, string $label = ''): array
    {
        $id = GameRepository::create($state, $label, false);
        $list = GameRepository::listGames();
        $meta = null;
        foreach ($list as $g) {
            if ((string) $g['id'] === (string) $id) {
                $meta = $g;
                break;
            }
        }

        return [
            'id' => (string) $id,
            'label' => (string) ($meta['label'] ?? $label),
            'saved_at' => (string) ($meta['saved_at'] ?? date('c')),
            'path' => 'db:games/' . $id,
        ];
    }

    public static function load(string $id): array
    {
        return GameRepository::load((int) $id);
    }

    public static function delete(string $id): bool
    {
        return GameRepository::delete((int) $id);
    }
}
