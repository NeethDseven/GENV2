# Design — Canon GENESIS REBORN

**Source de vérité produit.**  
Si le code et le Design divergent, **le Design a raison** jusqu’à amendement explicite ici.

| Couche | État |
|--------|------|
| **Bible Phase 1** (`00`–`12`) | **TERMINÉE** — stabilisée · **créatures alignées `07`** (collection, pas lignée) |
| **Production** (`13`–`15`) | Opérationnelle — roadmap, boucle, backlog |
| **Code** (`src/Game/`, `public/`) | Jeu PHP — découverte + croisement (`IntentResolver`) |

---

## Ordre de lecture (obligatoire)

| # | Fichier | Contenu |
|---|---------|---------|
| 00 | [00_Philosophie.md](00_Philosophie.md) | **Canon** — vision, charte, tests (aligné `07`) |
| 01 | [01_Core_Gameplay.md](01_Core_Gameplay.md) | **Canon** — 5 verbes, D1–D5 · terrain Découvrir→Musée |
| 02 | [02_Audit.md](02_Audit.md) | **Canon** — audit V1 ❤️🔄❌🌌 |
| 03 | [03_Phase1.md](03_Phase1.md) | **Canon** — prologue, MVE, arcs |
| 04 | [04_Drones.md](04_Drones.md) | **Canon** — instruments, Vols, 4 classes |
| 05 | [05_Batiments.md](05_Batiments.md) | **Canon** — 5 instruments campus |
| 06 | [06_Ressources.md](06_Ressources.md) | **Canon** — 3 monnaies + états |
| 07 | [07_Creatures.md](07_Creatures.md) | **Canon** — Espèces, analyse, mutations, hybrides, musée |
| 08 | [08_UI_UX.md](08_UI_UX.md) | **Canon** — UI des décisions |
| 09 | [09_GENESIS_AI.md](09_GENESIS_AI.md) | **Canon** — IA observation |
| 10 | [10_Transition_Phase2.md](10_Transition_Phase2.md) | **Canon** — continuité d’échelle |
| 11 | [11_Multiplayer_Philosophy.md](11_Multiplayer_Philosophy.md) | **Canon** — seconde moitié |
| 12 | [12_Rules_Of_Genesis.md](12_Rules_Of_Genesis.md) | **Canon** — lois checklist |
| 13 | [13_Roadmap_Production.md](13_Roadmap_Production.md) | Production — phases & priorités |
| 14 | [14_Premiere_Boucle_Jouable.md](14_Premiere_Boucle_Jouable.md) | Spec prototype — boucle minimale |
| 15 | [15_Backlog_Vertical_Slice.md](15_Backlog_Vertical_Slice.md) | Backlog — flux, tâches, acceptation |

**Avant toute feature code :**

```
00 → 01 → (doc système) → 11 si multi/export → 12 → 14/15 → code
```

---

## Archive

| Emplacement | Contenu |
|-------------|---------|
| [`Archive/`](Archive/) | `00_Vision_Refonte` + brouillons `* copy.md` (supersédés par le canon condensé) |

**Canon actif** = série `00`–`15` à la racine de `Design/` (sauf `Archive/`).

---

## Process (ce workspace)

| Dossier | Rôle |
|---------|------|
| `Design/` | Vérité produit |
| `src/` + `public/` | Prototype PHP Phase 1 |
| `tests/` | Boucle d’état |

Références historiques éventuelles (`Architecture/`, `Decision_Log/`, `REBORN_ROADMAP.md`) : hors de ce dépôt minimal ; le Design local reste l’autorité.

## Suite immédiate

1. ~~Hygiène Design (archive copies, README)~~  
2. ~~Backlog vertical slice (`15`)~~  
3. ~~Lot prototype P0 (drones · monnaies · campus · GENESIS · scar)~~  
4. ~~Lot P1~~ : zones · multi-espèces · 2 mutations · croisement · musée structuré  
5. ~~Récupération · 2ᵉ boucle · polish UX~~  
6. **Suite** : playtest humain · plus d’espèces · polish narratif
