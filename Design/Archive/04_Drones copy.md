# 04 — Drones : pilier d’exploration scientifique

**Rôle :** Lead Gameplay Designer  
**Statut :** design from scratch (aucune dette d’implémentation)  
**Documents frères :** `01_Core_Gameplay.md` · `03_Phase1.md` · `10_Transition_Phase2.md`

---

## 0. Pitch

> Les drones sont les **yeux, les mains et la mémoire de terrain** de l’Institut.  
> Sans eux, le joueur **devine**. Avec eux, il **observe, cartographie, prélève, prépare et analyse** — plus loin, plus sûr, plus juste.

Ils ne combattent **jamais**.  
Ils rendent l’exploration **intelligente**.

---

## 1. Problème à résoudre

Aujourd’hui (et dans tout design “stock + slot”), les drones sont :

- une **ressource de colonie** à produire,  
- un **chiffre** dans le loadout,  
- un bonus plat de survie.

Conséquence : zéro décision mémorable, zéro fantasy scientifique, zéro muscle Phase 2.

**Cible :**

Les drones deviennent l’un des **trois piliers** de l’expérience terrain :

| Pilier | Rôle |
|--------|------|
| **Branches (créatures)** | Cœur émotionnel & patrimoine |
| **Personnel** | Jugement scientifique & présence humaine |
| **Drones** | Portée d’observation, cartographie, prélèvement, réduction de risque |

Si le joueur peut ignorer les drones et “quand même bien jouer”, le pilier a échoué.

---

## 2. Fantaisie & promesse

### 2.1 Fantaisie joueur

> Je suis directeur d’un **parc d’instruments d’expédition**.  
> Je configure des **vols de reconnaissance**, des **filets de prélèvement**, des **balises de carte**.  
> Je choisis ce que je risque : une sonde, un échantillon, une branche aimée.  
> Plus mon parc est juste, plus le monde s’ouvre **sans** que je le force.

### 2.2 Promesse de gameplay

Les drones permettent de :

1. **Voir** ce qui était opaque  
2. **Cartographier** ce qui était flou  
3. **Prélever** ce qui était hors de portée  
4. **Réduire le risque** sans supprimer le danger  
5. **Préparer** la prochaine expédition avec de l’intel réelle  
6. **Nourrir l’analyse** labo avec des données de terrain  
7. **Préparer la Phase 2** (présence hors berceau = drones)

### 2.3 Ce que les drones ne sont **pas**

| Interdit | Pourquoi |
|----------|----------|
| Unités de combat | Hors fantasy, hors scope |
| Munitions / spam farm | Tue la décision |
| Compteur +999 | Illisible, city-builder |
| Remplacement total des créatures | Les branches restent le cœur |
| Win condition (“max drones”) | Instruments, pas trophée |

---

## 3. Principes de design

| # | Principe | Implication |
|---|----------|-------------|
| 1 | **Instruments, pas soldats** | Observation, carte, prélèvement, logistique scientifique |
| 2 | **Rareté lisible** | Pool petit (ex. 4–12 opérationnels) ; chaque unité compte |
| 3 | **Décision avant puissance** | Configurer > stacker |
| 4 | **Intel avant force** | Un bon vol change ce que la mission *est* |
| 5 | **Risque déplacé, pas effacé** | Moins de pertes humaines/branches ; drones sacrifiables à coût réel |
| 6 | **Données = progression** | Cartes, signatures, échantillons de qualité |
| 7 | **Muscle Phase 2 dès P1** | Même verbes hors berceau (camp = consommation drones) |
| 8 | **Pas de combat** | Menaces = environnement, fuite, perte, corruption de données — jamais DPS |

---

## 4. Boucle drones (gameplay complet)

Les drones ne sont pas un mini-jeu isolé.  
Ils s’insèrent dans la boucle GENESIS comme **amplificateur de Rencontrer / Comprendre / (préparer) Éprouver**.

