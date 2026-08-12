<?php

declare(strict_types=1);

namespace Genesis\Game;

/**
 * Direction visuelle — biomes, raretés, sigils SVG (pas d’assets raster).
 */
final class VisualTheme
{
    public static function biomeSlug(string $biome): string
    {
        $b = mb_strtolower(trim($biome));

        return match ($b) {
            'chaleur' => 'chaleur',
            'froid' => 'froid',
            'minéral', 'mineral' => 'mineral',
            'feu' => 'feu',
            'abysse' => 'abysse',
            'spore' => 'spore',
            'vent' => 'vent',
            default => 'neutre',
        };
    }

    public static function raritySlug(string $rarity): string
    {
        $r = mb_strtolower(trim($rarity));

        return match (true) {
            str_contains($r, 'unique') => 'unique',
            str_contains($r, 'très rare'), str_contains($r, 'tres rare') => 'tres-rare',
            str_contains($r, 'rare') => 'rare',
            str_contains($r, 'peu') => 'peu-commune',
            str_contains($r, 'inconnue') => 'signal',
            default => 'commune',
        };
    }

    public static function riskSlug(string $risk): string
    {
        return match (mb_strtolower(trim($risk))) {
            'extrême', 'extreme' => 'extreme',
            'élevé', 'eleve' => 'eleve',
            default => 'modere',
        };
    }

    public static function biomeLabel(string $biome): string
    {
        return match (self::biomeSlug($biome)) {
            'chaleur' => 'Chaleur',
            'froid' => 'Froid',
            'mineral' => 'Minéral',
            'feu' => 'Feu',
            'abysse' => 'Abysse',
            'spore' => 'Spore',
            'vent' => 'Vent',
            default => $biome !== '' ? $biome : 'Inconnu',
        };
    }

    public static function biomeSvg(string $biome): string
    {
        $slug = self::biomeSlug($biome);
        $inner = match ($slug) {
            'chaleur' => '<circle cx="24" cy="24" r="8" fill="currentColor" opacity=".9"/><g stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity=".75"><path d="M24 4v6M24 38v6M4 24h6M38 24h6M9 9l4 4M35 35l4 4M9 39l4-4M35 13l4-4"/></g>',
            'froid' => '<path d="M24 6v36M10 14l28 20M10 34l28-20" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round"/><circle cx="24" cy="24" r="4" fill="currentColor"/>',
            'mineral' => '<path d="M24 6 L42 24 L24 42 L6 24 Z" fill="none" stroke="currentColor" stroke-width="2.2"/><path d="M24 14 L34 24 L24 34 L14 24 Z" fill="currentColor" opacity=".55"/>',
            'feu' => '<path d="M24 42c8 0 12-6 12-14 0-8-6-12-8-18-1 6-6 8-6 14 0-4-4-8-6-12-4 6-8 12-8 18 0 8 4 12 16 12z" fill="currentColor"/>',
            'abysse' => '<circle cx="24" cy="24" r="14" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="24" cy="24" r="7" fill="none" stroke="currentColor" stroke-width="1.6" opacity=".7"/><circle cx="24" cy="24" r="2.5" fill="currentColor"/>',
            'spore' => '<circle cx="24" cy="24" r="5" fill="currentColor"/><circle cx="12" cy="16" r="3.5" fill="currentColor" opacity=".7"/><circle cx="36" cy="18" r="3" fill="currentColor" opacity=".65"/><circle cx="16" cy="34" r="3" fill="currentColor" opacity=".6"/><circle cx="34" cy="34" r="4" fill="currentColor" opacity=".75"/>',
            'vent' => '<path d="M8 16c8-8 16-4 20 0s10 6 12 2M8 26c10-6 18 0 22 2s10 2 10-2M8 36c12-6 20-2 24 0" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>',
            default => '<rect x="10" y="10" width="28" height="28" rx="6" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="24" cy="24" r="5" fill="currentColor" opacity=".6"/>',
        };

        return '<svg class="biome-sigil biome-sigil--' . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') . '" viewBox="0 0 48 48" width="40" height="40" aria-hidden="true">' . $inner . '</svg>';
    }

