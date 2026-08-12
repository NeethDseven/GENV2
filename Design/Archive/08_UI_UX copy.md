# 08 — UI / UX : interface des décisions

**Rôle :** UX Director (standard Blizzard — clarté de décision, pas polish cosmétique)  
**Périmètre :** Phase 1  
**Interdit dans ce document :** refonte esthétique, skins, “plus joli”  
**Objet :** changer la **philosophie** de l’interface

**Documents frères :** `01_Core_Gameplay.md` · `03_Phase1.md` · `05_Batiments.md` · `06_Ressources.md` · `07_Creatures.md`

---

## 0. Diagnostic de philosophie

### 0.1 Ce qui est faux aujourd’hui

L’UI Phase 1 est construite autour des **systèmes** :

```
Nav = systèmes
Commandement · Monde · Exploration · Laboratoire · Biologie · Musée · Sortie · Journal
HUD  = stocks de systèmes
Bio · Mat · Éch · Ess · Pop · Drn · Mor · Snt · Lig · Déc · ISMN
Hub  = colonie (dashboard)
```

Chaque écran **expose un sous-système** (bâtiments, filtres réserve, prep score, checklist T8…).  
Le joueur doit **traduire** le système en décision.

### 0.2 Ce qui doit être vrai demain

L’UI est construite autour des **décisions** :

```
Que dois-je décider maintenant ?
  → Où aller ?
  → Qui comprendre ?
  → Que transformer ?
  → Qui risquer ?
  → Quoi inscrire ?
  → Sommes-nous dignes de partir ?
```

Un écran n’existe que s’il **porte une décision** (ou le feedback d’une décision).  
Un widget n’existe que s’il **change ce choix**.

### 0.3 Phrase guide UX

> **Montre la décision. Cache le système.**  
> **Une action primaire par écran. Une phrase de branche partout.**  
> **Le hub est l’Institut vivant, pas le QG de colonie.**

### 0.4 Les cinq décisions (seules légitimes en nav mentale)

| # | Décision joueur | Verbe |
|---|-----------------|-------|
| D1 | **Où** rencontrer le vivant / le monde ? | Rencontrer |
| D2 | **Qui** comprendre en priorité ? | Comprendre |
| D3 | **Que** transformer (forger / unir / refuser) ? | Transformer |
| D4 | **Qui** éprouver / risquer sur le terrain ? | Éprouver |
| D5 | **Quoi** inscrire / emporter (Maison, mémoire, départ) ? | Inscrire / Partir |

Toute UI hors de ces décisions est **suspecte**.

---

## 1. Principes d’architecture décisionnelle

| # | Principe | Implication |
|---|----------|-------------|
| 1 | **Decision First** | L’écran s’ouvre sur la question, pas sur l’inventaire système |
| 2 | **One Primary Action** | Un CTA dominant ; le reste est secondaire |
| 3 | **Progressive disclosure** | Détail technique en panneau “avancé”, jamais en premier fold |
| 4 | **Creature is the unit of UI** | Cartes = phrase d’identité, pas tableur |
| 5 | **Institute, not Empire** | Lexique + hub = Institut scientifique |
| 6 | **Click budget** | Acte core ≤ **3 clics** depuis n’importe quel écran principal |
| 7 | **HUD = état de décision** | Pas un scoreboard de stocks |
| 8 | **Feedback = histoire** | Toasts et journal racontent, pas seulement ±ressource |
| 9 | **Conseiller = prochaine décision** | L’IA propose un verbe, pas “up le QG” |
| 10 | **Same mental model P2** | Silhouette branche et verbes inchangés en galaxie |

### Budget de clics (cible)

| Acte | Clics max (depuis hub) |
|------|-------------------------|
| Lancer une recon / expédition (preset) | 3 |
| Lancer une analyse | 2 |
| Ouvrir une branche et lire sa phrase | 1–2 |
| Démarrer un rituel d’union (choix faits) | 3–4 |
| Voir l’état du patrimoine / départ | 1 |
| Fonder une lignée (formulaire) | 3–4 |

**Aujourd’hui :** souvent 5–12 clics + lecture de 3 panneaux parasites.

---

## 2. Analyse écran par écran (état actuel)

