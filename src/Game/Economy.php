<?php

declare(strict_types=1);

namespace Genesis\Game;

/**
 * Coûts et gains d’acte — équilibrage playtest.
 */
final class Economy
{
    public static function reconCost(array $zone, int $droneCount = 1): int
    {
        $base = match ((string) ($zone['risk'] ?? 'modéré')) {
            'extrême' => 3,
            'élevé' => 2,
            default => 1,
        };
        $droneCount = max(1, min(5, $droneCount));
        // Drones supplémentaires : +1 log chacun (effort de coordination)
        return $base + ($droneCount - 1);
    }

    /** Réappro logistique (timer) : coût biomasse → gain logistique. */
    public static function restockBiomassCost(): int
    {
        return 2;
    }

    public static function restockLogisticsGain(): int
    {
        return 2;
    }

    public static function crossCost(): int
    {
        return 1;
    }

    /** Coût mutation en prélèvements (pas d’organique). */
    public static function mutateCost(): int
    {
        return 1;
    }

    /** @deprecated Synthèse prélèvements → organique retirée (org = mission terrain). */
    public static function refineSamplesCost(): int
    {
        return 3;
    }

    /** @deprecated */
    public static function refineOrganicFromSamples(): int
    {
        return 1;
    }

    /** Synthèse organique optionnelle : biomasse → organique. */
    public static function refineBiomassCost(): int
    {
        return 4;
    }

    public static function refineOrganicFromBiomass(): int
    {
        return 1;
    }

    /**
     * Infos HUD ressources (label court + survol data-tip).
     *
     * @return list<array{key:string,label:string,value:int,tip:string}>
     */
    public static function resourceTips(array $resources): array
    {
        $o = (int) ($resources['organic'] ?? 0);
        $b = (int) ($resources['biomass'] ?? 0);
        $s = (int) ($resources['samples'] ?? 0);
        $l = (int) ($resources['logistics'] ?? 0);

        return [
            [
                'key' => 'organic',
                'label' => 'Organique',
                'value' => $o,
                'tip' => "Essence du vivant — croiser, soigner.\n"
                    . "Uniquement via mission terrain réussie d’un spécimen.\n"
                    . "Jamais par exploration ni capture.",
            ],
            [
                'key' => 'biomass',
                'label' => 'Biomasse',
                'value' => $b,
                'tip' => "Chair du monde — assembler drones, réappro logistique.\n"
                    . "Récolte à l’Institut, butin d’exploration.",
            ],
            [
                'key' => 'samples',
                'label' => 'Prélèvements',
                'value' => $s,
                'tip' => "Fragments de terrain — nourrissent la mutation.\n"
                    . "Exploration, escorte, capture, missions dures.",
            ],
            [
                'key' => 'logistics',
                'label' => 'Logistique',
                'value' => $l,
                'tip' => "Souffle des expéditions — chaque sortie en consomme.\n"
                    . "Réappro, musée, exploration.",
            ],
        ];
    }

    /**
     * Chance de réussite d’une mutation (échec = perte du spécimen).
     * Drones d’analyse : stabilisent (+% par drone alloué).
     */
    public static function mutateSuccessChance(array $creature, int $studyDrones = 0): int
    {
        $stats = $creature['stats'] ?? [];
        $intel = (int) ($stats['intelligence'] ?? 0);
        $purity = (int) ($creature['purity'] ?? 100);
        $base = 58 + min(12, intdiv($intel, 2));
        if ($purity < 60) {
            $base -= 20;
        } elseif ($purity < 80) {
            $base -= 10;
        } elseif ($purity >= 95) {
            $base += 4;
        }
        if (!empty($creature['is_hybrid'])) {
            $base -= 8;
        }
        if (!empty($creature['mutated'])) {
            return 0;
        }
        $studyDrones = max(0, min(5, $studyDrones));
        // Chaque drone d’analyse : +7 % (plafond fort mais pas garanti)
        $base += $studyDrones * 7;

        return max(18, min(94, $base));
    }

    /**
     * Chance de réussite d’un croisement (échec = pas d’hybride, organique perdu).
     * Drones d’analyse réduisent le risque d’échec.
     */
    public static function crossSuccessChance(array $state, int $studyDrones = 0): int
    {
        $studyDrones = max(0, min(5, $studyDrones));
        $base = 52 + $studyDrones * 9;
        // Bonus léger si beaucoup d’espèces connues
        $n = count(GameQueries::analyzedCreatures($state));
        $base += min(8, $n);

        return max(25, min(95, $base));
    }