```
                    ┌─────────────────────────────────────┐
                    │         PARC DE DRONES              │
                    │  (état, types, charge utile, usure) │
                    └───────────────┬─────────────────────┘
                                    │
           ┌────────────────────────┼────────────────────────┐
           ▼                        ▼                        ▼
    RECONNAISSANCE            EXPÉDITION              RETOUR LABO
    (vol autonome             (loadout mixte)         (données +
     ou pré-mission)                                   prélèvements)
           │                        │                        │
           ▼                        ▼                        ▼
    Carte / indices           Risque réduit            Analyse enrichie
    POI / signatures          Meilleur prélèvement     Knowledge +
    Risque anticipé           Nouvelles rencontres     plans d’expé
           │                        │                        │
           └────────────────────────┴────────────────────────┘
                                    │
                                    ▼
                         PROCHAINE DÉCISION
                    (où aller, quoi emporter,
                     quoi risquer, quoi forger)
```

### 4.1 Trois modes d’usage

| Mode | Quand | Ce que le joueur fait | Résultat |
|------|-------|----------------------|----------|
| **A. Reconnaissance** | Avant une grosse sortie ou pour ouvrir une zone | Envoie 1–3 drones **sans** (ou presque sans) personnel | Carte locale, indices de vie, niveau de risque, POI |
| **B. Escorte scientifique** | Pendant l’expédition principale | Compose drones + humains + branches | Observation live, prélèvement, extraction douce, réduction de risque |
| **C. Relais de données** | Après / entre missions | Traite les retours au Bureau / Labo | Layers de carte, échantillons typés, bonus d’analyse |

Le joueur qui n’utilise que B joue “OK”.  
Le joueur qui enchaîne **A → B → C** joue **GENESIS**.

---

## 5. Anatomie d’un drone

### 5.1 Identité minimale

Chaque drone (ou chaque **escadre** compacte — voir §5.3) expose :

| Attribut | Signification |
|----------|----------------|
| **Classe** | Rôle principal (voir §6) |
| **Charge utile** | Module actif équipé (1 seul, lisible) |
| **Intégrité** | Usure / dommages (opérationnel · abîmé · hors-service) |
| **Adaptation terrain** | Tags mineurs (froid, pression, spores…) — **pas** un arbre de combat |
| **Signature** | Bruit / discrétion face au vivant (impact prélèvement, pas DPS) |

Pas de niveau 50.  
Pas d’XP de kill.  
Progression = **meilleures configs + meilleur usage + parc mieux entretenu**.

### 5.2 Charge utile (modules) — cœur des décisions

Un drone = une **classe** + **un module** à la fois.

Changer de module = **reconfiguration** (temps court + logistique légère à l’Institut).  
En mission, le module est **figé** (décision avant départ).

Exemples de modules (non combat) :

| Module | Classe typique | Effet gameplay |
|--------|----------------|----------------|
| **Optique longue portée** | Observateur | Révèle POI distants, +qualité d’observation |
| **Spectre bio** | Observateur | Détecte signatures de vie rares / Apex potentiels |
| **Balise cartographe** | Cartographe | Remplit la grille de carte, ouvre “couloirs sûrs” |
| **Sonde de sol** | Cartographe | Révèle essences / éléments de zone |
| **Filet doux** | Préleveur | Capture / prélèvement vivant à faible trauma |
| **Micro-carottage** | Préleveur | Échantillons de haute pureté pour analyse |
| **Stabilisateur d’extraction** | Soutien | Réduit perte de branches/personnel au retour |
| **Relais de télémétrie** | Soutien | Améliore le brief de risque + journal de mission |
| **Conteneur stérile** | Préleveur / Soutien | Préserve l’intégrité des prélèvements jusqu’au labo |

**Règle :** un module change **ce que la mission peut accomplir**, pas un % de dégâts.

### 5.3 Unité de gestion : le “Vol”

Pour éviter la micro-gestion de 12 unités individuelles en UI :

> Le joueur gère des **Vols** (1 à 3 drones liés, même classe ou mixte simple).

| Vol | Taille | Exemple |
|-----|--------|---------|
| Léger | 1 drone | Sonde unique en zone extrême |
| Standard | 2 drones | Observateur + Préleveur |
| Complet | 3 drones | Cartographe + Observateur + Soutien |

Chaque Vol a un **rôle affiché** dans le loadout (icône + une ligne d’effet).  
Pertes : un drone du Vol peut être **perdu / abîmé** ; le Vol se dégrade.

Pool global exemple Phase 1 mature : **6–10 drones** → **3–5 Vols** max déployables (pas tous en même temps).