Légende clics : estimation du parcours **joueur motivé** pour l’acte principal de l’écran.

---

### 2.1 Chrome global — Nav + HUD + macarons + IA

**Écrans / couches :** `layout.php` nav · `hud.php` · `task_macarons` · `ai_panel`

| | |
|--|--|
| **Que cherche le joueur ?** | Où suis-je ? Que puis-je faire **maintenant** ? Est-ce que quelque chose se termine ? |
| **Inutile** | Nav “Commandement” comme hub empire ; HUD Bio/Mat/Éch/Ess/Pop/Drn/Mor/Snt/Déc en parallèle ; spés cachées dans d’autres pages ; macarons d’actions colonie type harvest/care |
| **À mettre en avant** | **Prochaine décision** (1 ligne) · timers d’actes scientifiques/expé · stabilité berceau · patrimoine (Maison / branches) · stocks **3 monnaies** max |
| **Clics** | Nav actuelle : 8 destinations → **taxe cognitive** avant même la décision. |

**Verdict philosophie :** le chrome raconte un **empire de jauges**. Il doit raconter un **Institut en train de décider**.

---

### 2.2 Commandement (`dashboard`)

| | |
|--|--|
| **Que cherche le joueur ?** | “Par où je commence ?” / gérer base / voir progression. **Réellement** il devrait chercher : *quelle est ma prochaine décision patrimoine ?* |
| **Inutile** | Build queue multi-bâtiments, harvest, care pop, formation spés, culture dominante, caps, panneaux colonie empilés, checklist T8 type “lab 8 / pop 150” en face |
| **À mettre en avant** | État de l’Institut (équipe + berceau) · **carte des 5 décisions** avec 1 CTA chacune · patrimoine résumé · actes en cours |
| **Clics (acte utile aujourd’hui)** | Souvent **0 décision claire** → le joueur **rebondit** (2–4 clics) vers Explore/Lab. Harvest : 2–4 clics de filler. |

**Verdict :** **mauvais hub**. À remplacer par **Institut** (voir §4).

---

### 2.3 Monde natal (`planet`)

| | |
|--|--|
| **Que cherche le joueur ?** | Comprendre mon berceau ; où aller ensuite ; santé du monde. |
| **Inutile** | Seed debug, facteurs ISMN en liste d’ingénierie, jargon “santé colonie” mélangé à l’écologie, densités techniques |
| **À mettre en avant** | **Carte des zones** (lecture : Inconnu / Entrevue / Lue) · **stabilité berceau** (1 bande) · **POI / indices drones** · CTA “Préparer une sortie ici” |
| **Clics** | Monde → choisir zone → Explorer : souvent **3–5** ; cible **2** (zone + CTA expé). |

**Verdict :** bon **lieu de décision D1** s’il devient carte décisionnelle, pas fiche technique planète.

---

### 2.4 Exploration (`explore`)

| | |
|--|--|
| **Que cherche le joueur ?** | Où envoyer qui, pour quoi, à quel risque — puis **partir**. |
| **Inutile** | Ligne Sci/Sol/Ing/Soig/Gén · preset ×1,5–×2 jargon · multi-seuils % discovery en pilules · bonus bâtiments “−% durée +prep” en tête · loadout tableur |
| **À mettre en avant** | **Zone** (avec intel drone) · **bande de risque nommée** · composition **Personnel / Branches (phrase) / Vols drones** · 1 CTA “Lancer” · missions en cours |
| **Clics** | Aujourd’hui souvent **6–10** (zone, objectif, équipement, spés, créatures, valider). Cible **3** avec presets intelligents + intel. |

**Verdict :** écran **critique** — aujourd’hui un simulateur de prep, demain un **rituel de départ**.

---

### 2.5 Laboratoire / Réserve (`lab`)

| | |
|--|--|
| **Que cherche le joueur ?** | Qui analyser ? Qui est mon patrimoine vivant ? Qui emmener / forger ? |
| **Inutile** | 15+ filtres (flags, pureté, type admin, gen bands…) · double langage pureté/stats · profondeur d’analyse façon debug · lab level comme héros de page |
| **À mettre en avant** | File **“À comprendre”** (non analysés) · grille **branches** en silhouette · filtres **≤6** (Nature, Lignée, Rôle, Dispo, Prestige, Knowledge) · CTA Analyser / Ouvrir rituel |
| **Clics** | Analyser : **2–4** OK. Trouver une branche dans le bruit filtres : **5–15**. Cible recherche **≤3**. |

