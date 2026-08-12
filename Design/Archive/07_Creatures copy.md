# 07 — Créatures : système définitif

**Statut :** référence officielle de développement  
**Rôles :** Creative Director · Lead Game Designer · Biologiste de fiction  
**Posture :** aucune mécanique héritée n’est sacrée si elle trahit la vision  
**Documents frères :** `00_Vision_Refonte.md` · `01_Core_Gameplay.md` · `03_Phase1.md` · `04_Drones.md` · `06_Ressources.md` · `10_Transition_Phase2.md`  
**Canon amont (aligné) :** `docs/PHILOSOPHIE_CREATURES.md` · `docs/MODELE_BRANCHE.md`

---

## 0. Phrase guide

> **Une créature n’est pas une unité.**  
> **C’est une branche vivante du patrimoine biologique du joueur.**

> Elle n’est jamais intéressante parce qu’elle a beaucoup de statistiques.  
> Elle l’est par son **histoire**, son **adaptation**, sa **lignée**, sa place dans le **vivant**, et ce qu’elle **devient**.

> Silhouette en cinq secondes :  
> **`[Nom] — Nature X, Rôle Y, Dons… — Lignée Z`**

Si une feature contredit ces phrases, **c’est la feature qui a tort**.

---

## 1. Ce que GENESIS n’est pas (créatures)

| GENESIS n’est pas… | Conséquence design |
|--------------------|-------------------|
| Un jeu de collection de monstres | Pas de Pokédex comme win condition |
| Un RPG d’units à min-maxer | Pas de build “Force 18” |
| Un city-builder où la créature est un bâtiment mobile | Pas d’usine à biomasse anonyme |
| Un combat de mascottes | Pas de moveset PvP comme identité |
| Un inventaire de modules | Face joueur = langage vivant, pas admin |

**GENESIS est** un jeu de **création, transmission et empreinte** du vivant.  
La créature est le **visage** du patrimoine — du tutoriel jusqu’à la galaxie multi.

---

## 2. Analyse des concepts classiques “jeux de créatures”

Pour chaque concept : indispensable ? redondant ? décision ? émotion ? fusion ?

Légende verdict : **GARDER** · **FUSIONNER** · **CACHER** · **SUPPRIMER** · **P2 SEULEMENT**

---

### 2.1 Statistiques (Force, PV, Attaque…)

| | |
|--|--|
| **Indispensable ?** | Non en face joueur. |
| **Redondant ?** | Oui avec Rôle + Dons + fit. |
| **Décision ?** | Faible (min-max) — mauvaise décision pour GENESIS. |
| **Émotion ?** | Quasi nulle (“+2” n’attache pas). |
| **Verdict** | **CACHER** (moteur d’ombre si besoin). Jamais argument d’amour ou de trade. |

*Justification :* les stats enseignent l’unit. GENESIS enseigne la branche.

---

### 2.2 Niveaux / XP

| | |
|--|--|
| **Indispensable ?** | Non. |
| **Redondant ?** | Oui avec Knowledge, scars, générations. |
| **Décision ?** | “Farm XP” — toxique. |
| **Émotion ?** | Faible (progression anonyme). |
| **Verdict** | **SUPPRIMER**. La croissance = **épreuves + knowledge + Dons + génération**. |

---

### 2.3 Rareté

| | |
|--|--|
| **Indispensable ?** | Oui, **légère**. |
| **Redondant ?** | Non si ≠ puissance pure. |
| **Décision ?** | Oui (protéger, exposer, exporter). |
| **Émotion ?** | Oui (exception, fierté). |
| **Verdict** | **GARDER** comme **prestige narratif** (common → rare → exceptionnelle / Apex). Apex = légende, pas DPS. |

---

### 2.4 Évolution (forme 1 → forme 2 style Pokémon)

| | |
|--|--|
| **Indispensable ?** | Non sous forme “ligne d’évolution fixe”. |
| **Redondant ?** | Avec forge, union, adaptation. |
| **Décision ?** | Faible si scriptée ; forte si choisie. |
| **Émotion ?** | Oui si métamorphose **mémorable**. |
| **Verdict** | **FUSIONNER** dans **Transformer** : forger un Don, unir, parfois éveil — jamais arbre d’évolution catalogue. |

---

### 2.5 Talents / compétences / moves

