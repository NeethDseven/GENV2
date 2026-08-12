# 01 — Core Gameplay

**Statut :** canon REBORN — boucle principale uniquement  
**Autorité :** Lead Game Designer · aligné sur `00_Philosophie.md` · créatures `07_Creatures.md`  
**Ignore volontairement ici :** bâtiments détaillés, ressources chiffrées, écrans UI  
**Suite :** `03_Phase1.md` (prologue) · `07_Creatures.md` (êtres) · `12_Rules_Of_Genesis.md` · `14` (prototype)

---

## 0. Ancrage

**Phrase produit** (`00`) :

> Le joueur dirige un Institut Scientifique chargé de créer, comprendre et préserver le vivant avant de partir explorer une galaxie vivante.

**Objet qui grossit vraiment :** le **patrimoine biologique** — pas la base, pas les stocks, pas le niveau.  
**Forme du patrimoine créatures :** une **collection d’espèces** (analyse, mutations, hybrides, musée) — **pas** de lignées ni de dynasties (`07`).

---

## 1. Contre-modèle : ce que la boucle n’est pas

GENESIS n’est **pas** un city-builder.

Boucle city-builder classique :

```
Collecter → Construire → Optimiser → Débloquer → Collecter plus
```

| City-builder | GENESIS |
|--------------|---------|
| Récompense = infrastructure qui grossit | Récompense = **patrimoine** qui s’épaissit de sens |
| Monde = terrain à aménager | Monde = **partenaire fragile** à lire |
| Temps = croissance | Temps = **rituels** (analyse, mutation, mission) |
| Perte = stock remplaçable | Perte = **histoire** (créature blessée, pureté brisée) |
| Multi greffé = course à la prod | Multi préparé = **légende et savoir du vivant** |

Si une feature ressemble à la colonne de gauche, elle est **hors boucle principale**.

---

## 2. La boucle principale

### 2.1 En une phrase

> **Tu sors chercher du vivant inconnu, tu l’analyses, tu le fais évoluer, tu l’éprouves en mission, tu l’exposes dans ta collection — puis tu recommences avec un bestiaire plus riche.**

### 2.2 Boucle formelle (5 verbes)

```
        ┌──────────────────────────────────────────────────┐
        │                                                  │
        ▼                                                  │
   RENCONTRER                                              │
   le vivant / le monde (recon, signal d’espèce)           │
        │                                                  │
        ▼                                                  │
   COMPRENDRE                                              │
   analyser — fiche, stats, pureté                         │
        │                                                  │
        ▼                                                  │
   TRANSFORMER                                             │
   muter / croiser / ou refuser                            │
        │                                                  │
        ▼                                                  │
   ÉPROUVER                                                │
   mission (risque, statut)                                │
        │                                                  │
        ▼                                                  │
   INSCRIRE                                                │
   musée, mémoire, identité de collection                  │
        │                                                  │
        └──────── le patrimoine a changé ──────────────────┘
```

**Cinq verbes.**  
**Même boucle en Phase 1 et en Phase 2** — seule l’échelle change (`00` §5 · `10`).

### 2.3 Équivalent terrain (`07` · `14`)

```
Découvrir → Analyser → Muter / Croiser → Mission → Musée
```

| Verbe `01` | Acte créature |
|------------|---------------|
| Rencontrer | Recon / signal d’espèce |
| Comprendre | Analyser (labo) |
| Transformer | Muter (− organique, pureté ↓) ou croiser |
| Éprouver | Mission (succès / épuisement / blessure) |
| Inscrire | Exposer au musée (collection, pas généalogie) |

### 2.4 Ownership (Architecture)

| Verbe | Domaines principalement responsables |
|-------|--------------------------------------|
| Rencontrer | `Expedition` · `World` · `Drones` |
| Comprendre | `Laboratory` · `Creatures` |
| Transformer | `Laboratory` · `Creatures` |
| Éprouver | `Expedition` · `Creatures` · `World` |
| Inscrire | `Museum` · `Memory` · `Heritage` |

