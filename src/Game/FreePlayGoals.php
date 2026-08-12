<?php

declare(strict_types=1);

namespace Genesis\Game;

/**
 * Objectifs silencieux post-tutoriel — direction free-play sans grind.
 * Récompenses soft : lore, monnaies légères, voix assistant, flash one-shot.
 */
final class FreePlayGoals
{
    public static function defaultState(): array
    {
        return [
            'completed' => [],
            'flags' => [
                'healed_wounded' => false,
            ],
            'last_claim' => null,
        ];
    }

    /**
     * @return list<array{id:string,title:string,hint:string,cta:?string,screen:?string}>
     */
    public static function definitions(): array
    {
        return [
            [
                'id' => 'filets_trois',
                'title' => 'Trois filets en flotte',
                'hint' => 'Assemble au Hangar jusqu’à trois drones de capture.',
                'cta' => 'Hangar',
                'screen' => 'institute',
            ],
            [
                'id' => 'soin_blessure',
                'title' => 'Chair soignée',
                'hint' => 'Une forme blessée au bestiaire — soigne-la (organique).',
                'cta' => 'Bestiaire',
                'screen' => 'bestiary',
            ],
            [
                'id' => 'biomes_croises',
                'title' => 'Deux horizons unis',
                'hint' => 'Croise deux formes de biomes différents.',
                'cta' => 'Labo',
                'screen' => 'lab',
            ],
            [
                'id' => 'nom_secret',
                'title' => 'Un nom secret',
                'hint' => 'Révèle une synergie nommée au Codex (pas un simple Écho mixte).',
                'cta' => 'Codex',
                'screen' => 'codex',
            ],
            [
                'id' => 'seuil_berceau',
                'title' => 'Seuil du berceau',
                'hint' => 'Remplis le Spatio-port (MVE) — cartographie + patrimoine digne.',
                'cta' => 'Spatio-port',
                'screen' => 'institute',
            ],
        ];
    }

    public static function ensure(array &$state): void
    {
        if (!isset($state['freeplay']) || !is_array($state['freeplay'])) {
            $state['freeplay'] = self::defaultState();
        }
        if (!isset($state['freeplay']['completed']) || !is_array($state['freeplay']['completed'])) {
            $state['freeplay']['completed'] = [];
        }
        if (!isset($state['freeplay']['flags']) || !is_array($state['freeplay']['flags'])) {
            $state['freeplay']['flags'] = ['healed_wounded' => false];
        }
    }

    public static function isActive(array $state): bool
    {
        // Post-tutoriel uniquement
        if (!empty($state['tutorial']['active'])) {
            $step = (string) ($state['tutorial']['step'] ?? '');
            if ($step !== Tutorial::STEP_FREE) {
                return false;
            }
        }

        return true;
    }

    /**
     * Évalue + récompense les objectifs nouvellement atteints.
     * Peut poser flash / latest_event / genesis voice.
     */
    public static function tick(array &$state): void
    {
        self::ensure($state);
        if (!self::isActive($state)) {
            return;
        }

        foreach (self::definitions() as $def) {
            $id = $def['id'];
            if (in_array($id, $state['freeplay']['completed'], true)) {
                continue;
            }
            if (!self::isMet($state, $id)) {
                continue;
            }
            self::claim($state, $def);
        }
    }

    public static function isMet(array $state, string $id): bool
    {
        return match ($id) {
            'filets_trois' => self::countCaptureDrones($state) >= 3,
            'soin_blessure' => !empty($state['freeplay']['flags']['healed_wounded']),
            'biomes_croises' => self::hasCrossBiomeHybrid($state),
            'nom_secret' => self::namedSynergyCount($state) >= 1,
            'seuil_berceau' => !empty(MveEvaluator::evaluate($state)['complete'])
                || !empty($state['export']['baie_unlocked']),
            default => false,
        };
    }

    public static function markHealedWounded(array &$state): void
    {
        self::ensure($state);
        $state['freeplay']['flags']['healed_wounded'] = true;
    }