    /**
     * Portrait SVG déterministe : biome + stats + graine espèce (album lisible).
     */
    public static function creaturePortrait(array $creature): string
    {
        $slug = self::biomeSlug((string) ($creature['biome'] ?? ''));
        $hybrid = !empty($creature['is_hybrid']);
        $analyzed = !empty($creature['analyzed']);
        $rarity = self::raritySlug((string) ($creature['rarity'] ?? ''));
        $seed = self::portraitSeed($creature);

        $cls = 'creature-portrait biome-sigil--' . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8')
            . ' rarity-' . htmlspecialchars($rarity, ENT_QUOTES, 'UTF-8')
            . ($hybrid ? ' is-hybrid' : '')
            . (!$analyzed ? ' creature-portrait--signal' : '');

        if (!$analyzed) {
            return '<svg class="' . $cls . '" viewBox="0 0 80 80" width="72" height="72" aria-hidden="true">'
                . '<defs><radialGradient id="sg' . $seed . '" cx="50%" cy="45%" r="55%">'
                . '<stop offset="0%" stop-color="currentColor" stop-opacity=".25"/>'
                . '<stop offset="100%" stop-color="currentColor" stop-opacity="0"/></radialGradient></defs>'
                . '<circle cx="40" cy="40" r="30" fill="url(#sg' . $seed . ')"/>'
                . '<circle cx="40" cy="40" r="22" fill="none" stroke="currentColor" stroke-width="2" stroke-dasharray="4 5" opacity=".7"/>'
                . '<circle cx="40" cy="40" r="6" fill="currentColor" opacity=".55"/>'
                . self::biomeMark($slug, 40, 18, 0.45)
                . '</svg>';
        }

        $stats = $creature['stats'] ?? [];
        $f = max(1, (int) ($stats['force'] ?? 4));
        $v = max(1, (int) ($stats['vitesse'] ?? 4));
        $r = max(1, (int) ($stats['resistance'] ?? 4));
        $i = max(1, (int) ($stats['intelligence'] ?? 4));
        $avg = max(4.0, ($f + $v + $r + $i) / 4.0);

        // Diamant de stats (conservé) + silhouette biomique
        $rf = 10 + (int) round(14 * min(1.5, $f / $avg));
        $rv = 10 + (int) round(14 * min(1.5, $v / $avg));
        $rr = 10 + (int) round(14 * min(1.5, $r / $avg));
        $ri = 10 + (int) round(14 * min(1.5, $i / $avg));
        $pts = sprintf('40,%d %d,40 40,%d %d,40', 40 - $ri, 40 + $rf, 40 + $rr, 40 - $rv);

        // Variation stable par graine (asymétrie légère)
        $jitter = (($seed % 7) - 3);
        $body = self::biomeBody($slug, $seed, $f, $v, $r, $i);

        $rings = '';
        if ($hybrid) {
            $rings .= '<circle cx="40" cy="40" r="30" fill="none" stroke="currentColor" stroke-width="1.4" opacity=".4" stroke-dasharray="3 4"/>';
            $rings .= '<circle cx="40" cy="40" r="34" fill="none" stroke="currentColor" stroke-width="0.8" opacity=".22"/>';
        } elseif (in_array($rarity, ['rare', 'tres-rare', 'unique'], true)) {
            $rings .= '<circle cx="40" cy="40" r="31" fill="none" stroke="currentColor" stroke-width="1.2" opacity=".3"/>';
        }

        $status = (string) ($creature['status'] ?? 'prête');
        $statusMark = '';
        if ($status === 'blessée') {
            $statusMark = '<path d="M34 52 L40 58 L46 52" fill="none" stroke="#e07868" stroke-width="2" opacity=".85"/>';
        } elseif ($status === 'épuisée') {
            $statusMark = '<path d="M32 54 h16" stroke="currentColor" stroke-width="1.6" opacity=".45"/>';
        }

        return '<svg class="' . $cls . '" viewBox="0 0 80 80" width="72" height="72" aria-hidden="true">'
            . '<defs><radialGradient id="pg' . $seed . '" cx="40%" cy="35%" r="65%">'
            . '<stop offset="0%" stop-color="currentColor" stop-opacity=".2"/>'
            . '<stop offset="100%" stop-color="currentColor" stop-opacity="0"/></radialGradient></defs>'
            . '<circle cx="40" cy="38" r="28" fill="url(#pg' . $seed . ')"/>'
            . $rings
            . $body
            . '<polygon points="' . $pts . '" fill="currentColor" opacity=".22" stroke="currentColor" stroke-width="1.5" transform="rotate(' . $jitter . ' 40 40)"/>'
            . '<circle cx="40" cy="40" r="3.5" fill="currentColor" opacity=".9"/>'
            . self::biomeMark($slug, 40, 14, 0.55)
            . $statusMark
            . '</svg>';
    }