L’UI **orchestre** ces verbes ; elle ne les **possède** pas (`08`).

---

## 3. Les cinq maillons

| Verbe | Acte joueur | Ce que ce n’est **pas** | Muscle multi (A) | Identité (B) |
|-------|-------------|-------------------------|------------------|--------------|
| **Rencontrer** | Aller au contact d’un monde et d’un vivant non maîtrisé | Farmer une case | Sites partagés, courses de découverte | Premières, scars de terrain |
| **Comprendre** | Payer temps / risque pour *savoir* qui c’est | Tooltip de stats | Valeur des génomes cartographiés | Knowledge = dignité d’export |
| **Transformer** | Mutation / croisement risqué (pureté) | Craft +2 sans coût | Licences d’hybrides / savoir | Bestiaire unique |
| **Éprouver** | Remettre la créature dans le réel | Arène de build abstraite | Fiabilité d’expé, légende | Preuves, blessures, échecs |
| **Inscrire** | Exposer, mémoriser, accepter la perte | Achievement plat | Musée public, réputation | « J’ai une collection » |

Colonnes **A** et **B** = double test Phase 1 (`00` §4.2).

La boucle se ferme quand **l’identité du joueur a bougé** :

> « Avant j’avais des signaux. Maintenant j’ai un bestiaire. »

---

## 4. L’objet de la boucle

### 4.1 Patrimoine biologique

Ce que le joueur crée, comprend, préserve et pourra emporter / faire reconnaître :

| Expression | Rôle dans la boucle |
|------------|---------------------|
| **Créatures** | Visages — collection d’espèces, pas units |
| **Musée** | Pièces remarquables (pas d’arbre généalogique) |
| **Savoir** | Ce qui est analysé et transmissible |
| **Mémoire** | Découvertes, missions, deuils, expositions |

### 4.2 Structure d’une créature (`07`)

Chaque créature possède **uniquement** :

- un nom · une espèce · une rareté  
- 4 stats : **Force · Vitesse · Résistance · Intelligence**  
- 1–2 **capacités** spéciales  
- une **pureté génétique** (baissée par les mutations)  
- un **statut** : prête · épuisée · blessée  

C’est tout. Pas de Dons, pas de lignée, pas de rôle abstrait multi-filtres.

### 4.3 Ce qui monte à chaque cycle

| Après… | Le joueur possède… |
|--------|-------------------|
| Rencontrer + Comprendre | Une **espèce** nommée, une fiche lue |
| Transformer | Stats ↑ et/ou capacité, pureté ↓, ou un **hybride** |
| Éprouver | Des **preuves**, un statut (prête / épuisée / blessée) |
| Inscrire | Une **pièce de musée**, une **mémoire**, une **promesse** |

La progression n’est pas « +1 niveau ».  
C’est : **+1 phrase que l’on peut raconter à quelqu’un d’autre**.

> « Dracoral — hybride rare, Force et Résistance hautes, pureté fragile — a tenu la mission, exposé au musée. »

Cette phrase est le **contrat social** P1↔P2 (`07`, `10`, `11`).

---

## 5. La tension qui tient la boucle

Sans friction, GENESIS devient un Pokédex plat.  
Avec friction, chaque tour est un **pari sur l’histoire**.

> **Plus tu mutes et croises, plus tu risques la pureté, le berceau et les êtres que tu aimes.**

| Pôle A | Pôle B |
|--------|--------|
| Ambition génétique | Préservation / pureté |
| Puissance d’une créature | Statut (épuisement, blessure) |
| Extraire du monde | Ne pas le briser |
| Envoyer une pièce rare en mission | La protéger au musée |
| Montrer (célébrité future) | Cacher (discrétion future) |

**Tension centrale :** enrichir le bestiaire sans détruire le berceau, ni vider le sens de ce qu’on a créé.

En multi, le même axe devient **éthique de site partagé** et **protection de collection** — pas une nouvelle boucle.

---

## 6. Micro-boucle et macro-boucle

### 6.1 Micro (session courte, 10–25 min)

