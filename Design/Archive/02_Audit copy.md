# 02 — Audit exhaustif des mécaniques Phase 1

**Rôle :** Game Director · UX Designer · Senior Game Designer  
**Objet :** inventaire critique de **toutes** les mécaniques jouables de la Phase 1  
**Canon de référence :** `00_Vision_Refonte.md` · `01_Core_Gameplay.md` · charte patrimoine  
**Posture :** sévérité maximale — on juge ce que le joueur *fait*, pas ce que les docs *disent*

---

## 0. Cadre d’évaluation

Pour chaque mécanique, six questions :

| Code | Question |
|------|----------|
| **W** | **Pourquoi elle existe** (intention historique / fonction) |
| **L** | **Ce qu’elle apprend réellement** au joueur (comportement formé) |
| **P2** | **Prépare-t-elle la Phase 2** (galaxie, camps, bourse, réputation) ? |
| **IS** | **Renforce-t-elle le fantasy « Institut Scientifique »** ? |
| **PB** | **Renforce-t-elle le patrimoine biologique** ? |
| **CB** | **Ressemble-t-elle à une mécanique générique de city-builder** ? |

### Légende de classification

| Icône | Catégorie | Signification |
|-------|-----------|---------------|
| ❤️ | **Indispensable** | Noyau d’identité. Garder (éventuellement clarifier le langage). |
| 🔄 | **À transformer** | Idée utile, forme actuelle toxique ou city-builder. Refondre. |
| ❌ | **À supprimer** | Bruit, filler, ou contredit la vision. Couper. |
| 🌌 | **À déplacer en Phase 2** | Valide plus tard, pas en prologue d’Institut. |

### Échelle qualitative (P2 / IS / PB / CB)

`Oui fort` · `Oui faible` · `Non` · `Contre-productif`

---

## 1. Synthèse exécutive

```
CE QUE LE JOUEUR APPREND AUJOURD’HUI     CE QU’IL DEVRAIT APPRENDRE
─────────────────────────────────────    ──────────────────────────
Maxer bâtiments et caps                  Écrire un patrimoine digne d’export
Staffing multi-jobs                      Composer une équipe scientifique
Farmer biomasse / matériel               Rencontrer / comprendre le vivant
Checklist de sortie multi-seuils         MVE patrimonial + readiness départ
Min-maxer score de mission               Éprouver des branches avec risque
Gérer moral / santé / fatigue pop        Protéger le berceau et l’équipe
```

**Verdict global :**

> La Phase 1 est un **city-builder gêné** qui a greffé un excellent noyau génétique/patrimonial par-dessus, sans tuer le centre de gravité colonial.  
> Les docs et le tutoriel disent « patrimoine ». Le **HUD, les bâtiments, la sortie T8 et les actions dashboard** disent encore « colonie ».

**Score d’alignement vision (brut) :** ~4/10  
**Potentiel après purge + recentrage :** ~9/10

---

## 2. Inventaire par domaine

### 2.1 Session, accès, temps asynchrone

#### Auth / session empire

| Critère | Évaluation |
|---------|------------|
| **W** | Compte joueur, run persistante, état d’empire. |
| **L** | « Tu as une base qui te appartient. » |
| **P2** | Oui fort (identité persistante MMO). |
| **IS** | Oui faible (neutre). |
| **PB** | Non (infra). |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** (infra) |

#### Tick serveur / timers d’action

| Critère | Évaluation |
|---------|------------|
| **W** | Style OGame asynchrone : constructions, missions, analyses, mutations. |
| **L** | « Le temps passe hors session ; planifie et reviens. » — **aussi** : « attends un build queue ». |
| **P2** | Oui fort (sessions MMO, camps). |
| **IS** | Oui faible (rituels d’attente scientifique possibles). |
| **PB** | Oui faible (naissance, analyse, observation). |
| **CB** | Oui fort quand appliqué aux *bâtiments* ; non quand appliqué aux *rituels biologiques*. |
| **Classe** | 🔄 **À transformer** — garder le temps asynchrone sur **missions / analyse / naissance** ; le retirer comme cœur de progression **bâtiment**. |

#### Tutoriel guidé + tips IA de boucle

| Critère | Évaluation |
|---------|------------|
| **W** | Onboarding vers boucle patrimoine. |
| **L** | Message correct (« pas city-builder ») **contredit** par le premier CTA « commandement / harvest ». |
| **P2** | Oui faible. |
| **IS** | Oui faible (texte) / Contre-productif (parcours UI réel). |
| **PB** | Oui fort en intention. |
| **CB** | Non (le contenu). |
| **Classe** | 🔄 **À transformer** — aligner parcours UI sur Rencontrer→…→Inscrire, pas sur dashboard colonial. |

---

### 2.2 Monde natal & planète

#### Génération procédurale du monde (seed, types, rareté)

| Critère | Évaluation |
|---------|------------|
| **W** | Unicité de run ; berceau unique. |
| **L** | « Mon monde n’est pas le tien. » — bon muscle MMO. |
| **P2** | Oui fort (planètes sauvages partagées, identité d’origine). |
| **IS** | Oui fort (terrain d’étude). |
| **PB** | Oui fort (Natures nées d’un lieu). |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

#### Biomes / zones / élément majeur

| Critère | Évaluation |
|---------|------------|
| **W** | Diversité de terrain, fit Nature↔monde, loot typé. |
| **L** | « Le lieu change ce que le vivant peut être et faire. » |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non (proche exploration / scientific survey). |
| **Classe** | ❤️ **Indispensable** |

#### Re-roll planète (≤2)

| Critère | Évaluation |
|---------|------------|
| **W** | Filet anti-seed injouable / frustration. |
| **L** | « Shop de seed » si trop généreux ; « relocalisation d’urgence » si rare. |
| **P2** | Non (Phase 2 = mondes partagés, pas re-roll perso). |
| **IS** | Non. |
| **PB** | Contre-productif (dévalorise le berceau). |
| **CB** | Non (plus roguelike/meta). |
| **Classe** | 🔄 **À transformer** — 0–1 max, narratif strict, jamais shopping confort. |

#### % découverte / cartographie

| Critère | Évaluation |
|---------|------------|
| **W** | Progression d’exploration du monde natal ; critère de sortie. |
| **L** | « Explore plus pour débloquer. » — risque de **barre de completion** pure. |
| **P2** | Oui faible (cartographier un monde partagé). |
| **IS** | Oui fort. |
| **PB** | Oui faible (contextuel) ; Contre-productif s’il domine la victoire. |
| **CB** | Oui faible (fog of war / map %). |
| **Classe** | 🔄 **À transformer** — garder comme **savoir territorial** ; **retirer** comme pilier de victoire égal au patrimoine. |

---

### 2.3 Stabilité écologique & tension

#### ISMN (Indice de Stabilité Monde Natal)

| Critère | Évaluation |
|---------|------------|
| **W** | Co-évolution / prix des manipulations ; game over écologique. |
| **L** | « Créer a un coût sur le berceau. » — **meilleure tension du jeu**. |
| **P2** | Oui fort (éthique d’expédition, réputation écologique). |
| **IS** | Oui fort. |
| **PB** | Oui fort (patrimoine *responsable*). |
| **CB** | Non (unique). |
| **Classe** | ❤️ **Indispensable** — simplifier l’affichage / bandes, pas le concept. |