| | |
|--|--|
| **Indispensable ?** | Non en liste longue. |
| **Redondant ?** | Oui avec **Dons** + **Rôle**. |
| **Décision ?** | Oui si ≤3 signatures. |
| **Émotion ?** | Oui si nommés et racontables. |
| **Verdict** | **FUSIONNER** → **Dons (0–3)** + **Rôle (1)**. Supprimer skill trees. |

---

### 2.6 Éléments / types

| | |
|--|--|
| **Indispensable ?** | Oui sous une seule langue. |
| **Redondant ?** | Oui si éléments + tags + modules d’adaptation + origin. |
| **Décision ?** | Oui : où l’envoyer, quoi forger. |
| **Émotion ?** | Oui (“née de la lave”). |
| **Verdict** | **FUSIONNER** tout → **Nature** (majeure ± mineure). Une langue d’adaptation. |

---

### 2.7 Races / espèces / familles taxonomiques

| | |
|--|--|
| **Indispensable ?** | Faible en jargon admin. |
| **Redondant ?** | Avec Nature + birth + lignée. |
| **Décision ?** | Faible (filtres Excel). |
| **Émotion ?** | Faible. |
| **Verdict** | **FUSIONNER** : **birth** (sauvage / unie / forgée) + **Nature** + **Lignée**. Supprimer types “naturelle/hybride/mutante” comme UI primaire. |

---

### 2.8 Mutations

| | |
|--|--|
| **Indispensable ?** | Oui comme **acte** de transformation. |
| **Redondant ?** | Si +stats basiques + flags parallèles aux Dons. |
| **Décision ?** | Oui (risque intégrité / berceau / Don). |
| **Émotion ?** | Oui (peur, ambition, cicatrice). |
| **Verdict** | **GARDER** comme rituel **Forger** → produit un **Don** ou change la Nature. Supprimer mutations filler +stats. |

---

### 2.9 ADN / gènes / modules génétiques

| | |
|--|--|
| **Indispensable ?** | Comme fiction, oui ; comme UI, non. |
| **Redondant ?** | Modules + flags + traits = triple modèle. |
| **Décision ?** | UI module = tableur, pas décision d’histoire. |
| **Émotion ?** | Faible en liste technique. |
| **Verdict** | **CACHER** (ombre moteur). Face joueur = Nature, Rôle, Dons, Intégrité. |

---

### 2.10 Arbres génétiques (tech tree du génome)

| | |
|--|--|
| **Indispensable ?** | Non. |
| **Redondant ?** | Avec Knowledge + Dons découverts. |
| **Décision ?** | “Débloquer le nœud 12” = faux profond. |
| **Émotion ?** | Non. |
| **Verdict** | **SUPPRIMER**. Le “arbre” vivant = **lignée + descendants**, pas un skill tree. |

---

### 2.11 Reproduction / croisement

| | |
|--|--|
| **Indispensable ?** | **Oui** — cœur émotionnel. |
| **Redondant ?** | Non. |
| **Décision ?** | Maximale (qui unir, à quel risque, pour quelle promesse). |
| **Émotion ?** | Maximale (naissance, espoir, deuil). |
| **Verdict** | **GARDER** comme rituel **Unir**. Compatibilité = Nature / intégrité / récit — pas admin de sexe seul si ça tue le fun sans gain. |

---

### 2.12 Âge

| | |
|--|--|
| **Indispensable ?** | Non comme chiffre. |
| **Redondant ?** | Avec génération + scars + fertilité. |
| **Décision ?** | Faible. |
| **Émotion ?** | Oui en soft (“ancienne”, “jeune héritière”). |
| **Verdict** | **FUSIONNER** en **stade de vie** narratif optionnel (jeune / mature / usée) via fertilité + scars — pas compteur d’années. |

---

### 2.13 Personnalité

| | |
|--|--|
| **Indispensable ?** | Non en liste de traits RPG. |
| **Redondant ?** | Avec Rôle + scars + journal. |
| **Décision ?** | Faible si 20 traits. |
| **Émotion ?** | Oui si **une** signature comportementale. |
| **Verdict** | **FUSIONNER** : Rôle + **1 teinte** optionnelle issue des épreuves (ex. “méfiante”, “liée à sa Maison”) — pas un simulateur de personnalité. |

---

### 2.14 Génération

| | |
|--|--|
| **Indispensable ?** | Oui. |
| **Redondant ?** | Non. |
| **Décision ?** | Oui (protéger G1, éprouver G3). |
| **Émotion ?** | Oui (continuité, héritage). |
| **Verdict** | **GARDER** (G1 fondateur, G2… ; extinction possible). |