**Verdict :** excellent **cœur** mal habillé en Excel. Doit devenir **Archives vivantes + porte d’analyse**.

---

### 2.6 Biologie (`genetics`)

| | |
|--|--|
| **Que cherche le joueur ?** | Créer : unir, forger un Don, parfois intégrer au monde. |
| **Inutile** | Protocoles / vœux / investissements / staff / opposition élémentaire en premier plan · gates “lab ≥10” comme message d’échec · tabs techniques sans question humaine |
| **À mettre en avant** | **Qui + qui → quelle promesse ?** (union) · **Quelle branche + quel Don + quel risque ?** (forge) · preview **phrase d’identité résultante** · coût en monnaies simples · impact berceau |
| **Clics** | Union complète aujourd’hui **8–15**. Cible rituel **3–5** (sujets → promesse → confirmer risque). |

**Verdict :** le plus grand écart “profondeur désirée vs UI de console dev”. **Rituel**, pas formulaire.

---

### 2.7 Musée (`museum`)

| | |
|--|--|
| **Que cherche le joueur ?** | Voir ma Maison ; me souvenir ; fierté ; parfois célébrer. |
| **Inutile** | Bonus moral/pureté, niveaux musée vanity, prestige chiffré P1 |
| **À mettre en avant** | **Lignées** (nom, promesse, fondateur, génération) · **souvenirs** · mémoriaux · CTA “Fonder / Inscrire” si pertinent |
| **Clics** | Voir sa Maison : **1–2** (bon). Célébrer spam : à **désinciter**. |

**Verdict :** bon **lieu émotionnel** — le purger du city-builder soft.

---

### 2.8 Sortie Phase 1 (`ending` + `phase1_panel`)

| | |
|--|--|
| **Que cherche le joueur ?** | Suis-je digne de partir ? Qu’est-ce qui me manque ? Réclamer / revoir mon patrimoine. |
| **Inutile** | Checklist multi-seuils colonie (pop, lab level, spatioport level…) au même rang que le MVE · jargon achieve/maintain |
| **À mettre en avant** | **3–4 piliers patrimoine** (Maison, knowledge, diversité Natures, mémoire, preuves) · **Baie** comme seuil · revue de **ce qui voyage** · CTA unique |
| **Clics** | Comprendre le blocage : aujourd’hui **lecture longue**. Cible : **1 écran, 1 manquant, 1 CTA**. |

**Verdict :** aujourd’hui un **achievement board** ; demain un **conseil de départ**.

---

### 2.9 Journal (`journal`)

| | |
|--|--|
| **Que cherche le joueur ?** | Qu’est-il arrivé ? Relire une histoire. |
| **Inutile** | Bruit d’events colonie low-signal ; listes sans mise en récit |
| **À mettre en avant** | Timeline des **actes patrimoniaux** (naissance, scar, fondation, départ de mission, perte de drone) · non-lus · lien vers branche |
| **Clics** | **1** pour ouvrir — bon. Filtrer l’essentiel : **0 friction**. |

**Verdict :** garder comme **mémoire**, pas log serveur.

---

### 2.10 Partiels transverses

| Partiel | Problème philosophique | Refonte |
|---------|------------------------|---------|
| `colony_status` | Langage colonie | → État Institut / berceau |
| `population_panel` | RH empire | → Disponibilité d’équipe simple |
| `ismn_panel` | Facteurs d’ingénierie | → 1 bande + “pourquoi c’est tendu” en 1 phrase |
| `loop_widget` | États NEED_COLLECT / BUILD | → 5 verbes décisions |
| `squad_presets` | Harvest 8 rôles | → Presets **expé / labo** seulement |
| `tutorial_overlay` | CTA vers commandement | → Première **sortie + analyse** |
| `active_actions` / macarons | File colonie | → Actes rituels & missions seulement |

---

