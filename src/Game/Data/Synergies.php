<?php

declare(strict_types=1);

namespace Genesis\Game\Data;

/**
 * Synergies narratives de croisement (paires de biomes).
 * Clé = biomes triés alphabétiquement joints par |.
 */
final class Synergies
{
    public static function all(): array
    {
        return [
            'chaleur|froid' => [
                'id' => 'thermocline',
                'title' => 'Thermocline vivante',
                'ability' => 'Choc de frontière',
                'stat_bonus' => ['resistance' => 1, 'intelligence' => 1, 'vitesse' => 1],
                'flavor' => 'Là où le chaud rencontre le froid, le vivant invente une frontière qui ne choisit pas — une ligne de battement entre deux peaux du berceau.',
                'chronicle' => 'Synergie Thermocline : deux climats opposés ont forcé une forme nouvelle. On s’en souvient comme d’un seuil, pas d’un accident.',
            ],
            'chaleur|feu' => [
                'id' => 'brasier',
                'title' => 'Brasier unifié',
                'ability' => 'Souffle de scorie vive',
                'stat_bonus' => ['force' => 2],
                'flavor' => 'La plaine et la scorie ne font plus qu’un foyer — trop vivant pour s’éteindre.',
                'chronicle' => 'Synergie Brasier : la chaleur du berceau a reconnu le feu de scorie.',
            ],
            'abysse|froid' => [
                'id' => 'cryofosse',
                'title' => 'Abysse gelée',
                'ability' => 'Silence de profondeur',
                'stat_bonus' => ['intelligence' => 2],
                'flavor' => 'Sous la canopée froide et dans la fosse noire, la lumière apprend à se taire.',
                'chronicle' => 'Synergie Abysse gelée : froid et profondeur ont inventé une intelligence opaque.',
            ],
            'feu|minéral' => [
                'id' => 'verre_scorie',
                'title' => 'Verre de scorie',
                'ability' => 'Carapace vitrifiée',
                'stat_bonus' => ['resistance' => 2],
                'flavor' => 'Le sel et le magma se figent en une armure que le berceau n’avait jamais cataloguée.',
                'chronicle' => 'Synergie Verre de scorie : minéral et feu ont solidifié l’impossible.',
            ],
            'abysse|chaleur' => [
                'id' => 'lanterne_lave',
                'title' => 'Lanternes de lave',
                'ability' => 'Phare de magma',
                'stat_bonus' => ['intelligence' => 1, 'force' => 1],
                'flavor' => 'Des lumières d’abîme brûlent comme des échos de plaine — le noir n’est plus aveugle.',
                'chronicle' => 'Synergie Lanternes de lave : abysse et chaleur ont allumé un regard.',
            ],
            'froid|minéral' => [
                'id' => 'givre_sel',
                'title' => 'Givre de sel',
                'ability' => 'Cristal mordant',
                'stat_bonus' => ['vitesse' => 1, 'resistance' => 1],
                'flavor' => 'Le gel sculpte le sel en lames ; la crête devient une tempête immobile.',
                'chronicle' => 'Synergie Givre de sel : froid et minéral ont tranché le silence.',
            ],
            'abysse|minéral' => [
                'id' => 'pilier_noir',
                'title' => 'Piliers noirs',
                'ability' => 'Ancrage d’abîme',
                'stat_bonus' => ['resistance' => 1, 'intelligence' => 1],
                'flavor' => 'Des colonnes de sel descendent dans la fosse — le fond tient le ciel.',
                'chronicle' => 'Synergie Piliers noirs : abysse et minéral ont bâti sans architecte.',
            ],
            'feu|froid' => [
                'id' => 'vapeur_vive',
                'title' => 'Vapeur vive',
                'ability' => 'Nuée brûlante',
                'stat_bonus' => ['vitesse' => 2],
                'flavor' => 'Magma et brume se heurtent : le mouvement naît de la contradiction.',
                'chronicle' => 'Synergie Vapeur vive : feu et froid ont refusé la stase.',
            ],
            'chaleur|minéral' => [
                'id' => 'dune_armure',
                'title' => 'Dune d’armure',
                'ability' => 'Peau de crête chaude',
                'stat_bonus' => ['force' => 1, 'resistance' => 1],
                'flavor' => 'L’écho de la plaine s’incruste dans le sel : le sol lui-même devient compagnon.',
                'chronicle' => 'Synergie Dune d’armure : chaleur et minéral ont solidifié la course.',
            ],
            'abysse|feu' => [
                'id' => 'forge_nocturne',
                'title' => 'Forge nocturne',
                'ability' => 'Étincelle des fosses',
                'stat_bonus' => ['force' => 1, 'intelligence' => 1],
                'flavor' => 'Au fond sans soleil, le feu n’a plus besoin du jour pour forger.',
                'chronicle' => 'Synergie Forge nocturne : abysse et feu ont créé sans aube.',
            ],
            // Spore
            'chaleur|spore' => [
                'id' => 'fermentation',
                'title' => 'Fermentation d’écho',
                'ability' => 'Souffle sporulé chaud',
                'stat_bonus' => ['intelligence' => 1, 'resistance' => 1],
                'flavor' => 'La chaleur accélère le réseau : le marais se souvient plus vite.',
                'chronicle' => 'Synergie Fermentation : spore et chaleur ont cultivé une mémoire vivante.',
            ],
            'froid|spore' => [
                'id' => 'moisissure_gel',
                'title' => 'Moisissure de givre',
                'ability' => 'Voile de spores gelées',
                'stat_bonus' => ['resistance' => 2],
                'flavor' => 'Sous le froid, le mycélium durcit sans mourir — une patience de glace.',
                'chronicle' => 'Synergie Moisissure de givre : spore et froid ont appris la lenteur.',
            ],
            'abysse|spore' => [
                'id' => 'reseau_noir',
                'title' => 'Réseau noir',
                'ability' => 'Filaments d’abîme',
                'stat_bonus' => ['intelligence' => 2],
                'flavor' => 'Les fosses et les spores tissent une toile que la lumière n’explique pas.',
                'chronicle' => 'Synergie Réseau noir : abysse et spore ont relié l’invisible.',
            ],
            'feu|spore' => [
                'id' => 'cendre_vivante',
                'title' => 'Cendre vivante',
                'ability' => 'Germe de scorie',
                'stat_bonus' => ['force' => 1, 'intelligence' => 1],
                'flavor' => 'Même brûlé, le réseau recommence — la scorie devient terreau.',
                'chronicle' => 'Synergie Cendre vivante : feu et spore ont refusé l’extinction.',
            ],
            // Vent
            'chaleur|vent' => [
                'id' => 'simoun',
                'title' => 'Simoun d’écho',
                'ability' => 'Rafale brûlante',
                'stat_bonus' => ['vitesse' => 2],
                'flavor' => 'La plaine s’envole : le courant porte la chaleur plus loin que les pieds.',
                'chronicle' => 'Synergie Simoun : vent et chaleur ont élargi le berceau.',
            ],
            'froid|vent' => [
                'id' => 'bise',
                'title' => 'Bise tranchante',
                'ability' => 'Lame de givre aérien',
                'stat_bonus' => ['vitesse' => 1, 'intelligence' => 1],
                'flavor' => 'Le vent froid ne caresse pas : il lit le monde en le coupant.',
                'chronicle' => 'Synergie Bise : vent et froid ont affûté le regard.',
            ],
            'minéral|vent' => [
                'id' => 'sable_volant',
                'title' => 'Sable volant',
                'ability' => 'Nuée de cristaux',
                'stat_bonus' => ['force' => 1, 'vitesse' => 1],
                'flavor' => 'Le sel quitte la crête et devient tempête — le minéral apprend à courir.',
                'chronicle' => 'Synergie Sable volant : vent et minéral ont levé le sol.',
            ],
            'spore|vent' => [
                'id' => 'pollinisation',
                'title' => 'Pollinisation forcée',
                'ability' => 'Nuage de spores portées',
                'stat_bonus' => ['intelligence' => 1, 'vitesse' => 1],
                'flavor' => 'Le marais voyage : chaque rafale est une lettre fongique.',
                'chronicle' => 'Synergie Pollinisation : vent et spore ont disséminé la thèse.',
            ],
            'abysse|vent' => [
                'id' => 'souffle_fosses',
                'title' => 'Souffle des fosses',
                'ability' => 'Courant d’ombre',
                'stat_bonus' => ['vitesse' => 1, 'intelligence' => 1],
                'flavor' => 'Même au fond, un courant existe — le noir n’est pas immobile.',
                'chronicle' => 'Synergie Souffle des fosses : vent et abysse ont troublé le silence.',
            ],
            'feu|vent' => [
                'id' => 'tempete_scorie',
                'title' => 'Tempête de scorie',
                'ability' => 'Étincelles portées',
                'stat_bonus' => ['force' => 1, 'vitesse' => 2],
                'flavor' => 'Le feu ne reste plus au sol : le vent en fait une météo.',
                'chronicle' => 'Synergie Tempête de scorie : feu et vent ont quitté le cratère.',
            ],
            // Paire manquante — signature mémorable
            'minéral|spore' => [
                'id' => 'sel_mycelien',
                'title' => 'Sel mycélien',
                'ability' => 'Racines de cristal',
                'stat_bonus' => ['resistance' => 2, 'intelligence' => 1],
                'flavor' => 'Le marais mange le sel : chaque filament devient une veine de pierre qui se souvient.',
                'chronicle' => 'Synergie Sel mycélien : spore et minéral ont fossilisé la mémoire.',
            ],
            // Variante narrative renforcée (clé déjà rare émotionnellement)
            // chaleur|froid déjà = Thermocline — on renforce le flavor via entry existante
        ];
    }