```
Rencontrer (recon / signal)
    → Comprendre (analyse)
        → une décision de transformation
            (muter OU croiser OU refuser / attendre)
                → éventuellement Mission ou Musée
```

Les **drones** interviennent surtout dans **Rencontrer** (et la préparation d’Éprouver) : voir avant d’exposer le personnel et le patrimoine (`04`, charte « drones avant humains »).

### 6.2 Macro (Phase 1 — prologue)

```
Première rencontre
    → première analyse
        → première mutation mémorable
            → première mission + première exposition musée
                → diversité d’espèces + preuves d’épreuve
                    → patrimoine digne d’export (MVE)
                        → départ
```

### 6.3 Macro (Phase 2 — même verbes, échelle ↑)

```
Rencontrer (monde sauvage / site)
    → Comprendre / Transformer (camp, labo de terrain)
        → Éprouver (ops, Colosses multi)
            → Inscrire (réputation, licences, légende publique)
```

La micro nourrit la macro.  
La macro donne un **destin** à chaque micro.  
**Aucun nouveau verbe fondamental en P2.**

---

## 7. Les cinq décisions qui comptent (D1–D5)

Si une session ne pose pas au moins **deux** de ces questions, ce n’est pas GENESIS.

| # | Décision | Verbe surtout |
|---|----------|----------------|
| **D1** | **Où** aller rencontrer le vivant / le monde ? | Rencontrer |
| **D2** | **Qui** y exposer (créatures, drones, personnel) ? | Rencontrer / Éprouver |
| **D3** | **Que** chercher à analyser en priorité ? | Comprendre |
| **D4** | **Que** transformer (muter, croiser, sacrifier) — ou **refuser** ? | Transformer |
| **D5** | **Quoi** exposer / emporter, et **quoi** risquer encore ? | Inscrire (+ départ) |

L’UI REBORN s’organise autour de **ces décisions**, pas autour des systèmes (`08`).  
GENESIS (IA) **ouvre** une décision ; elle ne la **tranche** pas (`09`).

---

## 8. Feedback émotionnel

| Maillon | Émotion cible |
|---------|----------------|
| Rencontrer | Curiosité, promesse |
| Comprendre | Appropriation |
| Transformer | Ambition, peur du gâchis (pureté) |
| Éprouver | Attachement, tension |
| Inscrire | Fierté, solennité |
| Perte dans la boucle | Deuil, responsabilité |
| Boucle bouclée avec musée | « C’est *ma* collection » |
| Départ (fin macro P1) | Fierté + vertige |

Sans ces émotions, il reste un outil de recombinaison — pas GENESIS.

---

## 9. Formule condensée

### GENESIS

```
Rencontrer → Comprendre → Transformer → Éprouver → Inscrire
        → patrimoine ↑ → recommencer
```

### Terrain / créatures

```
Découvrir → Analyser → Muter → Mission → Musée
```

### City-builder (contre-modèle)

```
Extraire → Construire → Optimiser → Extraire plus
```

| | City-builder | GENESIS |
|--|--------------|---------|
| Objet qui grossit | Ville / stock | **Patrimoine vivant** |
| Décision centrale | Où placer / quoi upgrader | **Qui analyser / muter / risquer / exposer** |
| Perte | Remplaçable | **Mémorable** |
| Multi | Course à la prod | **Économie et légende du vivant** |
| Fin de Phase 1 | Saturation de map | **Départ / transmission** |

---

## 10. Pourquoi cette boucle est plus forte

### 10.1 Récompense relationnelle, pas comptable

Le city-builder fait aimer un **plan**.  
GENESIS fait aimer des **êtres** et une **collection**.

« J’ai plus » &lt; « J’ai *fait évoluer* ».

### 10.2 Unicité structurelle

Deux joueurs optimaux de city-builder convergent.  
Deux joueurs de GENESIS **divergent** (mutations, hybrides, scars, berceau, pièces de musée).  
La boucle **accumule** la différence — condition d’un MMO de biodiversité.