## 3. Reconstruction : philosophie des écrans

Chaque écran = **une question** + **une action primaire**.

| Écran | Question (titre mental) | Action primaire |
|-------|-------------------------|-----------------|
| **Institut** | Que dois-je décider maintenant ? | CTA de la décision recommandée |
| **Monde** | Où le berceau m’appelle-t-il ? | Préparer une sortie sur une zone |
| **Expédition** | Qui risquons-nous, pour quoi ? | Lancer / Recon |
| **Archives vivantes** | Qui mon patrimoine contient-il ? | Analyser / Sélectionner une branche |
| **Rituel** | Que créons-nous ? | Forger / Unir / Intégrer |
| **Maison** | Que laisserons-nous ? | Fonder / Consulter lignée |
| **Départ** | Sommes-nous dignes ? | Ouvrir la Baie / Réclamer |
| **Mémoire** | Que s’est-il passé ? | Relire (secondaire) |

**Fiche branche** n’est pas un “écran nav” : c’est un **panneau universel** ouvert depuis partout (1 clic).

---

## 4. Arborescence UX officielle

### 4.1 Carte de navigation (primaire)

```
┌──────────────────────────────────────────────────────────────┐
│  GENESIS · [Nom de l’Institut]                               │
│  HUD décisionnel (compact)          [Mémoire] [Compte]       │
├──────────────────────────────────────────────────────────────┤
│  INSTITUT  │  MONDE  │  EXPÉDITION  │  ARCHIVES  │  MAISON   │
│                         (Départ = badge / fin d’arc)          │
└──────────────────────────────────────────────────────────────┘
```

| Nav | Remplace | Décisions servies |
|-----|----------|-------------------|
| **Institut** | Commandement / dashboard | Orientation D1–D5 |
| **Monde** | Monde natal | D1 |
| **Expédition** | Exploration | D1 + D4 |
| **Archives** | Lab (réserve) + porte analyse | D2 (+ D4 sélection) |
| **Maison** | Musée | D5 |
| **Départ** | Ending / Sortie P1 (accès contextuel) | D5 / Partir |
| **Mémoire** | Journal (icône, secondaire) | Feedback |

**Biologie (`genetics`)** n’est **plus** un item de nav top-level.  
C’est un **mode Rituel** ouvert depuis Archives (ou CTA Institut) :

```
Archives → sélection branche(s) → [Forger] [Unir] [Intégrer]
```

Réduit la fragmentation Lab vs Biologie (aujourd’hui 2 nav pour 1 famille de décisions).

---

### 4.2 Arborescence détaillée

```
AUTH
├── Connexion
└── Créer un Institut (ex-Arche)

PHASE 1
│
├── INSTITUT                          [HUB]
│   ├── Bandeau : prochaine décision (1 phrase + 1 CTA)
│   ├── Carte des 5 décisions (état : dispo / bloqué / en cours)
│   ├── Actes en cours (missions, analyses, forges, naissances)
│   ├── Patrimoine résumé (Maison · branches clés · knowledge)
│   ├── Berceau (1 bande stabilité) + Équipe (1 bande état)
│   ├── Campus (5 instruments — capacités, pas build queue)
│   └── (secondaire) Stocks 3 monnaies · liens Mémoire
│
├── MONDE
│   ├── Carte zones (lecture + POI drones)
│   ├── Zone sélectionnée
│   │     ├── Intel (risque nommé, signatures)
│   │     └── CTA → Expédition (préremplie)
│   └── Stabilité du berceau (détail progressif)
│
├── EXPÉDITION
│   ├── Missions en cours
│   ├── Mode : Recon | Expédition complète
│   ├── Zone (héritée ou choisie)
│   ├── Composition
│   │     ├── Personnel (simple)
│   │     ├── Branches (cartes phrase d’identité)
│   │     └── Vols drones (classe + module)
│   ├── Bande risque + objectifs lisibles
│   └── CTA Lancer
│         └── Résolution / récit → retour Archives / Institut
│
├── ARCHIVES VIVANTES
│   ├── File « À comprendre » (CTA Analyser)
│   ├── Grille patrimoine (silhouette)
│   ├── Filtres ≤6
│   └── Branche sélectionnée
│         ├── Fiche (panneau)
│         ├── Analyser
│         ├── Emmenner en expédition
│         ├── Ouvrir Rituel →
│         └── Préserver / Soins
│
├── RITUEL                          [SOUS-FLUX, pas nav top]
│   ├── Forger (1 branche → Don / Nature)
│   │     └── Preview phrase + risque + confirmer
│   ├── Unir (2 branches → naissance)
│   │     └── Promesse · preview · risque · confirmer
│   └── Intégrer (branche → zone)
│         └── Mode simple · impact berceau · confirmer
│
├── MAISON (Galerie des Fondateurs)
│   ├── Lignées
│   │     ├── Fondation (si candidature)
│   │     ├── Fondateur / générations / vivants
│   │     └── Souvenirs & scars exposés
│   └── Mémoriaux
│
├── DÉPART (Baie de projection)
│   ├── Piliers MVE (3–4, pas 12 checks)
│   ├── Revue de l’export (ce qui voyage)
│   ├── CTA Réclamer / Partir
│   └── (post) Bilan émotionnel + nouvelle run
│
├── MÉMOIRE (Journal)
│   └── Timeline filtrée (Patrimoine | Terrain | Institut)
│
└── FICHE BRANCHE                   [OVERLAY UNIVERSEL]
    ├── Phrase d’identité
    ├── Intégrité · Fertilité · Knowledge
    ├── Scars / parents / lignée
    └── Actions contextuelles (Analyser, Forger, Expé, Inscrire…)
```