#### Contamination / pollution de manipulation

| Critère | Évaluation |
|---------|------------|
| **W** | Risque d’échec génétique → impact colonie/planète. |
| **L** | « Forger n’est pas gratuit. » |
| **P2** | Oui faible. |
| **IS** | Oui fort. |
| **PB** | Oui fort (intégrité du monde + des branches). |
| **CB** | Non. |
| **Classe** | 🔄 **À transformer** — fusionner dans **un seul système de tension** (ISMN + intégrité), pas une 4ᵉ jauge rouge isolée. |

#### Événements colonie (épidémie, désertion, crises…)

| Critère | Évaluation |
|---------|------------|
| **W** | Tension aléatoire, mitigation militaire/médicale. |
| **L** | « Gère ta base comme un sim de survie coloniale. » |
| **P2** | Non (P2 = camps d’expé, pas empire). |
| **IS** | Oui faible (crise de labo OK) / Contre-productif (révolte de colons). |
| **PB** | Non. |
| **CB** | Oui fort (events city-builder / survival). |
| **Classe** | 🔄 **À transformer** — un seul flux de tension **écosystème + risque d’expédition** ; supprimer le flavour « ville en crise ». |

---

### 2.4 Ressources

#### Biomasse

| Critère | Évaluation |
|---------|------------|
| **W** | Carburant d’analyse, mutations, soins bio, constructions. |
| **L** | Aujourd’hui : « farm la ressource pour tout. » Devrait : « matière organique pour l’atelier. » |
| **P2** | Oui faible (logistique de camp). |
| **IS** | Oui faible. |
| **PB** | Oui faible (moyen, pas fin). |
| **CB** | Oui fort **si harvest = core loop**. |
| **Classe** | 🔄 **À transformer** — garder comme **carburant de labo/terrain** ; **jamais** boucle principale. |

#### Matériel

| Critère | Évaluation |
|---------|------------|
| **W** | Coût de bâtiments, drones, logistique. |
| **L** | « Construis et stocke. » — cœur city-builder. |
| **P2** | Oui faible (logistique d’expédition). |
| **IS** | Non. |
| **PB** | Non. |
| **CB** | Oui fort. |
| **Classe** | 🔄 **À transformer** — réduire à **logistique d’expédition** ; supprimer le trophée « cap matériel ». |

#### Échantillons (prélèvements)

| Critère | Évaluation |
|---------|------------|
| **W** | Savoir brut / coût d’analyse et manipulations. |
| **L** | « Ramène du vivant mesurable. » |
| **P2** | Oui fort (bourse de génomes, données). |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

#### Population (effectif)

| Critère | Évaluation |
|---------|------------|
| **W** | Staff, missions, croissance, game over démographique. |
| **L** | « Grossis ta colonie. » (aujourd’hui) vs « gère une équipe d’institut » (cible). |
| **P2** | Oui faible (équipes de camp limitées, pas empires pop 800). |
| **IS** | Oui fort **si** personnel d’institut. |
| **PB** | Non (humains ≠ patrimoine bio, sauf éthique de préservation). |
| **CB** | Oui fort (caps, growth, housing). |
| **Classe** | 🔄 **À transformer** — **personnel d’institut** borné, pas empire démographique. |

#### Drones

| Critère | Évaluation |
|---------|------------|
| **W** | Support missions, harvest, « production ». |
| **L** | « Usine de drones » ou « outils d’expédition » selon framing. |
| **P2** | Oui fort (exploration multi-sites). |
| **IS** | Oui fort (outils de terrain). |
| **PB** | Non. |
| **CB** | Oui fort **si** action « produire des drones » au dashboard. |
| **Classe** | 🔄 **À transformer** — voir `04_Drones.md` : outils d’expédition, pas usine. |

#### Essences de biome

| Critère | Évaluation |
|---------|------------|
| **W** | Coût thématique des mutations élémentaires / fit. |
| **L** | « Le terrain a une matière unique. » |
| **P2** | Oui fort (ressources de monde partagé). |
| **IS** | Oui fort. |
| **PB** | Oui fort (lien Nature / monde). |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** (ou fusion légère dans prélèvements **typés** — pas empiler 3 monnaies bio). |

#### Caps (biomasse / matériel / samples / pop / créatures)

| Critère | Évaluation |
|---------|------------|
| **W** | Balancing, anti-hoarding, progression structurelle. |
| **L** | « Améliore le bâtiment pour stocker plus » = city-builder pur. |
| **P2** | Non. |
| **IS** | Non. |
| **PB** | Contre-productif (slots créature OK en soft cap ; caps trophées non). |
| **CB** | Oui fort. |
| **Classe** | 🔄 **À transformer** — **techniques invisibles** ; slots patrimoine **narratifs** (vivarium), pas fierté HUD. |

#### Crédits / monnaies Phase 2

| Critère | Évaluation |
|---------|------------|
| **W** | Bourse galactique (futur). |
| **L** | N/A en P1. |
| **P2** | Oui fort. |
| **IS** | — |
| **PB** | — |
| **CB** | — |
| **Classe** | 🌌 **À déplacer en Phase 2** (ne pas introduire en P1). |

---

### 2.5 Population humaine (détail)

#### Moral

| Critère | Évaluation |
|---------|------------|
| **W** | Efficacité, croissance, seuil de sortie / game over. |
| **L** | « Maintiens une jauge de happiness coloniale. » |
| **P2** | Non. |
| **IS** | Oui faible (esprit d’équipe). |
| **PB** | Non. |
| **CB** | Oui fort. |
| **Classe** | 🔄 **À transformer** — fondre dans **État de l’Institut / de l’équipe**. |

#### Santé population

| Critère | Évaluation |
|---------|------------|
| **W** | Malus global, épidémies, soins. |
| **L** | Soft survival city. |
| **P2** | Oui faible (risque d’expé). |
| **IS** | Oui faible. |
| **PB** | Non. |
| **CB** | Oui fort. |
| **Classe** | 🔄 **À transformer** — fusion avec moral/fatigue. |

#### Fatigue population

| Critère | Évaluation |
|---------|------------|
| **W** | Coût des missions / récupération. |
| **L** | « Ne spam pas les expés. » — utile. |
| **P2** | Oui fort (logistique de camp). |
| **IS** | Oui fort (équipe de terrain). |
| **PB** | Non. |
| **CB** | Oui faible. |
| **Classe** | 🔄 **À transformer** — garder le **coût d’équipe**, pas la 3ᵉ jauge coloniale. |

#### Spécialisations (scientifiques, soldats, ingénieurs, soigneurs, généralistes)

| Critère | Évaluation |
|---------|------------|
| **W** | Roster mission + staffing bâtiments. |
| **L** | Micro-gestion de jobs (Banished / RimWorld light). |
| **P2** | Oui faible (rôles d’expé). |
| **IS** | Oui fort pour **scientifiques / ingénieurs de terrain** ; Contre-productif pour **soldats / généralistes filler**. |
| **PB** | Non. |
| **CB** | Oui fort. |
| **Classe** | 🔄 **À transformer** — **2–3 rôles d’institut** max. |

