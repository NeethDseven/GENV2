<?php

declare(strict_types=1);

namespace Genesis\Game;

use Genesis\Game\Data\Catalog;
use Genesis\Game\Data\Synergies;

final class CreatureFactory
{
    public static function blank(string $id): array
    {
        return [
            'id' => $id,
            'name' => 'Aucun spécimen actif',
            'species' => '—',
            'species_key' => null,
            'rarity' => '—',
            'stats' => [
                'force' => 0,
                'vitesse' => 0,
                'resistance' => 0,
                'intelligence' => 0,
            ],
            'abilities' => [],
            'purity' => 100,
            'discovered' => false,
            'analyzed' => false,
            'knowledge' => 0, // 0–100 : ce que l’institut a compris (UI) ; effets gameplay = stats réelles
            'mutated' => false,
            'mission_done' => false,
            'status' => 'prête',
            'parents' => null,
            'zone_id' => null,
            'is_hybrid' => false,
        ];
    }

    public static function fromDiscovery(string $id, string $speciesKey, string $zoneId): array
    {
        $species = Catalog::speciesByKey($speciesKey);
        $creature = self::blank($id);
        $creature['discovered'] = true;
        $creature['knowledge'] = 5;
        $creature['species_key'] = $speciesKey;
        $creature['zone_id'] = $zoneId;
        $creature['name'] = 'Signal non catalogué';
        $creature['species'] = 'Inconnue';
        $creature['rarity'] = 'inconnue';
        // Stats réelles déjà présentes (cachées UI) pour cohérence si besoin
        if ($species) {
            $creature['stats'] = $species['stats'];
            $creature['abilities'] = $species['abilities'];
            $creature['biome'] = $species['biome'] ?? null;
        }

        return $creature;
    }

    public static function applyAnalysis(array $creature): array
    {
        $species = Catalog::speciesByKey((string) ($creature['species_key'] ?? ''));
        if ($species === null) {
            return $creature;
        }

        $creature['analyzed'] = true;
        $creature['discovered'] = true;
        $creature['species'] = $species['species'];
        $creature['rarity'] = $species['rarity'];
        $creature['name'] = $species['name'];
        $creature['stats'] = $species['stats'];
        $creature['abilities'] = $species['abilities'];
        $creature['biome'] = $species['biome'] ?? null;
        // Première analyse : identité posée, pas encore la fiche complète
        $creature['knowledge'] = max(35, (int) ($creature['knowledge'] ?? 0));

        return $creature;
    }

    /** Approfondit l’étude (connaissance UI uniquement). */
    public static function deepenKnowledge(array $creature, int $gain = 28): array
    {
        $k = (int) ($creature['knowledge'] ?? 0);
        $creature['knowledge'] = min(100, $k + max(1, $gain));

        return $creature;
    }

    public static function knowledge(array $creature): int
    {
        return max(0, min(100, (int) ($creature['knowledge'] ?? 0)));
    }

    /** Ce que l’UI peut afficher selon la connaissance. */
    public static function visibility(array $creature): array
    {
        $k = self::knowledge($creature);
        $analyzed = !empty($creature['analyzed']);

        return [
            'identity' => $analyzed || $k >= 20,       // nom / espèce
            'biome' => $analyzed || $k >= 25,
            'rarity' => $k >= 45,
            'purity' => $k >= 50,
            'status' => true,
            'stats' => $k >= 60,
            'abilities' => $k >= 78,
            'synergy' => $k >= 70 && !empty($creature['is_hybrid']),
            'complete' => $k >= 100,
            'knowledge' => $k,
        ];
    }

    public static function displayName(array $creature): string
    {
        $vis = self::visibility($creature);
        if (!$vis['identity']) {
            return (string) ($creature['name'] ?? 'Signal non catalogué');
        }

        return (string) ($creature['name'] ?? '—');
    }

    public static function displaySpecies(array $creature): string
    {
        $vis = self::visibility($creature);
        if (!$vis['identity']) {
            return '???';
        }

        return (string) ($creature['species'] ?? '—');
    }