---

### 2.15 Lignées

| | |
|--|--|
| **Indispensable ?** | **Oui** — identité portable Phase 2. |
| **Redondant ?** | Non. |
| **Décision ?** | Fonder, exposer, risquer, éteindre. |
| **Émotion ?** | Cœur du patrimoine. |
| **Verdict** | **GARDER** comme **Maison** (nom, marque, promesse, fondateur). |

---

### 2.16 Sexe / dimorphisme

| | |
|--|--|
| **Indispensable ?** | Optionnel. |
| **Redondant ?** | Si seule règle de croisement. |
| **Décision ?** | Puzzle roster si trop strict. |
| **Émotion ?** | Faible. |
| **Verdict** | **CACHER** ou soft ; compatibilité d’union pilotée par **biologie de fiction** (Nature, intégrité, Dons), pas Excel M/F. |

---

### 2.17 Équipement / items sur créature

| | |
|--|--|
| **Indispensable ?** | Non. |
| **Verdict** | **SUPPRIMER**. Les “équipements” sont des **Dons** biologiques. |

---

### 2.18 Combat / PV en combat

| | |
|--|--|
| **Indispensable ?** | Non (pas de combat comme pilier). |
| **Verdict** | **SUPPRIMER** comme identité. Risque = trauma, scar, perte, échec d’expé — pas arène. |

---

### 2.19 Synthèse de l’analyse

| Concept | Verdict |
|---------|---------|
| Stats | Cacher |
| Niveaux / XP | Supprimer |
| Rareté / Apex | Garder (légende) |
| Évolution catalogue | → Forger / Unir |
| Talents / skills | → Rôle + Dons ≤3 |
| Éléments / tags multi | → Nature |
| Races admin | → Birth + Nature + Lignée |
| Mutations filler | Supprimer ; Forger → Don |
| ADN / modules UI | Cacher |
| Arbre génétique tech | Supprimer |
| Reproduction | Garder (Unir) |
| Âge chiffré | → Stade soft |
| Personnalité liste | → Rôle + teinte |
| Génération | Garder |
| Lignée | Garder |
| Équipement | Supprimer |
| Combat unit | Supprimer |

**Résultat :** ~60–70 % des concepts classiques disparaissent ou fusionnent.  
Ce qui reste est **plus simple** et **plus chargé de sens**.

---

## 3. Identité d’une créature (5 secondes)

### 3.1 Informations visibles (silhouette)

Le joueur doit comprendre une créature **en moins de cinq secondes** :

| # | Signal | Question répondue |
|---|--------|-------------------|
| 1 | **Nom** | Qui est-elle ? |
| 2 | **Nature** | Où est-elle chez elle ? |
| 3 | **Rôle** | Que fait-elle dans le monde / en expé ? |
| 4 | **Dons (0–3)** | Qu’a-t-elle d’unique ? |
| 5 | **Lignée** | À quelle Maison appartient-elle ? |
| 6 | **État** | Est-elle prête, éprouvée, brisée, éteinte ? |
| 7 | **Prestige** (si non-banal) | Est-elle rare / Apex / légendaire ? |

**Phrase d’identité (contrat P1 ↔ P2) :**

> **[Nom]** — Nature *[X(/y)]*, Rôle *[Y]*, Dons : *[…]* — Lignée *[Z]* · *[rareté]*

Cette phrase doit fonctionner : en réserve, en mission, au musée, en camp, sur une bourse, dans un contrat.

### 3.2 Informations révélées après analyse (pas dès la rencontre)

| Info | Pourquoi pas dès le premier regard |
|------|-------------------------------------|
| **Knowledge** détaillé | Comprendre est un acte, pas un tooltip gratuit |
| **Intégrité** précise | Récompense l’analyse ; tension de forge |
| **Fertilité** | Décision d’union — après savoir |
| **Origine / parents** | Histoire qui se dévoile |
| **Scars nommés** | Mémoire d’épreuves |

### 3.3 Informations toujours cachées (jamais langue joueur)

| Caché | Pourquoi |
|-------|----------|
| Stats plates | Anti-unit |
| Modules / flags / IDs gènes | Moteur |
| Scores de prep internes | Anti-tableur |
| Formules de loot | Fairness serveur |
| Double pureté/stability | Remplacé par Intégrité |

**Règle :** complexité dans les **actes** ; simplicité dans l’**être**.

---

## 4. Structure idéale d’une créature