    /** Graine stable pour variations d’apparence. */
    private static function portraitSeed(array $creature): int
    {
        $key = (string) ($creature['species_key'] ?? $creature['species'] ?? $creature['id'] ?? 'x');
        $h = 0;
        $len = strlen($key);
        for ($i = 0; $i < $len; $i++) {
            $h = (($h << 5) - $h + ord($key[$i])) & 0x7fffffff;
        }

        return max(1, $h % 997);
    }

    /** Petite marque biomique en haut du portrait. */
    private static function biomeMark(string $slug, int $cx, int $cy, float $opacity): string
    {
        $o = number_format($opacity, 2, '.', '');
        return match ($slug) {
            'chaleur' => '<circle cx="' . $cx . '" cy="' . $cy . '" r="3.5" fill="currentColor" opacity="' . $o . '"/>',
            'froid' => '<path d="M' . $cx . ' ' . ($cy - 4) . ' v8 M' . ($cx - 3) . ' ' . ($cy - 1) . ' l6 3 M' . ($cx - 3) . ' ' . ($cy + 2) . ' l6-3" stroke="currentColor" stroke-width="1.4" fill="none" opacity="' . $o . '"/>',
            'mineral' => '<path d="M' . $cx . ' ' . ($cy - 4) . ' L' . ($cx + 4) . ' ' . $cy . ' L' . $cx . ' ' . ($cy + 4) . ' L' . ($cx - 4) . ' ' . $cy . ' Z" fill="currentColor" opacity="' . $o . '"/>',
            'feu' => '<path d="M' . $cx . ' ' . ($cy + 4) . ' c3 0 5-3 4-7 -1 3-3 4-3 6 0-2-2-4-3-6 -2 3-4 5-4 7 0 2 2 3 6 3z" fill="currentColor" opacity="' . $o . '"/>',
            'abysse' => '<circle cx="' . $cx . '" cy="' . $cy . '" r="3.5" fill="none" stroke="currentColor" stroke-width="1.3" opacity="' . $o . '"/><circle cx="' . $cx . '" cy="' . $cy . '" r="1.2" fill="currentColor" opacity="' . $o . '"/>',
            'spore' => '<circle cx="' . $cx . '" cy="' . $cy . '" r="2" fill="currentColor" opacity="' . $o . '"/><circle cx="' . ($cx - 4) . '" cy="' . ($cy + 1) . '" r="1.4" fill="currentColor" opacity="' . $o . '"/><circle cx="' . ($cx + 4) . '" cy="' . ($cy + 1) . '" r="1.4" fill="currentColor" opacity="' . $o . '"/>',
            'vent' => '<path d="M' . ($cx - 6) . ' ' . $cy . ' c4-3 8-1 12 0" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity="' . $o . '"/>',
            default => '',
        };
    }