---

## 6. Classes de drones (piliers fonctionnels)

Quatre classes. Pas plus. Toutes **non-combat**.

### 6.1 Observateur — *voir sans forcer*

**Fantaisie :** œil de l’Institut.

| Capacité | Gameplay |
|----------|----------|
| Observation | Révèle comportements, patterns, “humeurs” de zone |
| Signatures | Chance de détecter vivant rare / Apex / anomalie |
| Réduction d’incertitude | Transforme “risque flou” en “risque nommé” |
| Découvertes | POI “nid”, “migration”, “fleurissement”, “silence inquiétant” |

**Sans Observateur :** la mission est un jet plus aveugle ; moins d’événements de découverte pure.

**Décision :**  
*Est-ce que je brûle un Observateur en solo pour savoir si la zone vaut une expédition complète ?*

### 6.2 Cartographe — *rendre le monde lisible*

**Fantaisie :** main qui dessine la carte.

| Capacité | Gameplay |
|----------|----------|
| Grille de connaissance | Remplit le % de **lecture de zone** (pas fog of war militaire) |
| Couloirs | Marque chemins à moindre risque pour la prochaine sortie |
| Couches | Biome / élément / stabilité locale / traces de vie |
| Mémoire durable | La carte **reste** — progression permanente du berceau |

**Sans Cartographe :** on peut explorer, mais on **réapprend** trop ; les zones restent “brumeuses”.

**Décision :**  
*Est-ce que j’investis ce départ en pure cartographie (peu de loot vivant) pour ouvrir le mid-game ?*

**Phase 2 :** cartographier un monde partagé = valeur sociale / de camp.

### 6.3 Préleveur — *ramener le fragile*

**Fantaisie :** mains douces de la science.

| Capacité | Gameplay |
|----------|----------|
| Capture douce | +chance de ramener un spécimen vivant intact |
| Qualité d’échantillon | Prélèvements de grade supérieur → analyse plus profonde / plus vite |
| Accès | Zones où le personnel ne peut pas prélever sans tout casser |
| Essences | Récolte d’essences de terrain liées au biome |

**Sans Préleveur :** rencontres possibles mais **moins de vivant exportable** ; plus de “on a vu mais pas ramené”.

**Décision :**  
*Filet doux (sûr, moins de rare) ou micro-carottage agressif (meilleure data, plus de trauma / risque écologique) ?*

### 6.4 Soutien d’expédition — *revenir avec l’histoire*

**Fantaisie :** fil d’Ariane et civière.

| Capacité | Gameplay |
|----------|----------|
| Réduction de risque | Baisse blessures / trauma / perte de données |
| Extraction | Sauve une branche ou un opérateur d’un échec partiel |
| Télémétrie | Meilleur brief post-mission + logs pour le journal |
| Conteneur | Les prélèvements arrivent **analysables** (sinon dégradés) |

**Sans Soutien :** l’exploration reste possible mais **punitive** ; le joueur hésite à emmener des branches aimées.

**Décision :**  
*Est-ce que je sacrifie un slot de découverte pour garantir le retour du fondateur ?*

---

## 7. Les cinq verbes du gameplay drones

Tout le design se résume à cinq verbes jouables.

### 7.1 Observer

**Acte :** déployer un Observateur (mode A ou B).

**Outputs possibles :**

- indice de vie (“présence forte / faible / anormale”),  
- comportement (“migration”, “territoire”, “symbiose”),  
- alerte douce (“zone instable”, “contamination latente”),  
- rare : **contact non forcé** (créature repérée sans capture).

**Nouvelle découverte typique :**  
le joueur *sait* qu’un Apex potentiel existe **avant** de risquer sa Maison.

### 7.2 Cartographier

**Acte :** Vol Cartographe sur une zone.

**Outputs :**

- +lecture de zone (permanente),  
- révélation de sous-zones / strates,  
- marqueurs de **prochaine expédition** (objectifs concrets),  
- parfois : chemin vers une zone autrefois “verrouillée par l’ignorance”.

**Progression :** la carte du berceau est un **patrimoine de savoir** (exporte une “carte postale enrichie” en P2, pas le mesh complet).

### 7.3 Prélever

**Acte :** Préleveur en contact avec le vivant / le sol / l’essence.