---

### 4.3 Flux de décisions (chemins heureux)

#### Flux A — Première heure (Rencontrer → Comprendre)

```
Institut (CTA « Sortir ») 
  →1 Expédition (zone proche + drones préremplis)
  →2 Lancer
  →3 Retour toast « Nouvelle branche »
  →4 Archives (À comprendre)
  →5 Analyser
```

**Clics décisions :** ~5 pour le premier cycle complet (acceptable tutoriel).  
**Cycles suivants :** raccourcis presets → **3**.

#### Flux B — Transformer

```
Archives → sélection A + B → Unir
  → preview phrase enfant
  → accepter risque berceau/intégrité
  → confirmer
```

**Clics :** 4.

#### Flux C — Éprouver une œuvre

```
Fiche branche → « Éprouver »
  → Expédition préremplie (zone fit)
  → ajuster drones
  → Lancer
```

**Clics :** 3–4.

#### Flux D — Inscrire / Partir

```
Maison → Fonder
// plus tard
Institut badge « Patrimoine digne » → Départ → revue → Partir
```

**Clics fondation :** 3–4. **Départ :** 2.

---

## 5. Spécification par écran reconstruit

### 5.1 Institut (hub)

**Question :** *Que dois-je décider maintenant ?*

| Zone | Contenu | Interdit |
|------|---------|----------|
| Hero décision | 1 phrase + 1 CTA (ex. « Une branche attend d’être comprise ») | Liste de buildings |
| 5 tuiles décisions | État coloré (prêt / bloqué / en cours) | Checks pop/lab level |
| En cours | Timers missions/analyses/naissances | Macarons harvest |
| Patrimoine | Maison · n branches · knowledge · diversité Natures | Caps matériel |
| Berceau / Équipe | 2 bandes | Moral+santé+fatigue séparés |
| Campus | 5 instruments → tooltip capacité | File d’upgrade empire |

**Action primaire :** CTA de la décision recommandée.  
**Clics vers acte :** **1**.

---

### 5.2 Monde

**Question :** *Où le vivant m’attend-il ?*

| Mettre en avant | Enlever du premier fold |
|-----------------|-------------------------|
| Zones cliquables + lecture | Seed, JSON facteurs |
| Intel drone (signatures) | “Santé colonie” |
| Stabilité 1 bande + 1 cause | 6 facteurs égaux |
| CTA Expédition | — |

**Action primaire :** Préparer une sortie sur la zone.  
**Clics zone→lancer (avec suite Expé) :** **2–3**.

---

### 5.3 Expédition

**Question :** *Qui risquons-nous, pour quelle rencontre ?*

**Structure en 3 colonnes mentales :**