#### Formation d’orientation des spés

| Critère | Évaluation |
|---------|------------|
| **W** | Diriger la croissance des jobs. |
| **L** | « Optimise le pipeline RH. » |
| **P2** | Non. |
| **IS** | Non. |
| **PB** | Non. |
| **CB** | Oui fort. |
| **Classe** | ❌ **À supprimer** (ou automatiser hors face joueur). |

#### Soldats (en tant que spé majeure P1)

| Critère | Évaluation |
|---------|------------|
| **W** | Survie mission / vibe défense. |
| **L** | Fantasy militaire coloniale. |
| **P2** | 🌌 plus tard (sécurité de camp / PvE hostile). |
| **IS** | Contre-productif en prologue pure science. |
| **PB** | Non. |
| **CB** | Oui (militaire base-building). |
| **Classe** | ❌ **À supprimer** en Phase 1 (ou 🌌 sécurité d’expé en P2). |

#### Généralistes

| Critère | Évaluation |
|---------|------------|
| **W** | Job filler polyvalent. |
| **L** | Bruit de roster. |
| **P2** | Non. |
| **IS** | Non. |
| **PB** | Non. |
| **CB** | Oui fort. |
| **Classe** | ❌ **À supprimer** (absorber en « soutien » invisible). |

#### Croissance démographique / housing implicite

| Critère | Évaluation |
|---------|------------|
| **W** | Pop cible 150–800, caps QG. |
| **L** | « Plus de colons = mieux. » |
| **P2** | Contre-productif (P2 ≠ empire pop). |
| **IS** | Contre-productif. |
| **PB** | Contre-productif. |
| **CB** | Oui fort. |
| **Classe** | ❌ **À supprimer** comme progression visible ; pop **stable / limitée**. |

#### Action « Soigner la population » (/care)

| Critère | Évaluation |
|---------|------------|
| **W** | Bouton de récup moral/santé. |
| **L** | Clicker pour up des jauges. |
| **P2** | Non. |
| **IS** | Non. |
| **PB** | Non. |
| **CB** | Oui fort. |
| **Classe** | ❌ **À supprimer** comme action dashboard ; récup **passive** ou liée aux retours de mission. |

#### Presets d’effectif harvest (Léger / Équilibré / Max)

| Critère | Évaluation |
|---------|------------|
| **W** | Allouer des corps à la collecte. |
| **L** | Micro-gestion de l’inutile. |
| **P2** | Non. |
| **IS** | Non. |
| **PB** | Non. |
| **CB** | Oui fort. |
| **Classe** | ❌ **À supprimer** pour harvest ; presets **expédition / labo** seulement. |

---

### 2.6 Bâtiments & construction

#### File de construction / upgrade (coût + timer + niveaux)

| Critère | Évaluation |
|---------|------------|
| **W** | Progression OGame / empire. |
| **L** | « La fierté = niveau de bâtiment. » |
| **P2** | Contre-productif. |
| **IS** | Contre-productif. |
| **PB** | Contre-productif. |
| **CB** | Oui fort (ADN du genre). |
| **Classe** | 🔄 **À transformer** — peu de structures ; upgrades = **actes scientifiques** ou seuils soft, pas grind 1→50. |

#### Centre de commandement

| Critère | Évaluation |
|---------|------------|
| **W** | HQ, caps pop/matériel, prep mission, moral. |
| **L** | « Tu es gouverneur de colonie. » |
| **P2** | Non. |
| **IS** | Contre-productif (« empire », « colons »). |
| **PB** | Non. |
| **CB** | Oui fort. |
| **Classe** | ❌ **À supprimer** comme identité — absorber en **Campus / Institut** narratif soft. |

#### Laboratoire génétique

| Critère | Évaluation |
|---------|------------|
| **W** | Analyse, mutations, croisements, unlocks. |
| **L** | « Ici on comprend et on forge le vivant. » |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Oui faible **uniquement** via max_level 50 / grind. |
| **Classe** | ❤️ **Indispensable** — renommer **Laboratoire central** ; **plafond bas** (tiers scientifiques, pas 50). |

#### Réserve biologique

| Critère | Évaluation |
|---------|------------|
| **W** | Slots créatures, caps bio, stockage. |
| **L** | Mix : « stocke des units » vs « vivarium du patrimoine ». |
| **P2** | Oui fort (registre portable). |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Oui faible (warehouse de créatures). |
| **Classe** | ❤️ **Indispensable** — **Vivarium / Archives** ; slots = contrainte de soin, pas entrepôt. |

#### Centre d’exploration

| Critère | Évaluation |
|---------|------------|
| **W** | Missions, loadout, multi-hangars, loot. |
| **L** | « Organise des expéditions. » — bon. Multi-pads = logistique empire. |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui faible (outil pour éprouver). |
| **CB** | Oui faible (fleet ops niv. 10). |
| **Classe** | ❤️ **Indispensable** — **Bureau des expéditions** ; réduire hangars parallèles. |

#### Musée scientifique / Musée des Fondateurs

| Critère | Évaluation |
|---------|------------|
| **W** | Mémoire des lignées, prestige, moral, célébrations. |
| **L** | « Ce que tu as créé a une histoire exposable. » |
| **P2** | Oui fort (réputation Maison). |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non (prestige culturel ≠ warehouse). |
| **Classe** | ❤️ **Indispensable** — purger bonus « pureté de croisement » obscurs ; maximiser **mémoire**. |

#### Spatioport

| Critère | Évaluation |
|---------|------------|
| **W** | Seuil de sortie Phase 1 / projection. |
| **L** | Aujourd’hui : un bâtiment de plus à builder. Cible : « prêt à emporter le patrimoine ». |
| **P2** | Oui fort (porte d’entrée). |
| **IS** | Oui faible (infrastructure). |
| **PB** | Oui faible (véhicule du patrimoine). |
| **CB** | Oui fort s’il se **level up** comme vanity. |
| **Classe** | 🔄 **À transformer** — **seuil de projection** unique, pas upgrade vanity multi-niveaux. |

#### Infirmerie coloniale

| Critère | Évaluation |
|---------|------------|
| **W** | Santé, soins, fatigue, natalité. |
| **L** | Mini-jeu médical colonial. |
| **P2** | Oui faible (soins d’expé). |
| **IS** | Oui faible. |
| **PB** | Non (sauf soins créatures si fusionnés). |
| **CB** | Oui fort. |
| **Classe** | 🔄 **À transformer** — fusion **Unité de préservation** (équipe + branches blessées), pas 3ᵉ bâtiment. |

#### Centre militaire

| Critère | Évaluation |
|---------|------------|
| **W** | Défense, crit resist, mitigation events. |
| **L** | Fantasy OGame / base militaire. |
| **P2** | 🌌 éventuellement sécurité de camp. |
| **IS** | Contre-productif. |
| **PB** | Non. |
| **CB** | Oui fort. |
| **Classe** | ❌ **À supprimer** en Phase 1 |

#### Entrepôt industriel

| Critère | Évaluation |
|---------|------------|
| **W** | Cap matériel, harvest, production passive. |
| **L** | « Stocke plus pour construire plus. » |
| **P2** | Non. |
| **IS** | Contre-productif. |
| **PB** | Non. |
| **CB** | Oui fort. |
| **Classe** | ❌ **À supprimer** |