**Outputs :**

- spécimen vivant,  
- échantillon de grade (standard / soigné / exceptionnel),  
- essence de biome,  
- parfois : prélèvement **sans** rencontrer (trace génétique seule).

**Trade-off :**

| Approche | Avantage | Coût |
|----------|----------|------|
| Douce | Intégrité du vivant + berceau | Moins de rare |
| Agressive | Data / rare | Trauma, stabilité, drone exposé |

### 7.4 Analyser (relais terrain → labo)

Les drones ne remplacent pas le Laboratoire.  
Ils **préparent** l’analyse.

| Donnée drone | Effet labo |
|--------------|------------|
| Observation comportementale | +vitesse knowledge “Rôle” |
| Spectre bio | Révèle pistes de **Nature** / élément plus tôt |
| Échantillon grade élevé | Analyse plus profonde au premier passage |
| Carte de zone | Contexte : fit Nature↔biome déjà partiellement connu |
| Télémétrie de mission | Journal + scars mieux qualifiés |

**Fantasy :** “Mon labo voit déjà le terrain.”

### 7.5 Préparer (l’expédition suivante)

L’intel drone transforme l’écran de préparation :

| Sans intel | Avec intel |
|------------|------------|
| Risque : “Élevé ?” | Risque : “Élevé — spores + pression ; éviter sans Remorque” |
| Objectif vague | Objectifs : “POI Nid / essence volcanique / trace Apex” |
| Loadout par habitude | Loadout **réponse** à la carte |
| Surprise pure | Surprise **choisie** (on sait ce qu’on accepte de ne pas savoir) |

Préparer = **décision éclairée**, pas tableur.

---

## 8. Intégration aux expéditions

### 8.1 Composition d’une sortie

Slots conceptuels (nombres de design, pas de code) :

| Slot | Contenu | Tension |
|------|---------|---------|
| Personnel | 1–3 | Jugement, analyse terrain, présence |
| Branches | 0–2 | Cœur, fit, émotion, risque patrimonial |
| Vols de drones | 1–3 | Intel, prélèvement, sécurité scientifique |

**Règles d’âme :**

- Certaines zones **exigent** un type de Vol (ex. abysses = Observateur pression + Préleveur).  
- Emporter trop de drones **réduit** les slots branches (choix identité vs instruments).  
- Emporter trop de branches sans Soutien = **imprudence narrative**.

### 8.2 Phases d’une mission (avec drones)

```
1. APPROCHE      — Cartographe / Observateur réduisent le brouillard
2. CONTACT       — Observation du vivant (sans combat)
3. INTERACTION   — Prélèvement / présence de branches / lecture
4. COMPLICATION  — Environnement, fuite du sujet, perte de signal, drone abîmé
5. EXTRACTION    — Soutien / conteneur / choix d’abandonner un Vol
6. RETOUR        — Données livrées au labo + carte mise à jour
```

Chaque phase peut produire un **événement non-combat** piloté par la config drones.

### 8.3 Réduction de risque (mécanique claire)

Les drones ne mettent pas le succès à 100 %.  
Ils **déplacent** le risque :

| Risque initial | Avec bons drones | Trade-off |
|----------------|------------------|-----------|
| Perte humaine | ↓ | Drone exposé |
| Trauma de branche | ↓ | Moins de slots pour 2ᵉ branche |
| Mission aveugle | ↓ | Temps / Vol de recon avant |
| Échantillon perdu | ↓ | Conteneur occupe un module |
| Échec total | ↓ léger | Toujours possible en zone extrême |

**Formule d’intention (design, pas code) :**

> Risque_perçu = Danger_zone − Intel − Fit_branches − Couverture_drones  
> Risque_réel reste non nul ; l’intel le rend **jouable et juste**.

### 8.4 Nouvelles découvertes rendues possibles **uniquement** par drones

| Découverte | Condition drones | Pourquoi c’est fun |
|------------|------------------|---------------------|
| **Trace spectrale** | Observateur + spectre bio | Apex / rare “annoncé” |
| **Couloir sûr** | Cartographe | Ouvre une route narrative |
| **Prélèvement fantôme** | Préleveur sans contact | ADN sans capturer (éthique / stabilité) |
| **Carte d’essences** | Sonde de sol | Forge élémentaire informée |
| **Comportement de lignée sauvage** | Observation longue | Idées d’union / Dons |
| **Zone silencieuse** | Observateur en échec partiel | Mystère : absence de vie = info |
| **Échantillon exceptionnel** | Filet doux + conteneur | Analyse “complete” plus tôt |