    /**
     * Vue pour l’UI (liste + progression + prochain objectif).
     *
     * @return array{active:bool,done:int,total:int,items:list<array>,next:?array,all_done:bool,pride:?string}
     */
    public static function view(array $state): array
    {
        self::ensure($state);
        $active = self::isActive($state) && (
            empty($state['tutorial']['active'])
            || (string) ($state['tutorial']['step'] ?? '') === Tutorial::STEP_FREE
        );

        $items = [];
        $done = 0;
        $next = null;
        foreach (self::definitions() as $def) {
            $met = in_array($def['id'], $state['freeplay']['completed'], true) || self::isMet($state, $def['id']);
            if ($met) {
                $done++;
            }
            $row = [
                'id' => $def['id'],
                'title' => $def['title'],
                'hint' => $def['hint'],
                'cta' => $def['cta'],
                'screen' => $def['screen'],
                'done' => $met,
            ];
            $items[] = $row;
            if (!$met && $next === null) {
                $next = $row;
            }
        }
        $total = count($items);
        $allDone = $done >= $total && $total > 0;
        $mve = MveEvaluator::evaluate($state);
        $pride = null;
        if ($allDone && !empty($mve['complete']) && empty($state['export']['baie_unlocked'])) {
            $pride = 'Le berceau te regarde. Ouvre le spatio-port — c’est la fierté, pas la fin.';
        } elseif ($allDone && !empty($state['export']['baie_unlocked'])) {
            $pride = 'Tu as tenu Aster-0. Les orbites attendent, une à une.';
        } elseif ($allDone) {
            $pride = 'Fils libres tenus. Le Spatio-port reste le vrai seuil.';
        }

        return [
            'active' => $active,
            'done' => $done,
            'total' => $total,
            'items' => $items,
            'next' => $next,
            'all_done' => $allDone,
            'pride' => $pride,
            'score' => $done . '/' . $total,
        ];
    }

    /** Phrase assistant pour free-play (remplace le silence décoratif). */
    public static function assistantHint(array $state): ?array
    {
        $v = self::view($state);
        if (!$v['active']) {
            return null;
        }
        if (!empty($v['pride'])) {
            return [
                'marker' => 'Seuil',
                'message' => $v['pride'],
            ];
        }
        $next = $v['next'];
        if ($next === null) {
            return null;
        }

        return [
            'marker' => 'Horizon',
            'message' => sprintf(
                'Fil libre — %s. %s',
                $next['title'],
                $next['hint']
            ),
        ];
    }

    private static function claim(array &$state, array $def): void
    {
        $id = (string) $def['id'];
        $state['freeplay']['completed'][] = $id;
        $state['freeplay']['last_claim'] = $id;

        $reward = self::rewardFor($id);
        if (($reward['logistics'] ?? 0) > 0) {
            $state['resources']['logistics'] = (int) ($state['resources']['logistics'] ?? 0) + (int) $reward['logistics'];
        }
        if (($reward['samples'] ?? 0) > 0) {
            $state['resources']['samples'] = (int) ($state['resources']['samples'] ?? 0) + (int) $reward['samples'];
        }
        if (($reward['biomass'] ?? 0) > 0) {
            $state['resources']['biomass'] = (int) ($state['resources']['biomass'] ?? 0) + (int) $reward['biomass'];
        }

        $lore = (string) ($reward['lore'] ?? $def['title']);
        $state['heritage']['memories'][] = 'Fil libre · ' . $lore;

        $msg = (string) ($reward['message'] ?? ('Objectif tenu : ' . $def['title']));
        $state['ui']['flash'] = [
            'type' => 'discovery',
            'title' => 'Fil libre',
            'message' => $msg,
        ];
        $state['ui']['latest_event'] = [
            'label' => 'Fil libre — ' . $def['title'],
            'message' => $msg,
        ];
        $state['ui']['genesis'] = [
            'marker' => (string) ($reward['marker'] ?? 'Continuité'),
            'message' => (string) ($reward['voice'] ?? $msg),
        ];
    }