#### Staffing min/optimal par bâtiment

| Critère | Évaluation |
|---------|------------|
| **W** | Efficacité structurelle selon jobs assignés. |
| **L** | Puzzle de jobs city-builder. |
| **P2** | Non. |
| **IS** | Oui faible. |
| **PB** | Non. |
| **CB** | Oui fort. |
| **Classe** | ❌ **À supprimer** ou automatiser — le joueur n’est pas DRH. |

#### Production passive (biomasse / matériel / tick)

| Critère | Évaluation |
|---------|------------|
| **W** | Idle income. |
| **L** | « Améliore pour AFK farm. » |
| **P2** | Contre-productif. |
| **IS** | Contre-productif. |
| **PB** | Contre-productif. |
| **CB** | Oui fort. |
| **Classe** | ❌ **À supprimer** (ou négligeable invisible) — le revenu doit venir d’**actes**. |

---

### 2.7 Actions colonie (dashboard)

#### Harvest biomasse

| Critère | Évaluation |
|---------|------------|
| **W** | Remplir le carburant bio. |
| **L** | Farm click → ressource (core city-builder). |
| **P2** | Non. |
| **IS** | Contre-productif. |
| **PB** | Contre-productif (détourne du vivant unique). |
| **CB** | Oui fort. |
| **Classe** | 🔄 **À transformer** — rare / secondaire / auto-faible ; **jamais** état NEED_COLLECT central. |

#### Harvest matériel

| Critère | Évaluation |
|---------|------------|
| **W** | Alimenter constructions. |
| **L** | Même poison que biomasse, pire (0 fantasy science). |
| **P2** | Non. |
| **IS** | Contre-productif. |
| **PB** | Non. |
| **CB** | Oui fort. |
| **Classe** | ❌ **À supprimer** comme action joueur (logistique absente ou mission-linked). |

#### Produire des drones

| Critère | Évaluation |
|---------|------------|
| **W** | Usine de support. |
| **L** | Craft d’outil en masse. |
| **P2** | Oui faible. |
| **IS** | Oui faible. |
| **PB** | Non. |
| **CB** | Oui fort. |
| **Classe** | 🔄 **À transformer** (voir drones). |

#### Build queue multi-bâtiments

| Critère | Évaluation |
|---------|------------|
| **W** | Progression empire. |
| **L** | Voir file de construction. |
| **P2** | Non. |
| **IS** | Contre-productif. |
| **PB** | Contre-productif. |
| **CB** | Oui fort. |
| **Classe** | ❌ **À supprimer** comme pilier — constructions **rares / narratives**. |

---

### 2.8 Exploration & missions

#### Préparation de mission (zone, équipe, prep score)

| Critère | Évaluation |
|---------|------------|
| **W** | Risque lisible avant départ. |
| **L** | Bon : « compose et assume. » Mauvais : tableur prep ×1,5. |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort (branches en compagnons). |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** — **1 bande de risque** lisible, pas simulateur Excel. |

#### Composition humains + drones + créatures

| Critère | Évaluation |
|---------|------------|
| **W** | Loadout d’expédition. |
| **L** | « Le vivant change le run. » — cœur d’Éprouver. |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

#### Résolution serveur (succès / partiel / échec / critique)

| Critère | Évaluation |
|---------|------------|
| **W** | Autorité serveur, loot, blessures, pertes. |
| **L** | « Le monde peut te blesser. » |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort (scars, morts, histoire). |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

#### Loot (espèces, échantillons, ressources, essences)

| Critère | Évaluation |
|---------|------------|
| **W** | Récompense d’exploration. |
| **L** | Rencontrer = ramener du vivant / du savoir. |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort (si loot bio > loot matériel). |
| **CB** | Oui faible (loot tables génériques). |
| **Classe** | ❤️ **Indispensable** — prioriser **vivant + savoir**. |

#### Narration de mission / journal

| Critère | Évaluation |
|---------|------------|
| **W** | Récit, timeline, mémoire. |
| **L** | « Ce n’était pas un roll invisible. » |
| **P2** | Oui fort (légendes de Maison). |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

#### Multi-missions concurrentes (hangars)

| Critère | Évaluation |
|---------|------------|
| **W** | Scaling mid/late colonie. |
| **L** | « Plus de pipelines d’ops. » |
| **P2** | Oui faible (multi-camps plus tard). |
| **IS** | Oui faible. |
| **PB** | Contre-productif (dilue l’attachement). |
| **CB** | Oui fort (fleet / multi-queue). |
| **Classe** | 🔄 **À transformer** — 1 expédition mémorable > 4 pipelines. |

#### Fit créature / biome / éléments en mission

| Critère | Évaluation |
|---------|------------|
| **W** | Adaptation, malus, tags. |
| **L** | « Nature ↔ monde. » Muscle P2 parfait. |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

#### Synergie de lignée en mission

| Critère | Évaluation |
|---------|------------|
| **W** | Bonus co-présence lignée. |
| **L** | « Emporte ta Maison ensemble. » |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

---

### 2.9 Labo, analyse, réserve

#### Analyse ADN progressive (timer, profondeur, knowledge)

| Critère | Évaluation |
|---------|------------|
| **W** | Comprendre avant de forger. |
| **L** | « Sans savoir, pas de patrimoine. » — **Comprendre**. |
| **P2** | Oui fort (valeur des génomes cartographiés). |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** — knowledge en **mots humains** (inconnu / scanné / cartographié / complet). |

#### Réserve filtrable (nombreux filtres)

| Critère | Évaluation |
|---------|------------|
| **W** | Gérer un stock croissant de spécimens. |
| **L** | Excel biologique si 15+ filtres. |
| **P2** | Oui faible (bibliothèque). |
| **IS** | Oui faible. |
| **PB** | Oui faible. |
| **CB** | Non (plutôt sim/data). |
| **Classe** | 🔄 **À transformer** — **bibliothèque scientifique** : Nature, Lignée, Rôle, Disponibilité, Apex — **max ~6 filtres**. |

#### Silhouette / phrase d’identité de branche

| Critère | Évaluation |
|---------|------------|
| **W** | Lisibilité sociale du patrimoine. |
| **L** | « Je peux raconter qui elle est. » |
| **P2** | Oui fort (MMO social). |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

#### Stats plates (force, etc.) dans scores

| Critère | Évaluation |
|---------|------------|
| **W** | Legacy unit RPG. |
| **L** | Min-max de unit. |
| **P2** | Contre-productif. |
| **IS** | Contre-productif. |
| **PB** | Contre-productif. |
| **CB** | Non (RPG, pas city). |
| **Classe** | 🔄 **À transformer** — **Rôle + Dons + fit** devant ; stats sous le capot si besoin. |

#### Modules / flags / capacités (sous le capot)

| Critère | Évaluation |
|---------|------------|
| **W** | Architecture génétique historique. |
| **L** | Double mental model vs Dons. |
| **P2** | Oui faible (si exposé proprement). |
| **IS** | Oui faible. |
| **PB** | Oui faible. |
| **CB** | Non. |
| **Classe** | 🔄 **À transformer** — face joueur = **Dons ≤3** ; modules = implémentation. |