Sans drones, ces couches **n’existent pas** — on ne peut pas les farmer autrement.

---

## 9. Modes de mission drones (contenus dédiés)

En plus de l’escorte, des **missions purement instrumentales** existent.

### 9.1 Vol de reconnaissance (court)

- Coût : 1–2 Vols, peu/pas de personnel  
- Durée : plus courte qu’une expé complète  
- But : carte + indices + risque nommé  
- Loot vivant : rare (volontairement)  
- Risque : perte / usure drones, rarement personnel  

**Rôle dans la boucle :** le joueur intelligent **recon** avant d’envoyer le fondateur.

### 9.2 Campagne de cartographie (méso)

- Objectif multi-sorties : porter une zone à “lue”  
- Récompense : déblocage de strates / POI / stabilité mieux comprise  
- Feel : travail d’Institut, pas farm  

### 9.3 Prélèvement ciblé

- Intel préalable obligatoire (sinon malus fort)  
- Objectif : un spécimen / une essence précise  
- Préleveur + Observateur recommandés  
- Soutien si la cible est fragile ou l’extraction dure  

### 9.4 Relais d’observation longue (asynchrone)

- Un Vol reste “posé” en balise (timer)  
- Au retour : log comportemental, migration, alerte stabilité  
- Peut révéler une **fenêtre** (événement temporel) pour une capture unique  

**Phase 2 :** même structure = “yeux” sur un monde partagé.

---

## 10. Économie & cycle de vie du parc

### 10.1 Taille du parc

| Stade Phase 1 | Drones op. | Vols max en sortie |
|---------------|------------|---------------------|
| Arrivée | 2–3 | 1 |
| Mid | 5–7 | 2 |
| Mature | 8–10 | 2–3 |

Assez pour des **choix**, jamais assez pour tout couvrir.

### 10.2 Obtenir des drones

Sources **alignées science**, pas usine :

| Source | Feel |
|--------|------|
| Dotation d’arrivée | Tutoriel |
| Amélioration Bureau des expéditions | Capacité d’Institut |
| Récupération de carcasses / réassemblage | Conséquence de terrain |
| “Fabrication” lente hors loop principale | Rare, chère, **jamais** action filler |

**Supprimé :** “produire 5 drones” comme clic de session.

### 10.3 Usure, réparation, perte

| État | Effet |
|------|--------|
| Opérationnel | Full |
| Abîmé | Malus ou module offline jusqu’à réparation |
| Hors-service | Slot mort jusqu’à atelier |
| Perdu | Retiré du parc (mémoire journal possible) |

Réparation = temps + logistique légère à l’Institut.  
Perte = **douleur réelle** → chaque recon a un poids.

### 10.4 Reconfiguration

- Changer module / re-composer un Vol : action d’Institut (courte).  
- Interdit en mission.  
- Encourage la **préparation** comme skill.

---

## 11. Décisions joueur (checklist de design)

Si le joueur ne se pose pas régulièrement ces questions, le pilier est mort.

### Avant la sortie

1. Est-ce que je fais un **vol de recon** d’abord ?  
2. Quelle **classe** manque à mon intel actuel ?  
3. Quel **module** fixe mon intention (voir / cartographier / ramener / sécuriser) ?  
4. Est-ce que je prends un Soutien pour protéger une branche, au prix d’une découverte ?  
5. Combien de drones je **garde en réserve** pour la sortie suivante ?

### Pendant la sortie (choix narratifs / résolution)

6. J’abandonne un drone pour sauver un prélèvement exceptionnel ?  
7. J’interromps pour cartographier au lieu de forcer le contact ?  
8. Je prélève **doux** (berceau + intégrité) ou **agressif** (data) ?

### Au retour

9. Qu’est-ce que ces données changent à mon **plan d’analyse** ?  
10. Quelle zone devient **prioritaire** grâce à la carte ?  
11. Est-ce que mon parc abîmé m’oblige à une session “atelier / recon légère” ?