1. **But** — zone + objectif (recon / prélèvement / épreuve / cartographie)  
2. **Composition** — Personnel · Branches · Vols  
3. **Risque** — bande nommée + ce qui est en jeu  

**Presets :** “Première sortie”, “Recon drones”, “Éprouver le fondateur (prudent)”, “Prélèvement ciblé”.

**Action primaire :** Lancer.  
**Clics avec preset :** **≤3**.  
**Interdit en tête :** roster Sci/Sol/Ing en ligne mono.

---

### 5.4 Archives vivantes

**Question :** *Qui fait partie de mon patrimoine, et qui dois-je comprendre ?*

| Priorité visuelle | Secondaire |
|-------------------|------------|
| File À comprendre | Filtres avancés |
| Cartes phrase d’identité | Anciens types/pureté |
| Knowledge badge | Lab level hero |
| CTA Analyser / Rituel / Expé | 15 filtres |

**Filtres max 6 :** Nature · Lignée · Rôle · Disponibilité · Prestige · Knowledge.

**Action primaire contextuelle :** Analyser **ou** ouvrir fiche.  
**Clics analyse d’un non-analysé en tête de file :** **2**.

---

### 5.5 Rituel (Forger / Unir / Intégrer)

**Question :** *Que créons-nous, à quel prix ?*

**Pattern unique (Blizzard-clean) :**

```
1. Sujet(s)     — cartes identité
2. Intention    — 3 choix max visibles (Don / promesse / zone)
3. Prix         — organique · prélèvements · intégrité · berceau
4. Preview      — phrase d’identité future (ou “échec possible”)
5. Confirmer    — un bouton, langage humain
```

**Avancé** (replié) : protocoles fins, oppositions, etc.

**Action primaire :** Confirmer le rituel.  
**Clics :** **3–5**.

---

### 5.6 Maison

**Question :** *Quelle histoire portons-nous ?*

- Galerie de lignées (carte Maison, pas stats).  
- Fondateur en portrait émotionnel.  
- Générations, vivants, mémoriaux.  
- CTA Fonder si hybride / branche digne sans Maison.

**Action primaire :** Consulter / Fonder.  
**Clics voir sa lignée :** **1**.

---

### 5.7 Départ

**Question :** *Pouvons-nous emporter une Maison digne ?*

```
[  Maison fondée     ✓/✗  ]
[  Branches comprises ✓/✗  ]
[  Diversité Natures  ✓/✗  ]
[  Mémoire & preuves  ✓/✗  ]
[  Baie prête         ✓/✗  ]
```

Un manquant = **un** CTA (“Aller fonder”, “Analyser encore”, “Éprouver une branche”).

**Action primaire :** Partir / Réclamer.  
**Clics si prêt :** **2**.  
**Clics si bloqué :** **1** vers la décision manquante.

---

### 5.8 Fiche branche (overlay)

Toujours le même squelette (`07_Creatures.md`) :

1. Phrase d’identité  
2. Intégrité · Fertilité · Knowledge  
3. Scars / lignée / parents  
4. Actions (max 4 visibles)

Ouverte depuis : Archives, Expédition, Maison, Mémoire, toast.

**Clics pour ouvrir :** **1**.

---

### 5.9 Mémoire

Timeline par **poids narratif**, pas par dump.

Filtres : Patrimoine · Terrain · Institut.

**Pas** de nav primary équivalente aux 5 piliers — icône avec badge non-lus.

---

## 6. HUD décisionnel (remplace le scoreboard)

### Afficher en permanence

| Élément | Pourquoi (décision) |
|---------|---------------------|
| Nom Institut | Identité |
| Prochaine décision (1 ligne) ou badge “acte prêt” | Orientation |
| Stabilité berceau (bande) | Puis-je forger / intégrer ? |
| Équipe dispo (simple) | Puis-je sortir ? |
| Drones op. (parc, pas gold) | Puis-je instrumenter ? |
| Organique · Prélèvements · Logistique | Puis-je payer l’acte ? |
| Timers d’actes en cours | Dois-je attendre / revenir ? |
| Maison (lien) | Ancre patrimoine |

### Ne plus afficher en permanence