    public static function forBiomes(string $biomeA, string $biomeB): ?array
    {
        $biomeA = trim($biomeA);
        $biomeB = trim($biomeB);
        if ($biomeA === '' || $biomeB === '' || $biomeA === $biomeB) {
            return null;
        }

        $parts = [$biomeA, $biomeB];
        sort($parts, SORT_STRING);
        $key = $parts[0] . '|' . $parts[1];

        return self::all()[$key] ?? null;
    }

    public static function genericMix(string $biomeA, string $biomeB): array
    {
        return [
            'id' => 'echo_mixte',
            'title' => 'Écho mixte',
            'ability' => 'Écho mixte',
            'stat_bonus' => [],
            'flavor' => sprintf(
                'Deux écologies (%s × %s) se rencontrent sans rituel nommé — le vivant improvise.',
                $biomeA,
                $biomeB
            ),
            'chronicle' => sprintf('Synergie improvisée : %s × %s.', $biomeA, $biomeB),
        ];
    }

    /**
     * Codex complet : entrées nommées + statut découvert.
     *
     * @param list<string> $discoveredIds
     * @return list<array<string, mixed>>
     */
    public static function codex(array $discoveredIds = []): array
    {
        $discovered = array_fill_keys($discoveredIds, true);
        $entries = [];
        foreach (self::all() as $pairKey => $syn) {
            $biomes = explode('|', $pairKey);
            $entries[] = [
                'id' => $syn['id'],
                'title' => $syn['title'],
                'ability' => $syn['ability'],
                'stat_bonus' => $syn['stat_bonus'] ?? [],
                'flavor' => $syn['flavor'],
                'chronicle' => $syn['chronicle'],
                'biomes' => $biomes,
                'pair_key' => $pairKey,
                'discovered' => isset($discovered[$syn['id']]),
            ];
        }

        usort($entries, static function (array $a, array $b): int {
            if ($a['discovered'] !== $b['discovered']) {
                return $a['discovered'] ? -1 : 1;
            }

            return strcmp((string) $a['title'], (string) $b['title']);
        });

        return $entries;
    }

    public static function totalNamed(): int
    {
        return count(self::all());
    }
}