### Patrimoine

12. Est-ce que j’expose le **fondateur** sans Soutien drone ? (folie / bravoure)  
13. Est-ce qu’une Maison “des Abysses” s’écrit grâce à des vols de sondes mémorables ?

---

## 12. Lien avec les créatures (coexistence)

Les drones **servent** les créatures ; ils ne les remplacent pas.

| Situation | Drone | Branche |
|-----------|-------|---------|
| Premier contact inconnu | Observateur idéal | Risquée |
| Fit Nature déjà connu | Soutien / Préleveur | Compagnon fort |
| Capture d’un rare | Préleveur + intel | Optionnelle |
| Épreuve d’un Don | Moins critiques | **Cœur** de la mission |
| Fondation de légende | Peuvent être cités dans le journal | Héros de la phrase d’identité |

**Règle d’or :**  
une découverte purement drone est un **indice** ou un **échantillon** ;  
une **branche** reste le visage du patrimoine.

---

## 13. Lien Laboratoire & knowledge

Pipeline idéal :

```
Vol Observateur / Cartographe
        → indices + carte
                → Expédition avec Préleveur
                        → échantillon / spécimen
                                → Labo (analyse accélérée / ciblée)
                                        → Nature · Rôle · pistes de Dons
                                                → décision Transformer éclairée
```

Sans drones, le labo travaille **à l’aveugle** (plus lent, plus d’impasses).  
Avec drones, le labo travaille **comme un Institut** (hypothèses → terrain → validation).

---

## 14. UI / UX (intentions)

### 14.1 Où vivent les drones

| Écran | Contenu |
|-------|---------|
| **Bureau des expéditions** | Parc, Vols, reconfig, recon, prep loadout |
| **Préparation de mission** | Slots Vols + effets en une ligne + risque nommé grâce à l’intel |
| **Carte du berceau** | Layers issus des Cartographes |
| **Labo** | “Données de terrain” attachées à un spécimen / une zone |
| **Journal** | Pertes de drones, vols héroïques, premières cartes |

**Pas** à côté de la biomasse comme score de colonie.

### 14.2 Lisibilité

- Icônes de **classe** + pastille de **module**.  
- États : opérationnel / abîmé / perdu.  
- Effet en **langage humain** : “Réduit le trauma à l’extraction”, pas “+12 prep”.  
- Intel de zone en **bande unique** : Inconnu · Entrevue · Lue · Cartographiée.

### 14.3 Feedback émotionnel

| Événement | Feel |
|-----------|------|
| Première carte d’une zone | Maîtrise scientifique |
| Trace Apex | Excitation contenue |
| Drone perdu pour un échantillon unique | Sacrifice mémorable |
| Extraction sauvée | Soulagement |
| Analyse “déjà nourrie par le terrain” | Fluidité Institut |

---

## 15. Progression Phase 1 (arc drones)

| Acte | Relation aux drones |
|------|---------------------|
| **I — Installation** | Apprendre recon → carte → sortie ; premier Préleveur ; première usure |
| **II — Création** | Protéger les unions / fondateurs avec Soutien ; prélever pour forger ; zones extrêmes gated par config |
| **III — Maturité** | Cartographie quasi-complète des zones utiles ; vols ciblés ; sélection de ce qui mérite l’export |
| **Clôture** | Parc de drones = savoir-faire d’explorateur (muscle P2), pas un score exporté comme “12 drones” |

Ce qui voyage en Phase 2 n’est pas le hangar.  
C’est la **compétence** : composer des Vols, lire une carte, prélever juste, risquer un instrument plutôt qu’une Maison.

---

## 16. Préparation Phase 2 (contrat explicite)

| Muscle entraîné en P1 | Usage P2 |
|-----------------------|----------|
| Recon avant engagement | Approcher un monde partagé sans le casser |
| Cartographier une zone | Camps, routes, claims scientifiques soft |
| Prélever avec éthique / risque | Qualité de génomes mis en bourse |
| Gérer un parc limité | Coût de **présence** hors berceau |
| Sacrifier un drone vs une branche | Priorités de patrimoine en multi |
| Lire une bande de risque nommée | Coordination / contrats d’expé |
| Observation longue (balise) | Surveillance de sites galactiques |

### Ce que la Phase 2 consomme