### 10.3 Le risque a un visage

Perdre du stock n’est rien.  
Blesser une créature rare en Extrême est une **histoire**.  
La boucle place le risque sur ce qui a été **nommé et transformé**.

### 10.4 Préparation naturelle du multijoueur

L’objet de la boucle est déjà **socialisable** :

- échange de savoir / licences  
- contrats de capture ou de recherche  
- réputation de collection  
- coopération sur l’impossible (Colosses)  
- menace / protection du patrimoine d’autrui  

Pas besoin de greffer du multi sur une course à la prod (`11`).

### 10.5 Une fin digne (Phase 1)

On ne s’arrête pas par ennui de map pleine.  
On **part** quand le patrimoine est **exportable** (MVE).  
C’est une **transmission**, pas un abandon de run.

### 10.6 Résistance au feature-creep

Test d’ajout :

> Est-ce que cela enrichit **Rencontrer**, **Comprendre**, **Transformer**, **Éprouver** ou **Inscrire** —  
> d’une façon qui passe aussi le double test A/B (`00`) ?

Sinon → hors boucle principale.

---

## 11. Démonstration par l’absurde

Si l’on retire bâtiments et ressources, mais que l’on garde :

- un monde qui réagit,  
- du vivant à rencontrer,  
- le pouvoir d’analyser et muter,  
- le risque de mission,  
- le droit d’exposer au musée,  

**le jeu existe encore.**

Si l’on retire le vivant et que l’on garde la gestion de base,  
il reste un city-builder **sans GENESIS**.

Donc la boucle principale n’est **pas** la colonie.  
La colonie n’était qu’un bruit de construction autour d’une boucle plus pure.

---

## 12. Règle d’or de complexité

> **Complexité sur les actes de la boucle (surtout Transformer et Éprouver).**  
> **Simplicité sur les êtres (fiche créature lisible) et sur l’identité du joueur (collection).**

Silhouette en cinq secondes (`07`, `12`) :

> `[Nom] — Espèce · Rareté · Capacités · Pureté`

---

## 13. Place des supports (sans les détailler)

Ces éléments **servent** la boucle ; ils ne la **remplacent** pas.

| Support | Rôle dans la boucle | Doc |
|---------|---------------------|-----|
| Drones | Rencontrer mieux, moins aveugle | `04` |
| Campus (5 instruments) | Ouvrir des capacités d’acte | `05` |
| Monnaies d’acte | Payer Comprendre / Transformer / ops | `06` |
| Créatures | Sujet de tous les verbes | `07` |
| UI des décisions | Poser D1–D5 | `08` |
| GENESIS IA | Ouvrir une lecture / décision | `09` |

Si un support devient la boucle (farm, max building), il a **échoué**.

---

## 14. Phrase-résumé

**La boucle de GENESIS est la boucle de l’évolution dirigée sous contrainte morale et écologique :**

tu rencontres le vivant,  
tu l’analyses,  
tu oses le muter ou le croiser,  
tu le confrontes au monde,  
tu l’inscris dans une collection qui te survit.

Ce qui grandit n’est pas un empire.  
C’est une **responsabilité créatrice** — unique, fragile, et partageable.

---

## 15. Signature de stabilité

| Critère | État |
|---------|------|
| 5 verbes nommés et ordonnés | Oui |
| Mapping terrain Découvrir→Musée | Oui |
| Contre-modèle city-builder | Oui |
| Objet = patrimoine / collection | Oui |
| Aligné `07` (pas de lignée) | Oui |
| Tension berceau / pureté | Oui |
| D1–D5 décisions | Oui |
| Micro / macro P1 + continuité P2 | Oui |
| Double test A/B sur les maillons | Oui |
| Feature-creep test | Oui |

**Document 01 : stabilisé pour REBORN (aligné `07`).**  
Amendements ici d’abord ; `03`, `08`, `12`, `14` s’alignent sur ces verbes.

---

*Fin de `01_Core_Gameplay.md` — boucle officielle de GENESIS REBORN.*