    /** Texte court : ce qu’apporte la n-ième sonde d’exploration. */
    public static function reconSlotTooltip(int $n, int $chance, int $contact, int $cost): string
    {
        $lines = [
            1 => '×1 — sortie minimale. Scan lent, contact plus rare.',
            2 => '×2 — meilleur scan et contact. Coût log +1.',
            3 => '×3 — cartographie nettement plus rapide.',
            4 => '×4 — forte couverture ; utile zones risquées.',
            5 => '×5 — max. Meilleure tenue et contact, coût log max.',
        ];
        $base = $lines[$n] ?? sprintf('×%d sondes.', $n);

        return sprintf("%s\nTenue ~%d%% · contact ~%d%% · coût %d log.", $base, $chance, $contact, $cost);
    }

    /** Texte court : ce qu’apporte la n-ième unité de capture. */
    public static function captureSlotTooltip(int $n, int $chance, int $escortN = 0): string
    {
        $lines = [
            1 => '×1 — prise risquée. Un seul drone si échec est perdu.',
            2 => '×2 — nette hausse de réussite. Recommandé dès que possible.',
            3 => '×3 — prise plus sûre sur zones dures.',
            4 => '×4 — très bon filet ; peu d’échecs hors extrême.',
            5 => '×5 — max. Meilleure chance, mais plus de drones en jeu.',
        ];
        $base = $lines[$n] ?? sprintf('×%d drones de prise.', $n);
        $esc = $escortN > 0
            ? sprintf("\nCréatures d’appui : %d (bonus de réussite).", $escortN)
            : "\nAjoutez des créatures prêtes pour monter le %.";

        return sprintf("%s\nChance de capture ~%d%%%s", $base, $chance, $esc);
    }

    /** Texte court : drones d’analyse pour mutation / croisement. */
    public static function studySlotTooltip(int $n, int $mutateChance, int $crossChance): string
    {
        $lines = [
            0 => '×0 — labo nu. Mutation et croisement très risqués.',
            1 => '×1 — stabilisation légère (+% réussite).',
            2 => '×2 — bon filet contre la destruction.',
            3 => '×3 — labo renforcé ; risque nettement baissé.',
            4 => '×4 — très sûr pour muter / croiser.',
            5 => '×5 — max. Meilleure stabilité possible.',
        ];
        $base = $lines[$n] ?? sprintf('×%d drones d’analyse.', $n);

        return sprintf("%s\nMutation ~%d%% · croisement ~%d%%.", $base, $mutateChance, $crossChance);
    }

    /** Nombre max de créatures d’appui pour une capture. */
    public static function captureEscortMax(): int
    {
        return 3;
    }

    /**
     * Bonus (ou malus) d’une créature d’appui à la capture.
     *
     * @return array{bonus:int,ready:bool,label:string}
     */
    public static function captureEscortBonus(array $state, array $escort, int $slotIndex = 0): array
    {
        $creature = $state['creature'] ?? [];
        $targetBiome = mb_strtolower((string) ($creature['biome'] ?? ''));
        $zoneId = (string) ($creature['zone_id'] ?? '');
        $zone = ($zoneId !== '' && isset($state['world']['zones'][$zoneId]))
            ? $state['world']['zones'][$zoneId]
            : [];
        $zoneBiome = mb_strtolower((string) ($zone['biome'] ?? $targetBiome));
        $name = (string) ($escort['name'] ?? $escort['species'] ?? 'Appui');
        $ready = ($escort['status'] ?? '') === 'prête';

        if (!$ready) {
            return [
                'bonus' => -10,
                'ready' => false,
                'label' => $name,
            ];
        }

        $bonus = 5;
        $bonus += min(7, intdiv((int) ($escort['stats']['vitesse'] ?? 0), 3));
        $bonus += min(6, intdiv((int) ($escort['stats']['force'] ?? 0), 4));
        $bonus += min(4, intdiv((int) ($escort['stats']['resistance'] ?? 0), 5));
        if (!empty($escort['mutated'])) {
            $bonus += 3;
        }
        if (!empty($escort['is_hybrid'])) {
            $bonus += 4;
        }
        $eb = mb_strtolower((string) ($escort['biome'] ?? ''));
        $ebMain = explode('/', $eb)[0] ?? $eb;
        $zbMain = explode('/', $zoneBiome !== '' ? $zoneBiome : $targetBiome)[0] ?? '';
        $aff = GameEngine::biomeAffinityScore($ebMain, $zbMain);
        $bonus += (int) round($aff * 0.65);
        // Rendements décroissants si plusieurs appuis
        if ($slotIndex > 0) {
            $bonus = (int) round($bonus * (1.0 - 0.18 * $slotIndex));
        }

        return [
            'bonus' => max(2, $bonus),
            'ready' => true,
            'label' => $name,
        ];
    }