```
BRANCHE (créature)
├── IDENTITÉ          → reconnaissance immédiate
├── BIOLOGIE          → ce que le sang décide
├── PATRIMOINE        → ce qu’elle porte pour la Maison
├── EXPLORATION       → ce qu’elle change sur le terrain
├── HISTOIRE          → ce qu’on raconte d’elle
└── DONNÉES INTERNES  → ombre moteur (jamais UI)
```

---

### 4.1 IDENTITÉ

**Rôle :** être reconnue, aimée, nommée, exportée.

| Champ | Règle |
|-------|--------|
| Nom | Libre, renommable |
| Nature majeure (± mineure) | Exactement 1 majeure ; 0–1 mineure |
| Rôle | Exactement 1 profil |
| Dons | 0 à **3** noms maximum |
| Lignée | Marque ou « sans lignée » |
| Prestige | Rareté ; flag Apex si légende |
| Phrase d’identité | Toujours générable |

Sans identité lisible, pas de Phase 2 sociale.

---

### 4.2 BIOLOGIE

**Rôle :** décider ce qu’on peut **faire** avec elle (unir, forger, éprouver, préserver).

| Champ | Règle |
|-------|--------|
| **Intégrité** | Seul axe “qualité / santé du vivant” (bande ou 3–5 paliers lisibles) |
| **Fertilité** | fertile / usée / stérile |
| **Knowledge** | inconnu → scanné → cartographié → complet |
| **Birth** | sauvage · unie · forgée (éveil) |
| **Stade** (soft) | jeune / mature / usée — dérivé, pas XP |

**Intégrité** absorbe : pureté, stability, anomaly, corruption partielle.  
Une jauge (ou bande). Une lecture.

---

### 4.3 PATRIMOINE

**Rôle :** relier l’individu à la **Maison** et à l’export.

| Champ | Règle |
|-------|--------|
| Lignée (lien) | Fondation, promesse, symbole |
| Génération | G1, G2… |
| Parents / héritiers | Arbre simple |
| Transmissibles | Dons / Nature susceptibles de passer |
| Valeur d’export | Digne MVE ? Candidat bourse ? |

Le patrimoine n’est pas un score.  
C’est **ce qui survit** à l’individu.

---

### 4.4 EXPLORATION

**Rôle :** participation au terrain (avec drones & personnel).

| Champ | Règle |
|-------|--------|
| Fit Nature ↔ zone | À l’aise / neutre / hostile |
| Rôle en mission | Ce qu’elle apporte (éclaireur, garde, tisserand…) |
| Disponibilité | Prête, en mission, en soins, indisponible |
| Synergie de lignée | Bonus **narratif et léger** si Maison co-présente |
| Relation aux drones | Les drones **ouvrent** ; la branche **incarne** l’épreuve |

La créature n’est pas un drone.  
Les drones réduisent le risque et révèlent ;  
la branche **engage le patrimoine**.

---

### 4.5 HISTOIRE

**Rôle :** émotion, musée, légende, attachement longue durée.

| Champ | Règle |
|-------|--------|
| Scars | Épreuves nommées (pas −HP) |
| Souvenirs | Naissance, union, premier Apex, deuil… |
| Signature narrative | Une ligne de légende possible |
| Statut musée | Vivante · exposée · mémorial · éteinte |
| Célébrité | Locale → galactique (monte en P2) |

L’histoire est ce qui fait garder une créature **faible** mais **fondatrice**.

---

### 4.6 DONNÉES INTERNES

**Rôle :** simulation, balance, migration, anti-cheat.

| Exemples | Règle |
|----------|--------|
| IDs, seeds, modules ombre | Jamais affichés comme gameplay |
| Scores numériques dérivés | Calculés depuis Nature/Rôle/Dons/fit |
| Legacy flags | Migration uniquement |

**Interdit :** fuite de l’interne dans la langue joueur.

---

## 5. Les actes sur une créature (gameplay)