    /**
     * Aperçu / création d’hybride (même formule + synergies narratives).
     */
    public static function hybridPreview(array $parentA, array $parentB): array
    {
        $statsA = $parentA['stats'] ?? [];
        $statsB = $parentB['stats'] ?? [];
        $hybridStats = [
            'force' => intdiv(((int) ($statsA['force'] ?? 0) + (int) ($statsB['force'] ?? 0)), 2) + 1,
            'vitesse' => intdiv(((int) ($statsA['vitesse'] ?? 0) + (int) ($statsB['vitesse'] ?? 0)), 2) + 1,
            'resistance' => intdiv(((int) ($statsA['resistance'] ?? 0) + (int) ($statsB['resistance'] ?? 0)), 2) + 1,
            'intelligence' => intdiv(((int) ($statsA['intelligence'] ?? 0) + (int) ($statsB['intelligence'] ?? 0)), 2) + 1,
        ];

        $biomeA = self::biomeOf($parentA);
        $biomeB = self::biomeOf($parentB);
        $biomeMix = $biomeA !== '' && $biomeB !== '' && $biomeA !== $biomeB;

        $synergy = null;
        if ($biomeMix) {
            foreach (['force', 'vitesse', 'resistance', 'intelligence'] as $stat) {
                $hybridStats[$stat] += 1;
            }
            $synergy = Synergies::forBiomes($biomeA, $biomeB)
                ?? Synergies::genericMix($biomeA, $biomeB);
            foreach ($synergy['stat_bonus'] ?? [] as $stat => $delta) {
                $hybridStats[$stat] = (int) ($hybridStats[$stat] ?? 0) + (int) $delta;
            }
        }

        $abilityA = $parentA['abilities'][0] ?? null;
        $abilityB = $parentB['abilities'][0] ?? null;
        $abilities = array_values(array_unique(array_filter([$abilityA, $abilityB])));
        if ($synergy !== null && !empty($synergy['ability'])) {
            if (!in_array($synergy['ability'], $abilities, true)) {
                $abilities[] = $synergy['ability'];
            }
        } elseif ($biomeMix && count($abilities) < 2) {
            $abilities[] = 'Écho mixte';
        }
        $abilities = array_slice($abilities !== [] ? $abilities : ['Écho hybride'], 0, 2);

        $nameA = (string) ($parentA['species'] ?? 'Alpha');
        $nameB = (string) ($parentB['species'] ?? 'Beta');
        $hybridSpecies = mb_convert_case(
            mb_substr($nameA, 0, 4) . mb_strtolower(mb_substr($nameB, 0, 4)),
            MB_CASE_TITLE,
            'UTF-8'
        );

        if ($synergy !== null && ($synergy['id'] ?? '') !== 'echo_mixte') {
            // Nom affiché : espèce compacte + titre de synergie
            $displayName = $hybridSpecies . ' — ' . ($synergy['title'] ?? '');
        } else {
            $displayName = $hybridSpecies;
        }

        $note = $biomeMix
            ? (string) ($synergy['flavor'] ?? sprintf('Synergie biomes (%s × %s).', $biomeA, $biomeB))
            : 'Même famille écologique — croisement stable, sans synergie narrative.';

        if ($synergy !== null && !empty($synergy['ability']) && ($synergy['id'] ?? '') !== 'echo_mixte') {
            $note .= ' Capacité : ' . $synergy['ability'] . '.';
        }
        $bonusBits = [];
        foreach ($synergy['stat_bonus'] ?? [] as $stat => $delta) {
            $bonusBits[] = strtoupper(substr((string) $stat, 0, 1)) . '+' . (int) $delta;
        }
        if ($bonusBits !== []) {
            $note .= ' Bonus : ' . implode(' ', $bonusBits) . '.';
        }

        return [
            'name' => $displayName,
            'species' => $hybridSpecies,
            'rarity' => 'unique',
            'stats' => $hybridStats,
            'abilities' => $abilities,
            'purity' => 70,
            'parents' => [$nameA, $nameB],
            'biome_mix' => $biomeMix,
            'synergy' => $synergy,
            'synergy_title' => $synergy['title'] ?? null,
            'synergy_id' => $synergy['id'] ?? null,
            'synergy_ability' => $synergy['ability'] ?? null,
            'note' => $note,
            'chronicle' => $synergy['chronicle'] ?? null,
        ];
    }

    public static function hybrid(string $id, array $parentA, array $parentB): array
    {
        $preview = self::hybridPreview($parentA, $parentB);

        $biomeA = self::biomeOf($parentA);
        $biomeB = self::biomeOf($parentB);
        $biomeMix = !empty($preview['biome_mix']);

        return [
            'id' => $id,
            'name' => $preview['name'],
            'species' => $preview['species'],
            'species_key' => null,
            'rarity' => $preview['rarity'],
            'stats' => $preview['stats'],
            'abilities' => $preview['abilities'],
            'purity' => $preview['purity'],
            'biome' => $biomeMix
                ? ($biomeA !== '' && $biomeB !== '' ? $biomeA : ($biomeA !== '' ? $biomeA : $biomeB))
                : ($biomeA !== '' ? $biomeA : $biomeB),
            'discovered' => true,
            'analyzed' => true,
            // Forme créée ici : mieux connue dès la naissance, pas encore exhaustive
            'knowledge' => 55,
            'mutated' => false,
            'mission_done' => false,
            'status' => 'prête',
            'parents' => $preview['parents'],
            'zone_id' => null,
            'is_hybrid' => true,
            'biome_mix' => $preview['biome_mix'],
            'synergy_id' => $preview['synergy_id'] ?? null,
            'synergy_title' => $preview['synergy_title'] ?? null,
            'synergy_flavor' => $preview['note'] ?? null,
        ];
    }

    private static function biomeOf(array $creature): string
    {
        if (!empty($creature['biome'])) {
            return (string) $creature['biome'];
        }
        $key = (string) ($creature['species_key'] ?? '');
        if ($key === '') {
            return '';
        }
        $species = Catalog::speciesByKey($key);

        return is_array($species) ? (string) ($species['biome'] ?? '') : '';
    }

    public static function label(array $creature): string
    {
        if (empty($creature['discovered']) && empty($creature['analyzed'])) {
            return 'Aucun spécimen actif';
        }
        if (empty($creature['analyzed'])) {
            return 'Signal non catalogué';
        }

        $vis = self::visibility($creature);
        $parts = [self::displayName($creature), self::displaySpecies($creature)];
        if ($vis['rarity']) {
            $parts[] = (string) ($creature['rarity'] ?? '—');
        }
        if ($vis['stats']) {
            $stats = $creature['stats'] ?? [];
            $parts[] = sprintf(
                'F%d V%d R%d I%d',
                (int) ($stats['force'] ?? 0),
                (int) ($stats['vitesse'] ?? 0),
                (int) ($stats['resistance'] ?? 0),
                (int) ($stats['intelligence'] ?? 0)
            );
        } else {
            $parts[] = 'lecture en cours';
        }
        $parts[] = self::knowledge($creature) . '% connu';

        return implode(' · ', $parts);
    }
}