#### Pureté / stability / anomaly (multi-thermomètres)

| Critère | Évaluation |
|---------|------------|
| **W** | Risque génétique historique. |
| **L** | Confusion de jauges. |
| **P2** | Oui faible. |
| **IS** | Oui faible. |
| **PB** | Oui faible. |
| **CB** | Non. |
| **Classe** | 🔄 **À transformer** — **une Intégrité**. |

#### Types naturelle / hybride / mutante (taxonomie admin)

| Critère | Évaluation |
|---------|------------|
| **W** | Classification technique. |
| **L** | Admin de base de données. |
| **P2** | Non. |
| **IS** | Non. |
| **PB** | Oui faible (birth/lignée mieux). |
| **CB** | Non. |
| **Classe** | 🔄 **À transformer** — **naissance + lignée**, pas filtres admin. |

#### Soins créature (/care-creature)

| Critère | Évaluation |
|---------|------------|
| **W** | Blessures, trauma, corruption bloquent actions. |
| **L** | « Le vivant souffre et se soigne. » — bon. Micro-gestion médicale — mauvais. |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | 🔄 **À transformer** — rituel de **préservation**, pas bouton spam. |

#### Culture scientifique (bonus JSON obscurs)

| Critère | Évaluation |
|---------|------------|
| **W** | Buffs de labo cachés. |
| **L** | Rien de lisible. |
| **P2** | Non. |
| **IS** | Non (illisible). |
| **PB** | Non. |
| **CB** | Oui faible (tech tree caché). |
| **Classe** | ❌ **À supprimer** ou un seul « esprit d’institut » visible. |

---

### 2.10 Génétique : transformer

#### Mutations stratégiques (Adaptation Extrême, Lien de Lignée, Fardeau, etc.)

| Critère | Évaluation |
|---------|------------|
| **W** | Forger des identités, pas +stats. |
| **L** | « Chaque mutation change la phrase d’identité. » |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

#### Mutations basiques +stats / protocoles filler

| Critère | Évaluation |
|---------|------------|
| **W** | Legacy grind génétique. |
| **L** | Min-max unit. |
| **P2** | Contre-productif. |
| **IS** | Contre-productif. |
| **PB** | Contre-productif. |
| **CB** | Non (RPG). |
| **Classe** | ❌ **À supprimer** ou reléguer hors face joueur. |

#### Infusion / purification élémentaire + essences

| Critère | Évaluation |
|---------|------------|
| **W** | Lier créature et matière de biome. |
| **L** | « Je sculpte la Nature. » |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

#### Croisement / Union (hybrides, harmonie, parents)

| Critère | Évaluation |
|---------|------------|
| **W** | Création de branche nouvelle — cœur émotionnel. |
| **L** | « Unir, c’est écrire le patrimoine. » |
| **P2** | Oui fort (lignées uniques socialement). |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

#### Complexité UI croisement (protocoles, vœux, invest, staff, opposition…)

| Critère | Évaluation |
|---------|------------|
| **W** | Profondeur / contrôle. |
| **L** | Console pour devs. |
| **P2** | Non (friction anti-social). |
| **IS** | Contre-productif (trop technique). |
| **PB** | Contre-productif (le rituel se noie). |
| **CB** | Non. |
| **Classe** | 🔄 **À transformer** — **rituel 3 choix max** visibles ; reste en avancé. |

#### Intégration écosystémique (zone + mode de contrôle)

| Critère | Évaluation |
|---------|------------|
| **W** | Remettre le vivant dans le monde ; impact ISMN. |
| **L** | « Éprouver / co-évoluer avec le berceau. » |
| **P2** | Oui fort (introduire une forme sur un monde partagé). |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** — simplifier modes si trop d’UI. |

#### Contrainte sexe M/F pour croisement

| Critère | Évaluation |
|---------|------------|
| **W** | Règle biologie « réaliste » actuelle. |
| **L** | Puzzle de roster, parfois anti-fun. |
| **P2** | Neutre. |
| **IS** | Oui faible. |
| **PB** | Oui faible. |
| **CB** | Non. |
| **Classe** | 🔄 **À transformer** — compatibilité **Nature / intégrité / récit**, pas admin de sexe seul. |

#### Lab level gates (mut ≥6, cross ≥10, etc.)

| Critère | Évaluation |
|---------|------------|
| **W** | Gating progression. |
| **L** | « Upgrade le lab avant de jouer au vrai jeu. » |
| **P2** | Contre-productif. |
| **IS** | Contre-productif. |
| **PB** | Contre-productif. |
| **CB** | Oui fort (tech tree building). |
| **Classe** | 🔄 **À transformer** — gates par **savoir / milestones patrimoine**, pas lab niv. 10. |

---

### 2.11 Éléments, rareté, Apex

#### Système d’éléments (primary / secondary / hybrid)

| Critère | Évaluation |
|---------|------------|
| **W** | Signature du vivant + fit monde. |
| **L** | « Identité élémentaire. » |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** — unifier langage avec **Nature**. |

#### Rareté des formes

| Critère | Évaluation |
|---------|------------|
| **W** | Valoriser découvertes exceptionnelles. |
| **L** | « Certains êtres sont uniques. » |
| **P2** | Oui fort (bourse, prestige). |
| **IS** | Oui faible. |
| **PB** | Oui fort. |
| **CB** | Non (loot rarity). |
| **Classe** | ❤️ **Indispensable** |

#### Apex

| Critère | Évaluation |
|---------|------------|
| **W** | Sommet rare, puissant, encadré. |
| **L** | « Trophy vivant » — attention au power creep. |
| **P2** | Oui fort (icônes de Maison). |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** — **rarité narrative**, pas +stats monstrueux. |

---

### 2.12 Lignées, musée, mémoire

#### Fondation de lignée (nom, symbole, fondateur)

| Critère | Évaluation |
|---------|------------|
| **W** | Inscrire une Maison. |
| **L** | « J’ai un héritage nommé. » — **Inscrire**. |
| **P2** | Oui fort (identité MMO). |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

#### Générations / arbre / extinction

| Critère | Évaluation |
|---------|------------|
| **W** | Continuité, deuil, fierté. |
| **L** | « La lignée peut mourir. » |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

#### Célébration de lignée / vedette

| Critère | Évaluation |
|---------|------------|
| **W** | Rituel de fierté, moral, polish. |
| **L** | « On honore le patrimoine. » — utile si pas spam buff. |
| **P2** | Oui faible (prestige social). |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | 🔄 **À transformer** — rituel mémorable, **pas** bouton de buff moral. |

#### Souvenirs de naissance / mémoriaux

| Critère | Évaluation |
|---------|------------|
| **W** | Mémoire exportable / narrative. |
| **L** | « L’histoire compte autant que le build. » |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

#### Prestige (score musée)

| Critère | Évaluation |
|---------|------------|
| **W** | Classements futurs, renommée. |
| **L** | Score abstrait si mal branché. |
| **P2** | Oui fort. |
| **IS** | Oui faible. |
| **PB** | Oui faible. |
| **CB** | Non. |
| **Classe** | 🌌 **À déplacer en Phase 2** comme **réputation sociale** ; en P1, **mémoire qualitative** suffit. |