| Acte | Verbe de boucle | Effet sur la branche |
|------|-----------------|----------------------|
| **Rencontrer** | Rencontrer | Entre dans le vivarium (spécimen → candidate) |
| **Analyser** | Comprendre | Knowledge ↑ ; révèle Rôle, pistes de Dons, Intégrité |
| **Forger** | Transformer | Don nouveau / Nature altérée ; risque Intégrité & berceau |
| **Unir** | Transformer | Nouvelle branche (naissance) ; héritage |
| **Éprouver** | Éprouver | Mission : fit, scars, preuves, gloire ou perte |
| **Intégrer** | Éprouver / monde | Remise dans l’écosystème (co-évolution) |
| **Inscrire** | Inscrire | Fondation de lignée, galerie, mémoire |
| **Préserver** | — | Soins d’intégrité, quarantine douce |
| **Archiver** | Inscrire | Musée / mémorial si mort ou retraite |
| **Exporter / échanger** | Phase 2 | Génome, licence, prêt, don — **identité portable** |

Chaque acte doit produire soit une **décision**, soit une **émotion**, soit les deux.

---

## 6. Évolution d’une créature sur toute la vie du jeu

Le joueur doit pouvoir **aimer encore** ses premières branches après des centaines d’heures — non parce qu’elles sont “level max”, mais parce qu’elles sont **l’origine de la Maison**.

```
RECONTRE / NAISSANCE
        │
        ▼
   CROISSANCE (knowledge, premières sorties)
        │
        ▼
   ADAPTATION (fit, premier Don, scars)
        │
        ▼
   REPRODUCTION (unir) ──► DESCENDANCE
        │                        │
        ▼                        ▼
   MATURITÉ DE MAISON      NOUVELLES BRANCHES
        │                        │
        ▼                        ▼
   ÉPREUVES LÉGENDAIRES    DIVERSITÉ / SPÉCIALISATION
        │                        │
        ├────────────┬───────────┤
        ▼            ▼           ▼
   MUSÉE / MÉMORIAL  ESPÈCE /   COMMERCE
   (retraite, deuil) LIGNÉE     GALACTIQUE
                     FONDATRICE      │
                           │         ▼
                           └────► LÉGENDE BIOLOGIQUE
```

### 6.1 Première naissance / rencontre

- Silhouette partielle (souvent Nature floue, Knowledge bas).  
- Attachement naît du **nom** et du **premier récit** de mission.  
- Décision : garder, analyser en priorité, ou risquer tôt.

### 6.2 Croissance

- Pas d’XP.  
- Growth = **analyses** + **sorties** + **préservation**.  
- Le Rôle se confirme ; l’Intégrité se révèle.

### 6.3 Adaptation

- Fit aux biomes lus.  
- Premier **Don** forgé ou révélé.  
- Première scar : l’être devient **irremplaçable**.

### 6.4 Reproduction / descendance

- Union = événement.  
- Génération +1.  
- Transmission partielle de Nature / Dons / promesse de lignée.  
- Le fondateur peut **se retirer** du terrain (sacré).

### 6.5 Nouvelle espèce / branche signature

- Une forme qui n’existait pas sur le berceau.  
- Phrase d’identité unique.  
- Candidat naturel à la **Maison** et à l’export.

### 6.6 Musée

- Vivante exposée, retraitée, ou **mémorial**.  
- L’extinction n’efface pas : elle **grave**.  
- Le musée convertit la perte en **patrimoine**.

### 6.7 Commerce galactique (P2)

- On n’échange pas “une unit 12/12”.  
- On échange / license : **génome**, **adaptation**, **Don catalogué**, parfois un **être** sous règles éthiques.  
- La valeur suit la **phrase d’identité** + knowledge + intégrité + légende.

### 6.8 Légende biologique

- Célébrité de Maison.  
- “Les Cendres d’Obsidienne” connues hors du cercle du joueur.  
- Les **premières** créatures restent le **mythe d’origine** — raison de les conserver à jamais (au moins en mémoire / fondateur).

**Pourquoi garder les premières après 200 h :**

| Raison | Mécanique / fiction |
|--------|---------------------|
| Elles sont G1 | Génération non rejouable |
| Elles fondent la Maison | Lignée attachée |
| Leurs scars sont l’histoire | Musée / phrase |
| Les vendre / sacrifier coûte l’âme | Tension de design |
| Les descendants portent leur nom | Continuité |

---

## 7. Phase 1 — rôle des créatures

### 7.1 Pourquoi existent-elles ?

En Phase 1, les créatures existent pour :

1. **Enseigner** la boucle patrimoine (pas la colonie),  
2. **Créer** la première Maison,  
3. **Lire** le berceau avec un vivant adapté,  
4. **Produire** un MVE exportable,  
5. **Attacher** le joueur avant la galaxie.

Sans créatures au centre, la Phase 1 redevient un city-builder.

### 7.2 Que font-elles concrètement ?