    /**
     * IDs des créatures d’appui pour la capture (0–3).
     *
     * @return list<string>
     */
    public static function captureEscortIds(array $state): array
    {
        $raw = $state['expedition']['capture_escorts'] ?? null;
        if (!is_array($raw) || $raw === []) {
            // rétrocompat : ancienne escorte unique
            $one = $state['expedition']['escort_creature_id'] ?? null;
            if ($one !== null && $one !== '' && $one !== 'none') {
                $raw = [(string) $one];
            } else {
                $raw = [];
            }
        }
        $out = [];
        foreach ($raw as $id) {
            $id = (string) $id;
            if ($id !== '' && !in_array($id, $out, true)) {
                $out[] = $id;
            }
            if (count($out) >= self::captureEscortMax()) {
                break;
            }
        }

        return $out;
    }

    /**
     * Capture d’un signal : drones de prise + créatures d’appui + affinité biome.
     * Plus difficile qu’une simple recon — l’échec coûte drone + ressources.
     */
    public static function captureSuccessChance(array $state, int $captureDrones = 1): int
    {
        $captureDrones = max(1, min(5, $captureDrones));
        // Base basse : 1 drone ≈ 1 chance sur 3 ; chaque drone de plus aide clairement
        $base = 28 + ($captureDrones - 1) * 12;

        $creature = $state['creature'] ?? [];
        $zoneId = (string) ($creature['zone_id'] ?? '');
        $zone = ($zoneId !== '' && isset($state['world']['zones'][$zoneId]))
            ? $state['world']['zones'][$zoneId]
            : [];
        $risk = (string) ($zone['risk'] ?? 'modéré');
        if ($risk === 'élevé') {
            $base -= 8;
        } elseif ($risk === 'extrême') {
            $base -= 14;
        }

        // Scan local : un terrain mieux lu se laisse un peu plus prendre
        if ($zone !== []) {
            $base += min(10, intdiv((int) ($zone['scan'] ?? 0), 12));
        }

        $pool = GameQueries::analyzedCreatures($state);
        $targetId = (string) ($creature['id'] ?? '');
        foreach (self::captureEscortIds($state) as $i => $escortId) {
            if ($escortId === $targetId) {
                continue; // ne pas s’appuyer sur la cible elle-même
            }
            $escort = $pool[$escortId] ?? null;
            if (!is_array($escort)) {
                continue;
            }
            $info = self::captureEscortBonus($state, $escort, $i);
            $base += $info['bonus'];
        }

        return max(12, min(88, $base));
    }

    public static function recoverCost(string $status): int
    {
        return $status === 'blessée' ? 2 : 1;
    }

    public static function exhibitLogisticsGain(): int
    {
        return 1;
    }

    public static function newExplorationLogisticsGain(): int
    {
        return 1;
    }

    /** Escorte hybride : seuil d’intelligence pour +1 logistique. */
    public static function escortIntelThreshold(): int
    {
        return 8;
    }

    public static function missionSuccessChance(array $creature): int
    {
        $stats = $creature['stats'] ?? [];
        $power = (int) ($stats['force'] ?? 0)
            + (int) ($stats['vitesse'] ?? 0)
            + (int) ($stats['resistance'] ?? 0)
            + (int) ($stats['intelligence'] ?? 0);

        $penalty = match ((string) ($creature['status'] ?? 'prête')) {
            'blessée' => 25,
            'épuisée' => 12,
            default => 0,
        };

        // Hybrides un peu plus fiables (création éprouvée)
        $bonus = !empty($creature['is_hybrid']) ? 5 : 0;
        // Pureté basse = risque
        $purity = (int) ($creature['purity'] ?? 100);
        if ($purity < 60) {
            $penalty += 8;
        } elseif ($purity < 80) {
            $penalty += 3;
        }

        return max(18, min(78, 32 + $power + $bonus - $penalty));
    }

    public static function missionSuccessOrganicGain(): int
    {
        return 1;
    }

    public static function missionExhaustedSampleGain(): int
    {
        return 1;
    }