- Poser un camp = **drones en maintenance permanente**.  
- Tenir un site = Vols de balise + rotation d’usure.  
- Une Maison réputée “exploratrice” se reconnaît à ses **protocoles de vol**, pas à ses murs.

### Ce que la Phase 2 n’introduit **pas** via les drones

- Combat drone vs drone  
- Deathball de 200 unités  
- Pay-to-win de production  

---

## 17. Anti-patterns (à refuser)

| Anti-pattern | Pourquoi c’est mortel |
|--------------|----------------------|
| Drones = +% succès plat | Zéro décision |
| Production spam dashboard | City-builder |
| 15 classes / 40 modules | Illisible |
| Combat “défensif” déguisé | Scope creep, fausse fantasy |
| Drones remplacent les créatures | Tue le cœur GENESIS |
| Carte 100 % dès le début | Tue l’exploration |
| Aucune perte possible | Instruments sans poids |
| Intel qui spoile tout | Tue le mystère ; l’intel doit **nommer**, pas **résoudre** |

---

## 18. Exemples de sessions (feel)

### 18.1 Session 15 min — “Recon puis décision”

1. Vol Observateur + Cartographe sur zone volcanique.  
2. Résultat : essence rare détectée, risque “souffle thermique”, POI fissure.  
3. Joueur **reporte** la sortie branches ; reconfigure un Préleveur + Soutien.  
4. Session suivante : capture réussie, échantillon exceptionnel → labo heureux.

### 18.2 Session 25 min — “Sauver la Maison”

1. Expédition avec fondateur (épreuve d’un Don).  
2. Loadout : 1 Observateur, 1 Soutien (pas de 2ᵉ Préleveur).  
3. Complication : extraction ratée partielle.  
4. Soutien sauve le fondateur ; Observateur perdu.  
5. Journal : “La Maison vit. L’œil de l’Institut s’est éteint sur la crête.”

### 18.3 Session 20 min — “Carte avant ambition”

1. Deux vols de cartographie pure.  
2. Peu de loot.  
3. Une strate s’ouvre ; stabilité locale mieux lue.  
4. Le joueur sait **où** unir / intégrer sans brûler le berceau.

---

## 19. Critères d’acceptation (design / playtest)

Le pilier drones est validé si les joueurs disent :

1. « Sans recon, j’ai l’impression de jouer à l’aveugle. »  
2. « Composer mes Vols est aussi important que choisir mes créatures. »  
3. « J’ai découvert un truc que je n’aurais jamais vu sans sonde. »  
4. « J’ai sacrifié un drone pour sauver une branche — et ça valait le coup. »  
5. « La carte de mon monde, c’est mon travail. »  
6. « Je me sens prêt pour des camps ailleurs : je sais déjà gérer un parc. »  

Et **ne disent pas** :

- « Je spam des drones pour le % de win. »  
- « C’est juste une ressource de plus. »  
- « Les drones combattent. »

---

## 20. Synthèse opérationnelle

| Dimension | Décision de design |
|-----------|-------------------|
| Rôle | Instruments d’observation, carte, prélèvement, préparation, relais d’analyse |
| Combat | **Aucun** |
| Unité de gestion | **Vol** (1–3) + modules |
| Classes | Observateur · Cartographe · Préleveur · Soutien |
| Boucle | Recon → Expé → Données labo → meilleure décision |
| Risque | Déplacé vers les instruments ; jamais nul |
| Progression | Config, carte permanente, skill d’usage |
| Phase 2 | Coût de présence, camps, intel sur mondes partagés |
| Cœur émotionnel | Toujours les **créatures** ; drones = condition d’une science digne |

---

## 21. Engagement Lead Gameplay

> Si l’exploration est le souffle de GENESIS, les drones en sont les **poumons instrumentés**.  
> Ils préparent la galaxie, élargissent le possible, baissent le coût humain du savoir, et multiplient les décisions — **sans un seul coup de feu**.

**Test de toute feature drones future :**

> Est-ce que cela améliore l’observation, la carte, le prélèvement, l’analyse ou la préparation d’expédition — d’une façon qui formera un explorateur Phase 2 ?

Sinon → dehors.

---

*Fin de `04_Drones.md` — pilier drones, Lead Gameplay Designer.*