| Domaine | Rôle de la créature |
|---------|---------------------|
| **Exploration** | Compagnon de mission ; fit Nature↔zone ; événements ; preuves d’épreuve |
| **Drones** | Complément : drones voient/prélèvent/sécurisent ; créature **incarne** le risque patrimonial |
| **Laboratoire** | Sujet d’analyse, forge, union ; source de knowledge |
| **Découvertes** | Révèlent ce que le monde peut porter ; ouvre forges liées au terrain |
| **“Colonie” / Institut** | Œuvre de l’atelier — **jamais** outil de production anonyme |
| **Galerie** | Visages de la Maison |
| **Sortie P1** | Noyau du MVE (analysées, diversifiées, éprouvées, inscrites) |

### 7.3 Ce qu’elles ne font **pas** en Phase 1

- Combattre comme armée  
- Farmer de la biomasse en idle  
- Remplacer les drones  
- Servir de trophy de stats  
- Être jetables sans conséquence narrative  

### 7.4 Arc émotionnel Phase 1 (créatures)

```
Spécimen  →  Branche nommée  →  Compagnon d’expé
    →  Œuvre forgée / unie  →  Fondateur de lignée
        →  Descendance  →  Patrimoine digne de partir
```

---

## 8. Phase 2 — évolution naturelle (mêmes règles)

**Principe :** les règles ne changent pas ; **l’échelle** et le **public** changent.

| En Phase 1 | En Phase 2 (même système) |
|------------|---------------------------|
| Explorer le berceau | Explorer des mondes partagés |
| Analyser pour soi | Analyser pour valeur / contrat |
| Forger pour la Maison | Forger pour légende / demande |
| Unir pour fonder | Unir pour alliances de sang / projets joints |
| Musée local | Réputation galactique |
| MVE export | Patrimoine **en circulation** |

### 8.1 Exploratrices spatiales

- Même composition : personnel + drones + **branches**.  
- Fit Nature ↔ monde alien = muscle déjà formé.  
- Les drones restent indispensables ; les branches restent le cœur.

### 8.2 Marchandises biologiques

- Trade de **savoir** et de **droits** plus que de “pets”.  
- Prix piloté par : phrase d’identité, knowledge, intégrité, rareté, légende, demande de Nature/Don.  
- Vendre une fondatrice doit **faire mal** (friction narrative + coût de Maison).

### 8.3 Patrimoine génétique

- Lignées = réputation de joueur.  
- Générations continuent hors berceau.  
- Extinction publique possible = drame social.

### 8.4 Objets de recherche

- Autres Instituts / joueurs étudient (contrats).  
- Knowledge élevé = licence plus chère.  
- Branche peu comprise = risque + mystère.

### 8.5 Symboles de prestige

- Apex, fondateurs, scars légendaires.  
- Galerie → renommée.  
- Pas un score plat : des **noms** connus.

### 8.6 Membres d’une alliance

- Synergie de lignée entre joueurs (projets, camps, co-élevage sous règles).  
- Une Maison peut devenir **politique biologique**.  
- Toujours la même silhouette pour se reconnaître entre alliés.

**Le joueur ne reprend jamais un tutorial “stats P2”.**  
Il emmène sa phrase d’identité.

---

## 9. Les émotions — comment le système attache

### 9.1 Pourquoi garde-t-on une vieille créature ?

| Raison | Design qui la produit |
|--------|----------------------|
| C’est **la mienne** | Nom + scars + première mission |
| Elle a **fondé** | Lien lignée G1 |
| Elle a **souffert pour moi** | Scars nommés, extraction sauvée |
| Elle est **irremplaçable** | Génération / Apex / Don unique |
| La vendre **briserait** l’histoire | Coût narratif + mécaniques de Maison |

### 9.2 Pourquoi en vend-on / cède-t-on une autre ?

| Raison | Design |
|--------|--------|
| Spécimen sans histoire | Faible valeur émotionnelle |
| Descendance abondante | G3+ de même ligne |
| Knowledge catalogué | On vend le **savoir**, parfois pas l’être |
| Besoin de place au vivarium | Contrainte de préservation |
| Contrat éthique | Licence plutôt que abandon brutal |

Le jeu doit **permettre** la cession sans encourager le sacrifice des fondateurs.

### 9.3 Pourquoi une extinction est-elle triste ?

- La **Maison** saigne.  
- Le musée ouvre un **mémorial**, pas un +buff.  
- Les descendants portent le deuil dans la phrase.  
- En P2, d’autres peuvent **savoir** qu’une ligne s’est éteinte.