    /**
     * @return array{message:string,voice:string,marker:string,lore:string,logistics?:int,samples?:int,biomass?:int}
     */
    private static function rewardFor(string $id): array
    {
        return match ($id) {
            'filets_trois' => [
                'message' => 'Trois filets prêts. La flotte de prise est digne du berceau. (+1 logistique)',
                'voice' => 'Tu as assez de mains pour prendre sans trembler.',
                'marker' => 'Continuité',
                'lore' => 'Trois filets en flotte — la capture n’est plus un hasard.',
                'logistics' => 1,
            ],
            'soin_blessure' => [
                'message' => 'Tu as remis debout ce que le terrain a brisé. (+1 prélèvement)',
                'voice' => 'Soigner, c’est aussi écrire le vivant.',
                'marker' => 'Continuité',
                'lore' => 'Soin d’une blessure — la mémoire du sang.',
                'samples' => 1,
            ],
            'biomes_croises' => [
                'message' => 'Deux écologies se sont touchées. Le croisement a un sens.',
                'voice' => 'Ce n’est plus une expérience — c’est un dialogue entre peaux du monde.',
                'marker' => 'Hypothèse',
                'lore' => 'Croisement multi-biomes réussi.',
                'biomass' => 1,
            ],
            'nom_secret' => [
                'message' => 'Un nom secret est entré au Codex. (+1 logistique)',
                'voice' => 'Le berceau te donne un mot. Garde-le.',
                'marker' => 'Hypothèse',
                'lore' => 'Synergie nommée découverte.',
                'logistics' => 1,
            ],
            'seuil_berceau' => [
                'message' => 'Le Spatio-port est digne de s’ouvrir. Aster-0 t’a vu.',
                'voice' => 'Cartographie, patrimoine, preuve. Le ciel n’est plus un mur.',
                'marker' => 'Seuil',
                'lore' => 'Seuil MVE atteint — berceau digne.',
                'samples' => 1,
                'logistics' => 1,
            ],
            default => [
                'message' => 'Objectif tenu.',
                'voice' => 'Bien.',
                'marker' => 'Observation',
                'lore' => 'Objectif free-play.',
            ],
        };
    }

    private static function countCaptureDrones(array $state): int
    {
        $n = 0;
        foreach ($state['drones']['fleet'] ?? [] as $d) {
            if (($d['type'] ?? '') === DroneYard::TYPE_CAPTURE) {
                $n++;
            }
        }

        return $n;
    }

    private static function hasCrossBiomeHybrid(array $state): bool
    {
        foreach (GameQueries::analyzedCreatures($state) as $c) {
            if (empty($c['is_hybrid'])) {
                continue;
            }
            // Flag posé par CreatureFactory::hybrid
            if (!empty($c['biome_mix'])) {
                return true;
            }
            $id = (string) ($c['synergy_id'] ?? '');
            if ($id !== '' && $id !== 'echo_mixte') {
                return true;
            }
        }
        foreach ($state['heritage']['synergies'] ?? [] as $syn) {
            if (!is_array($syn)) {
                continue;
            }
            $title = (string) ($syn['title'] ?? '');
            $id = (string) ($syn['id'] ?? '');
            if ($title !== '' && $title !== 'Écho mixte' && $id !== 'echo_mixte') {
                return true;
            }
        }

        return false;
    }

    private static function namedSynergyCount(array $state): int
    {
        $n = 0;
        foreach ($state['heritage']['synergies'] ?? [] as $syn) {
            if (!is_array($syn)) {
                continue;
            }
            $title = (string) ($syn['title'] ?? '');
            $id = (string) ($syn['id'] ?? '');
            if ($title === '' || $title === 'Écho mixte' || $id === 'echo_mixte') {
                continue;
            }
            $n++;
        }

        return $n;
    }
}