---

### 2.13 Modèle Branche / patrimoine (couches récentes)

#### Nature · Rôle · Dons (≤3) · Intégrité · Lignée · Knowledge

| Critère | Évaluation |
|---------|------------|
| **W** | Unité fondamentale du patrimoine. |
| **L** | Langage unique du vivant. |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** — **seul** langage face joueur. |

#### HeritageRegistry / MVE / write gate

| Critère | Évaluation |
|---------|------------|
| **W** | Contrat d’export Phase 2. |
| **L** | « Ce qui part en galaxie est jugé. » |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

#### Analyse depths / forging / union services

| Critère | Évaluation |
|---------|------------|
| **W** | Implémentation de Comprendre / Transformer. |
| **L** | Aligné vision si UI suit. |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

#### Double langage legacy (pureté + intégrité, modules + dons…)

| Critère | Évaluation |
|---------|------------|
| **W** | Transition technique. |
| **L** | Confusion. |
| **P2** | Contre-productif. |
| **IS** | Contre-productif. |
| **PB** | Contre-productif. |
| **CB** | Non. |
| **Classe** | ❌ **À supprimer** en face joueur. |

---

### 2.14 Objectifs, sortie, fin de run

#### Widget LOOP_STATE (Explore / Collect / Analyze / …)

| Critère | Évaluation |
|---------|------------|
| **W** | Clarity de boucle. |
| **L** | Bonne intention ; **NEED_COLLECT** et **NEED_BUILD_LAB** recentrent sur colonie. |
| **P2** | Oui faible. |
| **IS** | Oui faible. |
| **PB** | Oui faible. |
| **CB** | Oui quand collect/build dominent. |
| **Classe** | 🔄 **À transformer** — états = **Rencontrer / Comprendre / Transformer / Éprouver / Inscrire**. |

#### Milestones (1ʳᵉ mission, 1ʳᵉ analyse, 1ʳᵉ lignée…)

| Critère | Évaluation |
|---------|------------|
| **W** | Onboarding. |
| **L** | Bons jalons patrimoniaux. |
| **P2** | Oui faible. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

#### Conditions de victoire multi-seuils (discovery, lab 8, pop 150, morale, ISMN, spaceport, heritage MVE…)

| Critère | Évaluation |
|---------|------------|
| **W** | Checklist empire + greffe patrimoine. |
| **L** | « Coche toutes les cases de colonie. » — le MVE est noyé. |
| **P2** | Contre-productif (enseigne la mauvaise fierté). |
| **IS** | Contre-productif. |
| **PB** | Oui faible (MVE présent mais dilué). |
| **CB** | Oui fort. |
| **Classe** | 🔄 **À transformer** — **MVE patrimoine + readiness départ** ; le reste en soft checks invisibles. |

#### Game Over démographique

| Critère | Évaluation |
|---------|------------|
| **W** | Punir l’effondrement pop. |
| **L** | « Protège tes colons. » |
| **P2** | Non. |
| **IS** | Oui faible. |
| **PB** | Non. |
| **CB** | Oui fort. |
| **Classe** | 🔄 **À transformer** — échec = **perte d’équipe critique / berceau brisé**, pas « <15 % pop de départ ». |

#### Game Over écologique (ISMN)

| Critère | Évaluation |
|---------|------------|
| **W** | Prix du berceau détruit. |
| **L** | « Tu as tué le monde. » — parfait. |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

#### Claim victory / ending / new run

| Critère | Évaluation |
|---------|------------|
| **W** | Fermeture de prologue + rejouabilité. |
| **L** | « La run a une fin. » |
| **P2** | Oui fort (export). |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** — l’écran de fin doit **montrer le patrimoine**, pas les stats de ville. |

#### Panneau Phase 1 à N checks

| Critère | Évaluation |
|---------|------------|
| **W** | Transparence des critères. |
| **L** | Achievement hunt. |
| **P2** | Non. |
| **IS** | Non. |
| **PB** | Oui faible. |
| **CB** | Oui fort. |
| **Classe** | 🔄 **À transformer** — **3 piliers** + détail repliable. |

---

### 2.15 UX / HUD / langages

#### HUD multi-ressources + macarons timers

| Critère | Évaluation |
|---------|------------|
| **W** | Ops dashboard. |
| **L** | « Tu gères une base. » |
| **P2** | Contre-productif. |
| **IS** | Contre-productif. |
| **PB** | Contre-productif. |
| **CB** | Oui fort. |
| **Classe** | 🔄 **À transformer** — HUD Institut : **équipe, expéditions, patrimoine, 1–2 stocks**. |

#### Page Commandement comme hub principal

| Critère | Évaluation |
|---------|------------|
| **W** | Entrée historique city-builder. |
| **L** | Fausse promesse d’identité. |
| **P2** | Contre-productif. |
| **IS** | Contre-productif. |
| **PB** | Contre-productif. |
| **CB** | Oui fort. |
| **Classe** | 🔄 **À transformer** — hub = **Laboratoire vivant / Institut**, pas QG colonial. |

#### Vocabulaire « colonie / empire / colons / matériel »

| Critère | Évaluation |
|---------|------------|
| **W** | Héritage lexical OGame/city. |
| **L** | Identité produit fausse. |
| **P2** | Contre-productif. |
| **IS** | Contre-productif. |
| **PB** | Contre-productif. |
| **CB** | Oui fort. |
| **Classe** | ❌ **À supprimer** — remplacer par Institut, personnel, atelier, patrimoine. |

#### Toasts / journal global

| Critère | Évaluation |
|---------|------------|
| **W** | Feedback, mémoire. |
| **L** | Récit de run. |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** |

---

### 2.16 Systèmes transverses / polish

#### Accélération par ressources (timers)

| Critère | Évaluation |
|---------|------------|
| **W** | Confort de session. |
| **L** | Spend pour skip wait. |
| **P2** | Neutre. |
| **IS** | Oui faible. |
| **PB** | Non. |
| **CB** | Oui faible. |
| **Classe** | 🔄 **À transformer** — OK sur **rituels**, pas sur grind build. |

#### Mode admin / bypass test

| Critère | Évaluation |
|---------|------------|
| **W** | Dev / QA. |
| **L** | N/A joueur. |
| **P2** | — |
| **IS** | — |
| **PB** | — |
| **CB** | — |
| **Classe** | ❤️ **Indispensable** (outil dev, hors fantasy) |

#### Phase 2 hooks / null ports

| Critère | Évaluation |
|---------|------------|
| **W** | Préparer export sans jouer P2. |
| **L** | Invisible joueur — bien. |
| **P2** | Oui fort. |
| **IS** | — |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | ❤️ **Indispensable** (architecture) |

#### Bourse de génomes / contrats joueurs / multi-sites

| Critère | Évaluation |
|---------|------------|
| **W** | Vision Phase 2. |
| **L** | N/A jouable P1. |
| **P2** | Oui fort. |
| **IS** | Oui fort. |
| **PB** | Oui fort. |
| **CB** | Non. |
| **Classe** | 🌌 **À déplacer en Phase 2** (ne pas simuler en P1). |