L’extinction est un **événement d’histoire**, pas un “game over soft” anonyme.

### 9.4 Pourquoi une naissance est-elle importante ?

- Quelque chose **qui n’existait pas** entre dans le monde.  
- La génération avance.  
- La promesse de lignée se matérialise.  
- Le journal écrit une page.  
- Le joueur devient **auteur** du vivant.

### 9.5 Le système raconte des histoires quand…

Chaque branche peut produire au moins une phrase du type :

> « Née dans les spores, unie sous pression, Don du Silence, scar de la Crête Noire, troisième de sa Maison — encore fertile. »

Si le système ne peut pas générer de telles phrases, il est encore un tableur.

---

## 10. Rôles d’exploration (profils — 1 seul par branche)

Liste **courte** et fictionnelle (exemples officiels, extensibles avec parcimonie) :

| Rôle | Fantaisie | Apport terrain (non-combat) |
|------|-----------|------------------------------|
| **Éclaireur** | Sent les seuils du monde | Meilleure lecture / moins d’aveuglement |
| **Garde** | Présence protectrice | Moins de trauma sur l’équipe / extraction |
| **Tisserand** | Lie les vivants | Synergies, prélèvements doux, cohésion |
| **Oracle** | Lit les patterns | Indices, anomalies, pistes de Nature |
| **Briseur** | Ouvre ce qui résiste | Accès zones / obstacles environnementaux |
| **Chimère** | Porte l’étrange | Polyvalence au prix d’intégrité / stabilité |

Un Rôle ≠ six stats.  
Un Rôle = **une promesse de fiction** tenue en mission.

---

## 11. Dons (0 à 3)

### 11.1 Règles

- Maximum **3** Dons nommés par branche.  
- Chaque Don change la **phrase d’identité**.  
- Un Don s’obtient par : révélation (analyse), **forge**, transmission (union), épreuve rare.  
- Forger un 4ᵉ Don force un **choix** (remplacer / refuser) — pas d’inventaire infini.

### 11.2 Exemples d’esprit (non exhaustif)

| Don | Émotion / décision |
|-----|-------------------|
| Adaptation Extrême | Où la Maison peut vivre |
| Lien de Lignée | Voyager ensemble |
| Fardeau Héroïque | Puissance au prix de la fertilité / vie |
| Mémoire Génétique | Transmission aux enfants |
| Catalyseur | Qualité des unions futures |

Les Dons sont le **remplacement définitif** des skills + modules + flags joueur.

---

## 12. Nature (seule langue d’adaptation)

| Règle | Détail |
|-------|--------|
| 1 Nature majeure | Obligatoire |
| 0–1 Nature mineure | Optionnelle (tension / richesse) |
| Fit | Dérivé Nature ↔ biome / monde |
| Forge élémentaire | Peut déplacer majeure/mineure au risque de l’intégrité |

**Supprimé en face joueur :** multi-tags modules, multi-éléments primary/secondary/hybrid comme jargon parallèle.

---

## 13. Intégrité, fertilité, knowledge (trio biologique)

| Axe | Lit | Décision |
|-----|-----|----------|
| **Intégrité** | Santé du vivant | Forger ? Unir ? Exposer au danger ? |
| **Fertilité** | Avenir de la lignée | Qui est le parent ? Qui est stérile par Don ? |
| **Knowledge** | Ce qu’on a compris | Valeur scientifique / export / analyse restante |

Trois axes.  
Pas dix thermomètres.

---

## 14. Lien avec le reste de GENESIS

| Système | Relation à la créature |
|---------|------------------------|
| **Drones** | Servent la rencontre et la survie des branches |
| **Bureau** | Lieu où on les emmène éprouver |
| **Labo** | Lieu où on les comprend et transforme |
| **Vivarium** | Lieu où on les porte et soigne |
| **Galerie** | Lieu où on les inscrit |
| **Baie** | Lieu d’où elles **partent** avec le joueur |
| **Ressources** | Organique + prélèvements paient les actes sur elles |
| **Berceau** | Leur forge pèse sur la stabilité |

---

## 15. Simplicité stricte — purge finale

### 15.1 Supprimé sans hésiter