    /**
     * Durée d’une tâche (secondes).
     * Context optionnel : risk, duration, creature (array), escort (array).
     * Stats qui accélèrent : vitesse (terrain), intelligence (labo), escorte mutée/hybride.
     * Pureté basse ralentit mission / mutation.
     */
    public static function taskDuration(string $type, array $context = []): int
    {
        // Instant pour les tests CLI
        $instant = $_ENV['GENESIS_INSTANT'] ?? getenv('GENESIS_INSTANT') ?: '';
        if ($instant === '1' || (defined('GENESIS_INSTANT') && GENESIS_INSTANT)) {
            return 0;
        }

        $base = match ($type) {
            'recon' => match ((string) ($context['risk'] ?? 'modéré')) {
                'extrême' => 18,
                'élevé' => 14,
                default => 10,
            },
            'analyze' => 8,
            'cross' => 12,
            'mutate' => 8,
            'mission' => 12,
            'farm' => 10,
            'restock' => 8,
            'craft_drone' => (int) ($context['duration'] ?? 14),
            'orbital' => 20,
            default => 10,
        };

        $mod = self::durationModifier($type, $context);

        // Plancher : toujours un peu d’attente hors tests
        return max(3, $base + $mod);
    }

    /**
     * Delta de secondes (négatif = plus rapide).
     */
    public static function durationModifier(string $type, array $context = []): int
    {
        $creature = is_array($context['creature'] ?? null) ? $context['creature'] : null;
        $escort = is_array($context['escort'] ?? null) ? $context['escort'] : null;
        $cStats = is_array($creature) ? ($creature['stats'] ?? []) : [];
        $eStats = is_array($escort) ? ($escort['stats'] ?? []) : [];

        $force = (int) ($cStats['force'] ?? 0);
        $vitesse = (int) ($cStats['vitesse'] ?? 0);
        $resistance = (int) ($cStats['resistance'] ?? 0);
        $intel = (int) ($cStats['intelligence'] ?? 0);
        $purity = (int) ($creature['purity'] ?? 100);

        $mod = 0;
        switch ($type) {
            case 'analyze':
                // Intelligence : lecture labo plus vive
                $mod -= min(4, intdiv(max(0, $intel), 3));
                break;
            case 'mutate':
                $mod -= min(3, intdiv(max(0, $intel), 4));
                if ($purity < 70) {
                    $mod += 2; // pureté basse = stabilisation plus longue
                }
                break;
            case 'cross':
                $mod -= min(4, intdiv(max(0, $intel), 3));
                if (!empty($context['parent_intel'])) {
                    $mod -= min(2, intdiv((int) $context['parent_intel'], 6));
                }
                break;
            case 'mission':
                // Vitesse : sortie plus courte ; force aide un peu
                $mod -= min(5, intdiv(max(0, $vitesse), 3));
                $mod -= min(2, intdiv(max(0, $force), 5));
                if ($purity < 60) {
                    $mod += 3;
                } elseif ($purity < 80) {
                    $mod += 1;
                }
                if (($creature['status'] ?? '') === 'épuisée') {
                    $mod += 2;
                }
                break;
            case 'recon':
            case 'orbital':
                // Escorte : vitesse + intelligence réduisent le vol
                $ev = (int) ($eStats['vitesse'] ?? 0);
                $ei = (int) ($eStats['intelligence'] ?? 0);
                $er = (int) ($eStats['resistance'] ?? 0);
                $mod -= min(6, intdiv($ev + $ei, 4));
                if ($type === 'orbital') {
                    $mod -= min(2, intdiv($ei, 5));
                }
                // Zones dures : résistance d’escorte aide un peu
                $risk = (string) ($context['risk'] ?? 'modéré');
                if (in_array($risk, ['élevé', 'extrême'], true)) {
                    $mod -= min(2, intdiv($er, 5));
                }
                if (is_array($escort)) {
                    if (!empty($escort['mutated'])) {
                        $mod -= 1;
                    }
                    if (!empty($escort['is_hybrid'])) {
                        $mod -= 1;
                    }
                    if (($escort['status'] ?? '') !== 'prête' && ($escort['status'] ?? '') !== '') {
                        $mod += 3; // escorte fatiguée = plus long / prudent
                    }
                }
                break;
            case 'farm':
            case 'craft_drone':
            default:
                break;
        }

        return $mod;
    }

    /**
     * Gain de biomasse à la fin du timer de récolte.
     * Bonus léger si beaucoup de zones explorées (équipe qui connaît le terrain).
     */
    public static function farmBiomassYield(array $state = []): int
    {
        $base = 3;
        $explored = 0;
        foreach ($state['world']['zones'] ?? [] as $z) {
            if (!empty($z['explored']) && (($z['layer'] ?? 'natal') === 'natal')) {
                $explored++;
            }
        }
        // +1 tous les 2 biomes cartographiés (plafond +3)
        $bonus = min(3, intdiv($explored, 2));

        return $base + $bonus;
    }

    /** Petite logistique bonus en fin de récolte (équipe au sol). */
    public static function farmLogisticsYield(): int
    {
        return 1;
    }

    public static function farmCrewCost(): int
    {
        return 0; // effort = timer only for slice
    }
}