---

## 3. Tableaux de classification récapitulatifs

### 3.1 ❤️ Indispensable

| Mécanique | Note |
|-----------|------|
| Auth / session persistante | Infra identité |
| Génération monde + biomes / zones / éléments | Berceau unique |
| ISMN (stabilité écologique) | Tension centrale |
| Échantillons / prélèvements | Savoir brut |
| Essences (ou prélèvements typés) | Matière de terrain |
| Labo génétique (cœur, plafond bas) | Atelier du vivant |
| Réserve / Vivarium | Contenant patrimoine |
| Bureau des expéditions | Rencontrer / Éprouver |
| Musée des Fondateurs (mémoire) | Inscrire |
| Préparation + résolution missions | Éprouver |
| Composition créatures + fit + synergie lignée | Patrimoine en action |
| Loot vivant / narration / journal | Récit |
| Analyse progressive + knowledge lisible | Comprendre |
| Silhouette / phrase d’identité | Lisibilité sociale |
| Mutations stratégiques → Dons | Transformer |
| Union / croisement mémorable | Transformer |
| Intégration écosystémique | Co-évolution |
| Nature · Rôle · Dons · Intégrité · Lignée · Knowledge | Langage unique |
| Fondation lignée + générations + deuil | Patrimoine nommé |
| Souvenirs / mémoriaux | Mémoire |
| MVE / HeritageRegistry / write gate | Pont P2 |
| Game Over écologique | Conséquence morale |
| Ending + new run centrés patrimoine | Fermeture digne |
| Milestones patrimoniaux | Onboarding juste |
| Apex / rareté (narratifs) | Exception vivante |
| Timers sur rituels bio/mission (pas build empire) | Asynchrone juste |
| Hooks d’export Phase 2 | Architecture |

### 3.2 🔄 À transformer

| Mécanique | Direction de transformation |
|-----------|------------------------------|
| Timers / tick | Rituels & missions, pas build queue empire |
| Tutoriel + IA | Parcours = 5 verbes, pas dashboard harvest |
| Re-roll planète | 0–1 narratif |
| % découverte | Savoir territorial, pas pilier de victoire |
| Contamination + events colonie | **Un** système de tension éco/expé |
| Biomasse | Carburant atelier, pas core farm |
| Matériel | Logistique d’expédition invisible |
| Population | Personnel d’institut borné |
| Drones | Outils d’expé (pas usine) |
| Caps HUD | Techniques / invisibles |
| Moral + santé + fatigue | **État d’équipe / Institut** |
| Spés multi-jobs | 2–3 rôles max |
| File de construction / multi-niveaux | Peu de structures ; upgrades scientifiques |
| Spatioport | Seuil unique de projection |
| Infirmerie | Unité de préservation |
| Harvest biomasse | Rare / secondaire |
| Multi-missions hangars | 1 expé mémorable |
| Filtres Réserve | Max ~6, bibliothèque scientifique |
| Stats / modules face joueur | Rôle + Dons |
| Pureté/stability | Une Intégrité |
| Types admin | Birth + lignée |
| Soins créature | Rituel préservation |
| UI croisement lourde | 3 choix visibles |
| Sexe comme seule compat | Compat Nature/intégrité/récit |
| Lab level gates | Gates savoir / patrimoine |
| Célébration lignée | Rituel sans spam buff |
| LOOP_STATE | 5 verbes patrimoine |
| Victoire multi-seuils | MVE + readiness |
| Game Over démo | Perte d’équipe / berceau |
| HUD + hub Commandement | HUD / hub Institut |
| Accélération timers | Sur rituels seulement |

### 3.3 ❌ À supprimer (Phase 1)

| Mécanique | Pourquoi |
|-----------|----------|
| Centre de commandement (identité HQ) | Fantasy empire |
| Entrepôt industriel | Warehouse city-builder |
| Centre militaire | Fantasy guerre coloniale |
| Soldats comme spé majeure | Bruit + mauvaise identité |
| Généralistes | Filler jobs |
| Formation multi-orientation | Pipeline RH |
| Staffing min/optimal joueur | DRH de colonie |
| Production passive mat/bio comme fierté | Idle city |
| Harvest matériel action | Farm pure |
| Croissance pop comme progression / housing | Empire démographique |
| Action Care population dashboard | Clicker de jauges |
| Presets harvest 8 rôles | Micro-gestion inutile |
| Mutations +stats basiques face joueur | Min-max unit |
| Culture scientifique obscure | Bruit |
| Double langage pureté/intégrité, modules/dons UI | Confusion |
| Vocabulaire colonie/empire/colons | Identité fausse |
| Build queue comme pilier de fun | OGame skeleton |

### 3.4 🌌 À déplacer en Phase 2

| Mécanique | Raison |
|-----------|--------|
| Crédits / économies de marché | Bourse galactique |
| Prestige classements | Réputation sociale multi |
| Multi-sites / multi-camps | Galaxie vivante |
| Contrats joueurs / licences génome | Social MMO |
| Sécurité militaire de camp (si besoin) | Menaces partagées |
| Soldats / force armée | PvE hostile galactique |
| Colosses impossibles seul | Contenu multi |
| Fleet ops / 4 hangars colonie | Scale empire ≠ prologue |

---

## 4. Redondances (même question, N systèmes)

| Question joueur | Systèmes concurrents aujourd’hui | Une seule réponse demain |
|-----------------|----------------------------------|---------------------------|
| Où est-elle à l’aise ? | Éléments, origin, tags modules, Adaptation Extrême, fit score | **Nature ↔ biome** |
| Est-elle « forte » ? | Stats, modules, flags, rareté, Apex, prep score | **Rôle + Dons + fit** |
| Est-elle saine ? | Pureté, stability, anomaly, corruption | **Intégrité** |
| Que fait-elle de spécial ? | Modules, flags, capacités, mutations | **Dons (≤3)** |
| Comment je progresse ? | Niveaux bâtiments, découverte %, pop, lab, musée, checks T8, MVE | **Patrimoine + readiness départ** |
| Est-ce que je gère bien ma base ? | Caps, staffing, formation, entrepôt, QG | **Ne plus poser la question** |
| Qu’est-ce qui est en danger ? | ISMN, contamination, moral, santé, events | **Berceau + équipe + branches aimées** |

---

## 5. Ce que chaque mécanique **apprend** vraiment (synthèse comportementale)

| Comportement formé aujourd’hui | Domaine responsable | Désiré demain |
|--------------------------------|---------------------|---------------|
| Maxer des bâtiments | Construction, caps, sortie lab/pop | Fonder des lignées digne d’export |
| Farmer des stocks | Harvest, passifs, entrepôt | Chercher du vivant inconnu |
| Staffing RH | Formation, spés, min staff | Composer 1 expédition juste |
| Cocher des checks | T8 multi-seuils, panneau P1 | Raconter une Maison |
| Min-max score prep | Loadout tableur | Parier une branche aimée |
| Gérer 4 alarmes rouges | Moral/santé/ISMN/contamination | Une tension claire berceau/ambition |
| Lire un Excel de créatures | Filtres + dual model | Lire une phrase d’identité |