| Système | Raison |
|---------|--------|
| Stats visibles | Ni émotion ni bonne décision |
| Niveaux / XP | Farm anti-vision |
| Modules / flags UI | Redondants avec Dons |
| Pureté + stability + anomaly | → Intégrité |
| Mutations +stats | Creux |
| Arbres génétiques tech | Faux profondeur |
| Évolution catalogue Pokémon | → Forger / Unir |
| Skill trees / movesets | → Rôle + Dons |
| Équipement créature | Anti-fiction |
| Combat comme identité | Hors piliers |
| 15 filtres techniques réserve | Excel |
| Types admin naturelle/hybride/mutante | → Birth |
| Créature = prod biomasse | Œuvre ≠ usine |
| Multi-éléments parallèles à Nature | Une langue |

### 15.2 Fusionné

| Avant | Après |
|-------|--------|
| Éléments + tags adaptation | **Nature** |
| Talents + modules + capacités | **Dons ≤3** + **Rôle** |
| Pureté / stability / anomaly / corruption partielle | **Intégrité** |
| Âge + usure | **Fertilité** + stade soft |
| Personnalité liste | **Rôle** + teinte d’épreuve |
| Rareté + Apex power | **Prestige narratif** |
| Histoire + journal + scars | **HISTOIRE** unifiée |

### 15.3 Système final (deux fois plus simple, deux fois plus profond)

**Face joueur — ce qui compte vraiment :**

```
Nom
Nature
Rôle
Dons (≤3)
Lignée + Génération
État
Intégrité · Fertilité · Knowledge
Scars / Mémoire
Prestige (si exception)
```

**Actes :**

```
Rencontrer · Analyser · Forger · Unir · Éprouver · Intégrer · Inscrire · Préserver · Archiver · (P2) Exporter
```

**Émotion :**

```
Naissance · Attachement · Ambition · Peur · Deuil · Fierté · Légende
```

C’est **tout**.  
Le reste est ombre ou Phase 2 sociale autour du **même** objet.

### 15.4 Impression de profondeur

La profondeur naît de :

1. **Combinatoire** Nature × Rôle × Dons × Lignée × Monde  
2. **Temps** (générations, scars, mémoire)  
3. **Risque** (intégrité, berceau, perte)  
4. **Choix moraux** (qui exposer, qui vendre, qui fonder)  
5. **Lisibilité sociale** (une phrase que d’autres comprennent)

Pas du nombre de champs.

---

## 16. Critères d’acceptation (référence QA design)

Le système créatures est validé si :

1. Un joueur décrit une branche en **une phrase** en <5 s.  
2. Il s’**attache** à au moins une créature par run.  
3. Il peut expliquer **pourquoi** il refuse de sacrifier le fondateur.  
4. Il ne demande **jamais** “c’est quoi sa Force ?”.  
5. Une extinction produit un **silence** / mémorial, pas un shrug.  
6. La même fiche est **valide** en P2 sans relearn.  
7. Les filtres réserve tiennent en **≤6** axes (Nature, Lignée, Rôle, Disponibilité, Prestige, Knowledge).  

Échec si le labo ressemble à un tableur de modules.

---

## 17. Contrat de développement

| Règle | Obligation |
|-------|------------|
| Silhouette ≤7 signaux | UI |
| Dons ≤3 | Data + UX |
| Une Intégrité | Langue unique |
| Une Nature (langue d’adaptation) | Plus de multi-systèmes fit |
| Pas de stats face joueur | Jamais |
| Lignée portable | Export P2 |
| Histoire non optionnelle | Scars / musée |
| Toute feature créature | Émotion **ou** décision **ou** muscle P2 |

**Test unique :**

> Est-ce que cela rend la branche plus **vivante**, plus **racontable**, ou plus **digne d’être emportée en galaxie** ?

Sinon → dehors.

---

## 18. Résumé exécutif

| | |
|--|--|
| **Nom du modèle** | Branche |
| **Unité fondamentale du jeu** | Patrimoine (la branche en est le visage) |
| **Lisibilité** | Phrase d’identité |
| **Biologie joueur** | Nature · Rôle · Dons · Intégrité · Fertilité · Knowledge |
| **Transmission** | Lignée · Génération · Union |
| **Temps long** | Scars · Musée · Légende |
| **Phase 1** | Fonder la Maison |
| **Phase 2** | Porter la Maison au regard des autres |
| **Interdit** | Unit, XP, tableur, farm, combat-identité |

---

*Fin de `07_Creatures.md` — référence officielle du système de créatures GENESIS.*  
*Toute implémentation, UI, balance ou feature créature se mesure à ce document.*