| Retiré | Raison |
|--------|--------|
| Mat/Bio/Éch/Ess/Pop/Mor/Snt/Déc en file | Bruit système |
| Caps | Non décisionnels |
| Multi-spés | RH |
| ISMN + 6 facteurs | → 1 bande |
| Macarons harvest/care/build | Anti-vision |

---

## 7. Patterns de composants (philosophie)

| Composant | Règle |
|-----------|--------|
| **Carte branche** | Phrase d’identité d’abord ; jamais grille de 12 stats |
| **Bande de risque** | Mots (Faible / Modéré / Élevé — *cause*) pas seulement % |
| **CTA primaire** | Un seul style dominant par vue |
| **Confirm risque** | Modal court : ce que tu risques (branche, drone, berceau) |
| **Empty states** | Toujours une décision (“Lancez une recon pour remplir les Archives”) |
| **Blocked states** | Toujours la raison + lien vers la décision qui débloque (pas “lab level 10”) |
| **Conseiller IA** | “Décision : Comprendre — 2 branches scannées attendent” + CTA |
| **Toast** | Titre narratif + lien vers fiche / Archives |

---

## 8. Mapping ancien → nouveau

| Ancien | Nouveau | Note |
|--------|---------|------|
| Commandement | **Institut** | Hub décisions |
| Monde natal | **Monde** | Carte D1 |
| Exploration | **Expédition** | Rituel de départ |
| Laboratoire | **Archives vivantes** | Patrimoine + analyse |
| Biologie | **Rituel** (sous-flux) | Plus en nav top |
| Musée | **Maison** | Lignées / mémoire |
| Sortie P1 / Ending | **Départ** | MVE + Baie |
| Journal | **Mémoire** | Secondaire |
| HUD multi-jauges | **HUD décisionnel** | 3 monnaies + états |
| Phase1 checklist 12 | **Piliers Départ** | 3–4 items |
| Fiche créature éclatée | **Overlay unique** | 07_Creatures |

---

## 9. Métriques UX de succès (design, pas esthétique)

| Métrique | Cible |
|----------|--------|
| Temps pour identifier la prochaine décision (hub) | **< 5 s** |
| Clics pour lancer une expé (joueur intermédiaire) | **≤ 3** |
| Clics pour analyser une branche en file | **≤ 2** |
| Clics pour démarrer union (sujets déjà choisis mentalement) | **≤ 4** |
| % joueurs qui citent “patrimoine/Maison” comme but UI | **> 70 %** playtest |
| % qui citent “maxer bâtiments/stocks” | **< 15 %** |
| Destinations nav primaires | **5** (+ Départ contextuel + Mémoire icône) |

---

## 10. Ce que ce document refuse explicitement

- “Améliorer les couleurs / le CSS” comme solution  
- Ajouter des écrans “parce que le système existe”  
- Garder Lab **et** Biologie en nav pour des raisons historiques  
- HUD trophée de ressources  
- Checklists d’empire comme progression émotionnelle  
- Toute UI qui oblige le joueur à **être comptable** avant d’être **directeur scientifique**

---

## 11. Arborescence officielle (résumé une page)

```
INSTITUT ──────────── Hub des 5 décisions
   │
   ├─► MONDE ──────── Où ? ──────────────► EXPÉDITION ── Qui / quoi risquer ?
   │                                              │
   │                                              ▼
   ├─► ARCHIVES ──── Qui comprendre ? ◄── retours terrain
   │       │
   │       └─► RITUEL ── Que créer ? (Forger / Unir / Intégrer)
   │
   ├─► MAISON ────── Quoi inscrire ?
   │
   └─► DÉPART ────── Partir digne ? (MVE + Baie)

MÉMOIRE (secondaire) · FICHE BRANCHE (partout)
```

**Cinq questions. Cinq lieux. Un rituel. Un départ.**

---

## 12. Engagement UX Director

> L’interface n’est pas le plan des systèmes.  
> L’interface est le **script des décisions** que le joueur doit aimer prendre.

Si un écran ne change pas une décision D1–D5,  
il n’a pas sa place en Phase 1.

---

*Fin de `08_UI_UX.md` — philosophie UI Phase 1, arborescence décisionnelle officielle.*