> **Le jeu enseigne ce qu’il récompense.**  
> Aujourd’hui il récompense encore la **ville**. Il doit récompenser le **patrimoine**.

---

## 6. Critique complète de la philosophie actuelle

### 6.1 La promesse vs le contrat de jeu

GENESIS **promet** :

> créer, comprendre et préserver le vivant pour emporter un patrimoine unique en galaxie.

GENESIS **contracte** encore, en pratiques UI et systèmes :

> développer une colonie asynchrone (bâtiments, ressources, population, timers) **avec** un sous-jeu génétique riche.

Ce n’est pas une nuance de wording. C’est un **conflit d’âme**.

Le joueur croit le **premier écran** et le **premier bouton**, pas le GDD.  
Or le premier réflexe du produit reste le **Commandement** : stocks, build, harvest, care.  
Le labo, l’union, la lignée — excellents — arrivent comme **contenu secondaire d’une base**.

### 6.2 La greffe patrimoine n’a pas remplacé le squelette

Les apports récents (Branche, MVE, musée, mutations stratégiques, tutoriel « pas city-builder ») sont **justes**.

Ils ont été **greffés** :

- sortie T8 = checklist colonie **+** MVE (le « + » trahit la greffe) ;
- loop_objectives contient encore `NEED_COLLECT` et `NEED_BUILD_LAB` ;
- buildings.json parle d’« empire », « colons », « quartier général de colonie mature », lab max **50** ;
- le HUD est un dashboard d’ops.

Résultat : le joueur peut **gagner en mentalité city-builder** en traitant le patrimoine comme une **quête annexe**.  
C’est l’échec le plus grave possible pour un jeu dont la Phase 2 jugera **uniquement** le patrimoine.

### 6.3 Trop de systèmes répondent à la même peur

La peur légitime : « le joueur s’ennuie / perd / casse la planète ».

Réponses empilées :

- moral, santé, fatigue  
- ISMN, contamination  
- events colonie  
- military mitigation  
- care buttons  
- caps partout  

Chaque système est **localement défendable**.  
Ensemble, ils **diluent la tension centrale** :

> créer du vivant sans détruire le berceau, ni vider le sens de ce qu’on a créé.

Une tension claire vaut mieux que quatre jauges rouges.

### 6.4 La Phase 1 enseigne le mauvais muscle pour la Phase 2

La galaxie demandera :

- lire un monde,  
- juger un génome,  
- fonder une réputation de Maison,  
- composer des expéditions,  
- risquer ce qu’on aime,  
- négocier du vivant unique.

Elle **ne** demandera **pas** :

- maxer un entrepôt,  
- former des soldats,  
- atteindre pop 150,  
- level 8 un labo pour débloquer le fun.

Or la sortie actuelle **valide explicitement** plusieurs de ces mauvais muscles (pop, lab level, spaceport level, discovery %) **au même titre** que le MVE.

On forme un **bon gouverneur de colonie** et un **génétique occasionnel**.  
On a besoin d’un **excellent explorateur scientifique**.

### 6.5 La complexité technique se fait passer pour de la profondeur

Filtres réserve, protocoles de croisement, multi-thermomètres pureté/stability, modules+flags+stats+dons :

- impression de profondeur pour le designer,  
- **friction cognitive** pour le joueur,  
- **illisibilité sociale** pour le futur MMO (on ne trade pas un tableur).

La profondeur de GENESIS doit être **biographique** (phrase, lignée, scars, Nature),  
pas **administrative**.

### 6.6 Le fantasy Institut est annoncé, pas habité

Un Institut scientifique se reconnaît à :

- des **rituels** (analyse, union, fondation),  
- une **éthique** (ISMN, intégrité),  
- une **mémoire** (musée, journal),  
- une **équipe** limitée et précieuse,  
- un **seuil de départ** vers l’inconnu.

Il ne se reconnaît **pas** à :

- un centre militaire,  
- un entrepôt industriel,  
- une usine de drones au dashboard,  
- un HQ « empire ».

Tant que ces objets existent **au même rang** que le labo, le fantasy reste un **skin**.

### 6.7 Ce qui sauve le projet

Le noyau d’or est réel et rare :

1. **Rencontrer** (missions sur biomes uniques)  
2. **Comprendre** (analyse progressive)  
3. **Transformer** (mutations-à-Dons + union)  
4. **Éprouver** (branches en mission, scars)  
5. **Inscrire** (lignée, musée, MVE)  
6. **Tension berceau** (ISMN)

Avec ce noyau seul, GENESIS est déjà un **prologue d’Institut** potentiellement excellent.  
Le reste est **squelette de 2016** qui empêche le produit d’être ce qu’il dit être.

### 6.8 Verdict philosophique final

| Axe | Note /10 | Commentaire |
|-----|----------|-------------|
| Alignement vision patrimoine | **4** | Greffe visible, pas centre de gravité |
| Cohérence fantasy Institut | **3** | Lexique + bâtiments contredisent |
| Préparation Phase 2 MMO | **5** | Concepts bons ; structure encore colonie |
| Lisibilité session 20 min | **3** | Trop de systèmes, mauvais hub |
| Densité de décisions mémorables | **4** | Noyées dans la gestion |
| Qualité du noyau d’or isolé | **9** | À libérer, pas à « améliorer autour » |
| **Potentiel après purge** | **9** | Remplacement d’identité, pas polish |

**Conclusion de Game Director :**

> La Phase 1 n’a pas besoin d’être **améliorée**.  
> Elle a besoin d’être **remplacée dans son centre de gravité**.  
>  
> **Ce qu’on garde :** le vivant, le risque, la lignée, le berceau, le départ.  
> **Ce qu’on tue :** la colonie comme finalité, le build queue comme progression, le HUD d’empire.  
>  
> Tant que le joueur peut se sentir **gouverneur** avant de se sentir **fondateur d’un patrimoine**,  
> GENESIS ment sur ce qu’il est — et prépare mal la galaxie.

---

## 7. Note aux documents suivants

| Document | Ce qu’il doit faire à partir de cet audit |
|----------|-------------------------------------------|
| `03_Phase1.md` | Redéfinir le prologue **uniquement** avec ❤️ + 🔄 transformés |
| `04_Drones.md` | Refonte outils d’expédition (pas usine) |
| `05_Batiments.md` | Campus d’Institut minimal ; purger ❌ |
| `06_Ressources.md` | Stocks support ; caps invisibles |
| `07_Creatures.md` | Langage Branche exclusif face joueur |
| `08_UI_UX.md` | Hub Institut, HUD patrimoine, rituels 3 choix |
| `09_GENESIS_AI.md` | Conseiller patrimoine, jamais « max le QG » |
| `10_Transition_Phase2.md` | MVE comme seule porte digne |

---

## 8. Engagement de design

**Test unique pour toute mécanique future Phase 1 :**

> Est-ce que cela rend le joueur meilleur **explorateur scientifique**,  
> ou enrichit son **patrimoine biologique** d’une façon que la galaxie pourra juger ?

Si non → **ne pas ship**.

---

*Fin de `02_Audit.md` — audit exhaustif des mécaniques Phase 1.*  
*Statut : livrable Game Director — autorité sur le triage ❤️ / 🔄 / ❌ / 🌌.*