    /** Silhouette de fond par biome (album). */
    private static function biomeBody(string $slug, int $seed, int $f, int $v, int $r, int $i): string
    {
        $a = 0.12 + ($seed % 5) * 0.01;
        $oa = number_format($a, 2, '.', '');
        // Petits offsets déterministes
        $dx = ($seed % 5) - 2;
        $dy = (($seed * 3) % 5) - 2;

        return match ($slug) {
            'chaleur' => '<ellipse cx="' . (40 + $dx) . '" cy="' . (42 + $dy) . '" rx="' . (16 + (int) ($f / 3)) . '" ry="' . (12 + (int) ($r / 4)) . '" fill="currentColor" opacity="' . $oa . '"/>'
                . '<ellipse cx="' . (40 + $dx) . '" cy="' . (28 + $dy) . '" rx="10" ry="9" fill="currentColor" opacity="' . $oa . '"/>',
            'froid' => '<path d="M' . (28 + $dx) . ' ' . (48 + $dy) . ' Q40 ' . (18 + $dy) . ' ' . (52 + $dx) . ' ' . (48 + $dy) . ' Z" fill="currentColor" opacity="' . $oa . '"/>'
                . '<ellipse cx="' . (40 + $dx) . '" cy="' . (36 + $dy) . '" rx="' . (11 + (int) ($v / 4)) . '" ry="8" fill="currentColor" opacity="' . number_format($a + 0.05, 2, '.', '') . '"/>',
            'mineral' => '<path d="M' . (40 + $dx) . ' ' . (20 + $dy) . ' L' . (58 + $dx) . ' ' . (40 + $dy) . ' L' . (40 + $dx) . ' ' . (58 + $dy) . ' L' . (22 + $dx) . ' ' . (40 + $dy) . ' Z" fill="currentColor" opacity="' . $oa . '"/>',
            'feu' => '<path d="M' . (40 + $dx) . ' ' . (56 + $dy) . ' c10 0 16-8 14-20 -2 8-8 10-8 18 0-6-6-10-8-16 -6 8-12 14-12 20 0 8 6 12 14 12z" fill="currentColor" opacity="' . $oa . '"/>',
            'abysse' => '<ellipse cx="' . (40 + $dx) . '" cy="' . (40 + $dy) . '" rx="' . (18 + (int) ($i / 5)) . '" ry="14" fill="currentColor" opacity="' . $oa . '"/>'
                . '<circle cx="' . (34 + $dx) . '" cy="' . (36 + $dy) . '" r="3" fill="currentColor" opacity=".25"/>'
                . '<circle cx="' . (48 + $dx) . '" cy="' . (38 + $dy) . '" r="2" fill="currentColor" opacity=".2"/>',
            'spore' => '<circle cx="' . (40 + $dx) . '" cy="' . (36 + $dy) . '" r="' . (11 + (int) ($i / 5)) . '" fill="currentColor" opacity="' . $oa . '"/>'
                . '<circle cx="' . (28 + $dx) . '" cy="' . (44 + $dy) . '" r="6" fill="currentColor" opacity="' . $oa . '"/>'
                . '<circle cx="' . (52 + $dx) . '" cy="' . (46 + $dy) . '" r="5" fill="currentColor" opacity="' . $oa . '"/>'
                . '<circle cx="' . (40 + $dx) . '" cy="' . (52 + $dy) . '" r="4" fill="currentColor" opacity="' . $oa . '"/>',
            'vent' => '<ellipse cx="' . (40 + $dx) . '" cy="' . (40 + $dy) . '" rx="' . (20 + (int) ($v / 4)) . '" ry="8" fill="currentColor" opacity="' . $oa . '" transform="rotate(-12 ' . (40 + $dx) . ' ' . (40 + $dy) . ')"/>'
                . '<ellipse cx="' . (40 + $dx) . '" cy="' . (34 + $dy) . '" rx="14" ry="6" fill="currentColor" opacity="' . number_format($a + 0.04, 2, '.', '') . '" transform="rotate(8 ' . (40 + $dx) . ' ' . (34 + $dy) . ')"/>',
            default => '<ellipse cx="40" cy="40" rx="16" ry="14" fill="currentColor" opacity="' . $oa . '"/>',
        };
    }

    /** Textes d’aide courtes (survol). */
    public static function traitHelp(string $key): string
    {
        return match ($key) {
            'force' => 'Force — impact sur le terrain, tenue dans les zones dures.',
            'vitesse' => 'Vitesse — fluidité des sorties et des vols d’escorte.',
            'resistance' => 'Résistance — encaissement ; utile en escorte sur zones rudes.',
            'intelligence' => 'Intelligence — finesse au labo et lecture terrain en escorte.',
            'purity' => 'Pureté — stabilité de la forme. Plus elle baisse, plus le terrain devient capricieux.',
            'biome' => 'Biome — écologie d’origine. Une escorte familière d’un lieu le lit mieux.',
            'rarity' => 'Rareté — signature de la forme, pas une mesure de puissance brute.',
            'status' => 'Statut — prête, épuisée ou blessée. L’état change ce qu’on peut lui demander.',
            'ability' => 'Capacité — particularité unique de cette lecture.',
            'hybrid' => 'Hybride — forme croisée, souvent précieuse en escorte.',
            'knowledge' => 'Connaissance — ce que l’institut a compris. Les effets existent déjà ; la fiche se révèle en étudiant.',
            default => '',
        };
    }

    public static function statBars(array $stats): string
    {
        $rows = '';
        $map = [
            'force' => 'Force',
            'vitesse' => 'Vitesse',
            'resistance' => 'Résistance',
            'intelligence' => 'Intelligence',
        ];
        foreach ($map as $key => $label) {
            $val = (int) ($stats[$key] ?? 0);
            $pct = max(4, min(100, (int) round($val * 7)));
            $help = self::traitHelp($key);
            $rows .= '<div class="stat-bar has-tip" data-tip="' . htmlspecialchars($help, ENT_QUOTES, 'UTF-8') . '" title="' . htmlspecialchars($help, ENT_QUOTES, 'UTF-8') . '">'
                . '<span class="stat-bar__label">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>'
                . '<span class="stat-bar__track">'
                . '<span class="stat-bar__fill" style="width:' . $pct . '%"></span></span>'
                . '<span class="stat-bar__val">' . $val . '</span></div>';
        }

        return '<div class="stat-bars" aria-label="Caractéristiques">' . $rows . '</div>';
    }
}
