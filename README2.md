# GENESIS — État du projet (README2)

**Date de cliché :** 2026-08-05  
**Statut :** **jeu en code** (`src/Game/*`) — plus de monolithe prototype  
**Tests :** `Game loop tests passed` (`tests/game_loop_test.php`)  
**Cœur :** **découverte + croisement** · mutation secondaire · musée = mémoire  

---

## 1. En une phrase

**GENESIS est un jeu de découverte et de création du vivant.**

Le joueur explore des mondes inconnus, découvre de nouvelles espèces et les **croise** pour créer des formes de vie inédites.

La progression ne repose pas sur une ville ou une armée, mais sur le **patrimoine biologique** construit au fil des découvertes.

### Objectif réel

Le véritable objectif n’est **pas** d’obtenir la créature la plus puissante.

Le plaisir vient de la **découverte** de nouvelles espèces et de l’**expérimentation de croisements inattendus**, afin d’enrichir progressivement le patrimoine vivant de l’institut.

---

## 2. Boucle scientifique

```
Explorer
    ↓
Découvrir une nouvelle espèce
    ↓
L’analyser
    ↓
Chercher une seconde espèce
    ↓
La croiser (aperçu → création)
    ↓
Créer un nouvel hybride
    ↓
Mission / escorte d’exploration
    ↓
Musée (conséquence)
    ↓
Recommencer
```

| Rôle | Système |
|------|---------|
| **Cœur créatif** | **Croisement** (+ preview parents, synergie biomes) |
| **Outil secondaire** | **Mutation** (jamais CTA principal) |
| **Conséquence** | **Musée** — trace, pas win condition |
| **Épreuve** | **Mission** — succès / épuisement / blessure |
| **Escorte** | Hybride **prêt** aide la recon suivante (+ prélèvement) |

---

## 3. Structure code (actuelle)

```
GenV2/
├── Design/                 # Canon produit
├── public/
│   ├── index.php           # UI
│   └── assets/styles.css
├── src/
│   ├── bootstrap.php       # autoload Genesis\
│   └── Game/
│       ├── Data/Catalog.php
│       ├── CreatureFactory.php
│       ├── StateFactory.php
│       ├── GameQueries.php
│       ├── IntentResolver.php   # CTA croisement-first
│       └── GameEngine.php       # actions + session genesis_game
├── tests/
│   └── game_loop_test.php
├── legacy/                 # ancien monolithe prototype
├── README.md
└── README2.md
```

---

## 4. Lancer

```
http://localhost/projet/GenV2/public/
```

```bash
c:\xampp\php\php.exe tests/game_loop_test.php
```

→ `Game loop tests passed`

---

## 5. Contenu (matière à croiser)

| Zone | Risque | Biome | Espèce |
|------|--------|-------|--------|
| Plaine d’écho | modéré | chaleur | **Thermidé** |
| Crêtes salines | élevé | minéral | **Salinide** |
| Forêt froide | modéré | froid | **Cryopode** |
| Fosse lumineuse | élevé | abysse | **Lumivive** |
| Champs de scorie | extrême | feu | **Ferrugon** |
| Marais sporulés | modéré | spore | **Mycorène** |
| Crêtes venteuses | élevé | vent | **Zéphiride** |

**Synergie de croisement :** biomes différents → +1 toutes stats + possibilité « Écho mixte ».

---

## 6. Systèmes livrés

### Croisement
- 2 espèces analysées distinctes · −1 organique  
- **Choix des parents** + **aperçu hybride** (labo)  
- Parents informatifs seulement (pas de lignée)  

### Mutations
- Secondaires uniquement (stats / capacité)  
- N’apparaissent **pas** comme CTA principal après analyse  

### Mission & soin
- Succès / épuisement / blessure  
- Soin : 1 organique (épuisée) · 2 (blessée)  

### Escorte hybride
- Hybride **prêt** + recon → +1 prélèvement (et +1 logistique si I≥6)  
- Hybride archivé **épuisé**  

### Synergies narratives
- Paires de biomes nommées (ex. chaleur×minéral → **Dune d’armure**)  
- Capacité signature + bonus de stats + phrase de chronique  
- Journal `heritage.synergies` + panneau UI  

### Musée
- Collection structurée · +1 logistique à l’exposition  

### Sauvegarde fichier
- `storage/saves/save_*.json`  
- Actions : sauvegarder · charger · effacer  
- Indépendant de la session navigateur  

### MVE · Baie de projection
Conditions (toutes requises) :
1. ≥ 3 espèces analysées  
2. ≥ 1 hybride créé  
3. ≥ 1 mission clôturée  
4. Musée non vide  
5. Hybride exposé **ou** mémoire de croisement  
6. ≥ 3 entrées de mémoire  
7. Diversité écologique  

Baie : **verrouillée** → **opérationnelle** (MVE) → **projection accomplie** (`depart`).

### Ressources départ
- Organique **6** · Logistique **5** · Drones recon ×4  

---

## 7. Actions (`GameEngine`)

| Action | Rôle |
|--------|------|
| `select_zone` / `recon` | Explorer, découvrir |
| `analyze` | Comprendre |
| `cross` | **Créer** (cœur + synergie) |
| `mutate` | Adapter (secondaire) |
| `mission` | Éprouver |
| `recover` | Soigner |
| `exhibit` | Mémoire musée |
| `new_exploration` | Relancer |
| `focus_creature` | Activer un spécimen du bestiaire |
| `save_game` / `load_game` / `delete_save` | Persistance fichier |
| `depart` | Ouvrir la Baie si MVE |

Session : `$_SESSION['genesis_game']`

---

## 8. Backlog

### Fait
- [x] Architecture `src/Game/*`  
- [x] Flux croisement-first (`IntentResolver`)  
- [x] 5 zones / 5 espèces · biomes  
- [x] Preview croisement + choix parents  
- [x] Synergie multi-biomes  
- [x] Escorte hybride en recon  
- [x] Tests verts  

### Fait (lots récents)
- [x] Synergies narratives · save fichier · MVE/Baie  
- [x] Manifeste d’export enrichi + écran **Projection**  
- [x] 7 zones / 7 espèces · biomes spore & vent  
- [x] Post-départ : continuer la science sur le berceau  
- [x] **Codex des synergies** (écran Codex, déblocage par croisement)  
- [x] **Équilibrage** : recon 1/2/3 log, escort I≥8, mission/pureté  
- [x] **Polish playtest** : guide rapide, coûts sur zones, tips contextuels  

### Suite immédiate
- [ ] Playtest humain réel  
- [ ] Ajustements d’équilibrage post-playtest  
- [ ] Codex : filtres / recherche  

### Interdits
- City-builder · lignées · gear score · musée comme seule win  

---

## 9. Smoke test

1. Plaine → analyser Thermidé  
2. Crêtes → analyser Salinide  
3. Labo → **aperçu** croisement → créer hybride  
4. Mission (ou escorte recon Forêt)  
5. Musée · nouvelle zone (Abysse / Scorie)  

> J’ai découvert deux espèces, je les ai croisées, j’ai obtenu quelque chose d’inédit, je l’ai confronté au monde — mon institut en a gardé la mémoire.

---

## 10. Références

| Besoin | Fichier |
|--------|---------|
| Identité / lois | `Design/00` · `01` · `07` · `12` |
| Code | `src/Game/*` · `public/index.php` |
| Tests | `tests/game_loop_test.php` |

*Le plaisir = **découvrir** + **croiser**. Le musée témoigne.*
