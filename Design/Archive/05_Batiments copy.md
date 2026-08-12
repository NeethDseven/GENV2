# 05 — Bâtiments : architecture Phase 1

**Rôle :** Architecte Gameplay  
**Règle d’or :**

> **Un bâtiment n’existe que s’il débloque une nouvelle capacité scientifique ou d’exploration.**

Sinon → **fusionner**, **transformer**, ou **supprimer**.  
Aucune obligation de compatibilité avec le code actuel.

**Documents frères :** `02_Audit.md` · `03_Phase1.md` · `04_Drones.md`

---

## 0. Cadre d’architecture

### 0.1 Ce qu’est un bâtiment dans GENESIS

Un bâtiment n’est **pas** :

- un trophée de progression,  
- un générateur de caps,  
- un nœud de city-builder,  
- une file d’attente OGame.

Un bâtiment **est** :

- un **instrument du campus** qui ouvre ou approfondit une **capacité jouable** ;  
- un **palier de science / d’exploration** lisible ;  
- un lieu fictionnel de l’**Institut**, pas d’un empire.

### 0.2 Test d’existence (obligatoire)

Pour chaque structure, répondre **oui** à au moins une :

| Code | Question |
|------|----------|
| **S** | Débloque-t-elle une **capacité scientifique** nouvelle (analyser, forger, unir, préserver le savoir, inscrire) ? |
| **E** | Débloque-t-elle une **capacité d’exploration** nouvelle (partir, cartographier, prélever, composer, projeter) ? |

Si **non** et **non** → hors Phase 1.

### 0.3 Inventaire actuel (code / config)

Neuf bâtiments sont définis aujourd’hui :

1. Centre de commandement  
2. Laboratoire génétique  
3. Réserve biologique  
4. Centre d’exploration  
5. Musée scientifique  
6. Centre militaire  
7. Infirmerie coloniale  
8. Entrepôt industriel  
9. Spatioport  

Ci-dessous : audit un par un, puis **liste officielle** reconstruite.

---

## 1. Audit des bâtiments actuels

Légende utilité réelle :

| Note | Sens |
|------|------|
| **Utile (cœur)** | Porte une capacité S ou E indispensable |
| **Utile (bruit)** | Idée partiellement utile, noyée dans du city-builder |
| **Inutile en P1** | N’apporte pas de capacité S/E digne |
| **Nocif** | Enseigne la mauvaise identité (empire / guerre / warehouse) |

---

### 1.1 Centre de commandement

| | |
|--|--|
| **Rôle actuel** | HQ administratif : caps population & matériel, bonus prep mission, moral passif ; unlock “missions basiques” au niv. 1 ; max 10 ; staffing généralistes/ingénieurs. |
| **Pourquoi il existe** | Squelette OGame / city-builder : tout empire a un QG ; coordination pop + ressources + ops. |
| **Utilité réelle** | **Nocif** comme identité. Le seul morceau utile (débloquer les missions / coordination soft) n’a **pas** besoin d’un bâtiment “empire”. Caps pop/matériel et moral passif **échouent** le test S/E. |
| **Verdict** | **Supprimer** comme bâtiment de fierté. **Absorber** la narration d’arrivée dans le **Campus de l’Institut** (état de jeu, pas structure à maxer). Les missions basiques relèvent du **Bureau des expéditions**. |

**Capacité S/E légitime extraite :** aucune propre — redistribuer prep légère → Bureau.

---

### 1.2 Laboratoire génétique

| | |
|--|--|
| **Rôle actuel** | Analyse ADN, profondeur, vitesse, bonus mutations ; unlocks mutations / croisement / fusions futures ; **max_level 50**. |
| **Pourquoi il existe** | Cœur scientifique annoncé de GENESIS ; lieu du Comprendre / Transformer. |
| **Utilité réelle** | **Utile (cœur)** — mais **déformé** par le grind de niveaux et des unlocks purement “lab level gate”. |
| **Verdict** | **Transformer** en **Laboratoire central**. Garder les capacités : analyser, forger des Dons, unir. Remplacer max 50 par **paliers scientifiques rares** (savoir / actes, pas béton). |

**Capacités S/E :**

| Palier (esprit) | Capacité débloquée |
|-----------------|-------------------|
| Labo opérationnel | Analyse (knowledge) |
| Atelier de forge | Mutations stratégiques → Dons |
| Chambre d’union | Croisement / création de branche |
| (optionnel mature) | Protocoles avancés **si** justifiés par le savoir, pas le niveau 30 |

---

### 1.3 Réserve biologique

| | |
|--|--|
| **Rôle actuel** | Slots créatures, caps biomasse/échantillons, production passive biomasse ; stockage de spécimens. |
| **Pourquoi il existe** | Contenir le vivant découvert ; “warehouse bio” + fuel économique. |
| **Utilité réelle** | **Utile (bruit)**. Le **vivarium** (porter / préserver des branches) est indispensable. Les caps biomasse et la prod passive sont du city-builder. |
| **Verdict** | **Transformer** en **Vivarium / Archives vivantes**. Garder : capacité d’accueil du patrimoine vivant + préservation. **Retirer** prod passive et fierté de caps. Fusionner les soins créature ici (voir infirmerie). |

**Capacités S/E :**

| Palier | Capacité |
|--------|----------|
| Vivarium actif | Accueillir et conserver des branches |
| Extension d’archives | Porter plus de patrimoine **vivant** (contrainte de soin, pas warehouse) |
| Unité de préservation (intégrée) | Soigner intégrité / trauma des branches (et soutien d’équipe léger) |

---

### 1.4 Centre d’exploration

| | |
|--|--|
| **Rôle actuel** | Missions, loadout, loot, durée, multi-hangars (jusqu’à 4 missions parallèles), renseignement de zone. |
| **Pourquoi il existe** | Organiser l’exploration locale ; scaling mid/late via hangars. |
| **Utilité réelle** | **Utile (cœur)** pour l’exploration. **Bruit** sur fleet ops / multi-pads coloniaux (dilue l’attachement, vibe empire). |
| **Verdict** | **Transformer** en **Bureau des expéditions**. Cœur : composer, partir, lire le risque, intégrer les **drones** (vols, recon, reconfig). Réduire fortement le multi-mission. |

**Capacités S/E :**

| Palier | Capacité |
|--------|----------|
| Bureau ouvert | Expéditions sur le berceau |
| Parc de drones | Vols, recon, reconfiguration modules |
| Protocoles de terrain | Meilleure préparation / intel (pas +% plat opaque) |
| (rare) Second front | Au plus **une** expédition parallèle mid/late — pas 4 |

---

### 1.5 Musée scientifique

| | |
|--|--|
| **Rôle actuel** | Prestige, moral passif, cap échantillons ; Galerie des Fondateurs ; bonus pureté croisements ; célébrations moral/ISMN ; visites guidées. |
| **Pourquoi il existe** | Mémoire des lignées, fierté, polish narratif ; soft prestige pour classements futurs. |
| **Utilité réelle** | **Utile (cœur)** pour **Inscrire** (mémoire, lignées exposées). **Bruit** : moral passif, pureté de croisement, cap samples, prestige score. |
| **Verdict** | **Transformer** en **Galerie des Fondateurs**. Capacité = **inscrire / exposer / se souvenir**. Purger les buffs de colonie. Prestige chiffré → Phase 2 sociale. |

**Capacités S/E :**

| Palier | Capacité |
|--------|----------|
| Galerie ouverte | Fonder / exposer des lignées, vitrine mémoire |
| Archives mémorielles | Souvenirs, mémoriaux, deuils consultables |
| Rituel d’inscription | Célébration **narrative** (pas spam buff) |

La Galerie est **scientifique** au sens Institut : sans mémoire cataloguée, pas de patrimoine exportable digne (MVE).

---

### 1.6 Centre militaire

| | |
|--|--|
| **Rôle actuel** | Résistance critique, mitigation d’événements, défense locale ; patrouilles ; vibe force coloniale. |
| **Pourquoi il existe** | Héritage base-building / OGame ; répondre aux events hostiles par la force. |
| **Utilité réelle** | **Nocif** en Phase 1. Aucune capacité **scientifique**. “Exploration” via défense armée contredit le fantasy Institut et le non-combat drones. |
| **Verdict** | **Supprimer** en Phase 1. Toute mitigation utile = **Soutien d’expédition / drones / stabilité berceau**, pas une caserne. Sécurité de camp armée éventuelle → **Phase 2** si un jour nécessaire. |

---

### 1.7 Infirmerie coloniale

| | |
|--|--|
| **Rôle actuel** | Santé pop, soins, fatigue, natalité, action “soigner la population”, quarantaine future. |
| **Pourquoi il existe** | Soft survival colonial ; récup post-mission ; croissance démographique. |
| **Utilité réelle** | **Utile (bruit)**. Soigner branches / équipe après épreuve = légitime. Natalité, care dashboard, jauges pop = city-builder. |
| **Verdict** | **Fusionner** dans le **Vivarium** (Unité de préservation). Pas de bâtiment séparé. |

**Capacité S/E extraite :** préserver l’intégrité du vivant (et de l’équipe de terrain) pour **repartir explorer / analyser**.

---

### 1.8 Entrepôt industriel

| | |
|--|--|
| **Rôle actuel** | Cap matériel, harvest matériel, logistique chantier, production passive matériel. |
| **Pourquoi il existe** | Alimenter la construction et les caps — cœur city-builder. |
| **Utilité réelle** | **Nocif / inutile**. Aucune capacité scientifique. “Exploration” uniquement si on confond logistique et expédition. |
| **Verdict** | **Supprimer**. Logistique légère = couche invisible ou coût soft au Bureau / Baie, jamais un bâtiment de fierté. |

---

### 1.9 Spatioport

| | |
|--|--|
| **Rôle actuel** | Condition de sortie Phase 1 ; emplacements vaisseaux ; vitesse de lancement ; “préparation Phase 2” ; max 5. |
| **Pourquoi il existe** | Porte narrative et mécanique vers la galaxie. |
| **Utilité réelle** | **Utile (cœur)** comme **seuil d’exploration hors-berceau**. **Bruit** si multi-niveaux vanity / slots vaisseaux sans gameplay. |
| **Verdict** | **Transformer** en **Baie de projection** : **un** seuil de capacité (“nous pouvons partir”), pas un hangar à level up. S’ouvre quand le **MVE patrimoine** est digne — la Baie **exprime** le départ, elle ne le remplace pas. |

**Capacité S/E :**

| État | Capacité |
|------|----------|
| Baie inactive | Pas de projection hors monde natal |
| Baie opérationnelle | **Export / départ** — première capacité d’exploration **galactique** |

---

## 2. Synthèse des verdicts

| Bâtiment actuel | Verdict | Destination |
|-----------------|---------|-------------|
| Centre de commandement | **Supprimer** | Campus narratif d’arrivée (non-bâtiment) |
| Laboratoire génétique | **Transformer** | Laboratoire central |
| Réserve biologique | **Transformer** | Vivarium / Archives vivantes |
| Centre d’exploration | **Transformer** | Bureau des expéditions (+ drones) |
| Musée scientifique | **Transformer** | Galerie des Fondateurs |
| Centre militaire | **Supprimer** | — (éventuel P2 hors scope) |
| Infirmerie coloniale | **Fusionner** | → Vivarium (préservation) |
| Entrepôt industriel | **Supprimer** | Logistique invisible |
| Spatioport | **Transformer** | Baie de projection (seuil unique) |

---

## 3. Règles de redéfinition

### 3.1 Ce qu’un bâtiment a le droit d’être

| Autorisé | Interdit |
|----------|----------|
| Unlock de capacité nommée | +cap matériel / pop comme fierté |
| Palier scientifique lisible (“nous savons unir”) | Max level 20–50 grind |
| Amélioration d’une décision d’expé / labo | Production passive AFK |
| Lieu de rituel (analyse, union, fondation, départ) | Staffing RH min/optimal joueur |
| Contrainte de patrimoine (slots vivarium) | Warehouse / caserne / QG empire |

### 3.2 Progression structurelle

- **Peu de structures** (liste officielle ci-dessous).  
- **Peu de paliers** par structure (ordre de grandeur : 2–4 états, pas 50 niveaux).  
- Les paliers s’ouvrent par **actes / savoir / besoins d’expédition**, pas par “j’ai farm le timer”.  
- Améliorer = **nouvelle phrase de capacité**, pas “+3 %”.

### 3.3 Campus ≠ ville

À l’arrivée, le joueur possède un **Campus d’Institut** (fiction) :

- abrite l’équipe,  
- n’est **pas** un bâtiment à upgrader,  
- n’apparaît **pas** dans la liste de progression.

Les bâtiments listés sont les **instruments** du campus.

---

## 4. Nouvelle liste officielle — Phase 1

**Cinq bâtiments. Zéro exception.**

```
┌─────────────────────────────────────────────────────────────┐
│                    CAMPUS DE L’INSTITUT                     │
│                  (contexte, non-bâtiment)                   │
│                                                             │
│   ┌──────────────┐  ┌──────────────┐  ┌──────────────┐    │
│   │   BUREAU DES │  │ LABORATOIRE  │  │   VIVARIUM   │    │
│   │ EXPÉDITIONS  │  │   CENTRAL    │  │  / ARCHIVES  │    │
│   └──────────────┘  └──────────────┘  └──────────────┘    │
│                                                             │
│   ┌──────────────┐  ┌──────────────┐                      │
│   │   GALERIE    │  │    BAIE DE   │                      │
│   │DES FONDATEURS│  │  PROJECTION  │                      │
│   └──────────────┘  └──────────────┘                      │
└─────────────────────────────────────────────────────────────┘
```

---

### B1 — Bureau des expéditions

| | |
|--|--|
| **Nom fiction** | Bureau des expéditions (ou Bureau des terrains) |
| **Capacité fondamentale** | **Explorer** le berceau avec méthode |
| **Test S/E** | **E** fort · **S** faible (intel terrain) |

**Débloque / approfondit :**

1. Lancer des **expéditions**  
2. **Composer** (personnel, branches, **Vols de drones**)  
3. **Reconnaissance** et protocoles de vol (lien `04_Drones.md`)  
4. **Cartographie** opérationnelle (layers de carte issus des retours)  
5. Lecture de **risque nommé** à la préparation  
6. (Palier rare) second front d’expédition — **au plus un**

**N’est pas :** multi-fleet colonial, usine à loot, hangar ×4.

**Paliers d’esprit (ex.) :**

| Palier | Capacité nouvelle |
|--------|-------------------|
| Ouvert | Premières expéditions locales |
| Parc instrumenté | Gestion des Vols / recon / reconfig drones |
| Protocoles avancés | Meilleure intel de préparation ; objectifs de zone |
| Double engagement | 2ᵉ expédition possible (coût fort, mid/late) |

---

### B2 — Laboratoire central

| | |
|--|--|
| **Nom fiction** | Laboratoire central |
| **Capacité fondamentale** | **Comprendre** et **Transformer** le vivant |
| **Test S/E** | **S** fort |

**Débloque / approfondit :**

1. **Analyse** progressive (knowledge)  
2. **Forge** de Dons (mutations stratégiques)  
3. **Union** de branches (création)  
4. Exploitation des **données de terrain** (relais drones)  
5. Protocoles liés aux **essences** / Nature  

**N’est pas :** lab level 50, gate arbitraire “cross à 10”, usine à +stats.

**Paliers d’esprit (ex.) :**

| Palier | Capacité nouvelle |
|--------|-------------------|
| Opérationnel | Analyse des spécimens |
| Chambre de forge | Forger des Dons |
| Chambre d’union | Unir deux branches |
| Atelier de contexte | Analyses enrichies par intel / grade d’échantillon |

---

### B3 — Vivarium / Archives vivantes

| | |
|--|--|
| **Nom fiction** | Vivarium (Archives vivantes) |
| **Capacité fondamentale** | **Préserver** le patrimoine vivant pour qu’il reste jouable |
| **Test S/E** | **S** fort (conservation, intégrité) · **E** faible (compagnons prêts à repartir) |

**Débloque / approfondit :**

1. **Accueillir** les branches découvertes / nées  
2. **Contrainte de capacité** (choix : qui garder, qui relâcher, qui exposer)  
3. **Préservation** : soins d’intégrité, trauma, récupération post-expé  
4. Préparer une branche à **repartir** (Éprouver)  
5. (Soft) soutien d’équipe de terrain — **un** levier, pas mini-jeu médical  

**N’est pas :** entrepôt de biomasse, prod passive, Excel de 40 slots fierté.

**Paliers d’esprit (ex.) :**

| Palier | Capacité nouvelle |
|--------|-------------------|
| Actif | Stocker / soigner un premier noyau de branches |
| Extension | Porter un patrimoine plus large (choix plus durs) |
| Préservation avancée | Réduire les temps de trauma / sauver des intégrités limites |
| Quarantaine douce | Isoler une forme instable sans tuer la boucle |

**Fusion absorbée :** Infirmerie coloniale (soins) + partie “slots” de l’ancienne Réserve.

---

### B4 — Galerie des Fondateurs

| | |
|--|--|
| **Nom fiction** | Galerie des Fondateurs |
| **Capacité fondamentale** | **Inscrire** le patrimoine (mémoire, lignées, récit exportable) |
| **Test S/E** | **S** fort (cataloguer / rendre le patrimoine digne) |

**Débloque / approfondit :**

1. **Exposer** les lignées fondées  
2. Consulter **souvenirs, naissances, mémoriaux, deuils**  
3. Rendre la **phrase d’identité** socialement lisible  
4. Rituels d’inscription (célébration narrative)  
5. Contribuer au **MVE** (mémoire non vide, Maison visible)  

**N’est pas :** générateur de moral, bonus pureté de croisement, score prestige P1, cap samples.

**Paliers d’esprit (ex.) :**

| Palier | Capacité nouvelle |
|--------|-------------------|
| Ouverte | Vitrine des lignées + fondation reconnue |
| Archives | Mémoire consultable riche (MVE) |
| Salle des épreuves | Scars / preuves d’expédition exposées |
| (P2 hook) | Graine de réputation — sans classements P1 |

---

### B5 — Baie de projection

| | |
|--|--|
| **Nom fiction** | Baie de projection |
| **Capacité fondamentale** | **Projeter** le patrimoine hors du berceau (sortie Phase 1) |
| **Test S/E** | **E** fort (exploration galactique / export) |

**Débloque / approfondit :**

1. **Seuil unique** : capacité de départ  
2. Revue de l’**export** (lignées, branches, savoir, mémoire, origine)  
3. Cérémonie de passage vers la **Phase 2**  

**N’est pas :** spatioport multi-niveaux, slots de flotte, vanity “vitesse de lancement”.

**États :**

| État | Capacité |
|------|----------|
| Inexistante / scellée | Pas de départ |
| Opérationnelle | Départ autorisé **si** MVE patrimoine rempli |

La Baie **ne se farm pas** pour remplacer le patrimoine.  
Elle **sanctionne** qu’on est prêt à emporter une Maison.

---

## 5. Table officielle compacte

| ID design | Nom | Capacité nouvelle (résumé) | Hérite de |
|-----------|-----|----------------------------|-----------|
| `bureau_expeditions` | Bureau des expéditions | Explorer, drones, prep, carte opérationnelle | Centre d’exploration (+ missions du QG) |
| `laboratoire_central` | Laboratoire central | Analyser, forger, unir | Laboratoire génétique |
| `vivarium` | Vivarium / Archives vivantes | Porter & préserver le vivant | Réserve + Infirmerie |
| `galerie_fondateurs` | Galerie des Fondateurs | Inscrire mémoire & lignées | Musée (purifié) |
| `baie_projection` | Baie de projection | Partir / exporter hors berceau | Spatioport (seuil) |

**Hors liste Phase 1 (officiellement absents) :**

| Ancien | Sort |
|--------|------|
| Centre de commandement | Supprimé (campus narratif) |
| Centre militaire | Supprimé |
| Entrepôt industriel | Supprimé |
| Infirmerie (standalone) | Fusionnée → Vivarium |
| Musée “prestige/moral” | Transformé → Galerie pure |

---

## 6. Mapping capacités ↔ boucle GENESIS

| Verbe de boucle | Bâtiment principal | Bâtiment support |
|-----------------|--------------------|------------------|
| **Rencontrer** | Bureau des expéditions | Vivarium (compagnons prêts) |
| **Comprendre** | Laboratoire central | Bureau (données de terrain) |
| **Transformer** | Laboratoire central | Vivarium (sujets disponibles) |
| **Éprouver** | Bureau des expéditions | Vivarium (récupération) |
| **Inscrire** | Galerie des Fondateurs | Laboratoire (knowledge digne) |
| **Partir** | Baie de projection | Galerie + Labo + Vivarium (MVE) |

Aucun verbe de la boucle n’exige un entrepôt, une caserne ou un QG.

---

## 7. Ce que les bâtiments ne gèrent plus

| Ancienne responsabilité | Nouvelle prise en charge |
|-------------------------|---------------------------|
| Caps matériel / pop | Invisibles ou absents ; personnel borné |
| Production passive | Supprimée |
| Harvest matériel | Supprimé |
| Staffing min/optimal | Automatisé / hors face joueur |
| Défense militaire | Absente P1 ; risque via drones/expé/berceau |
| Moral colonial | État d’équipe soft, pas bâtiment |
| Multi-hangars ×4 | Au plus double engagement rare |
| Lab niv. 50 | 3–4 paliers de capacité |
| Victoire = buildings max | Victoire = MVE + Baie |

---

## 8. Interactions entre bâtiments (architecture)

```
Bureau ──expéditions / drones / carte──► Laboratoire (données, spécimens)
   │                                         │
   │                                         ▼
   │                                   analyse / forge / union
   │                                         │
   ▼                                         ▼
Vivarium ◄──── branches nées / soignées ─────┘
   │
   │  fondateurs, héritiers, deuils
   ▼
Galerie ── mémoire digne ──► MVE
   │
   ▼
Baie de projection ── départ ──► Phase 2
```

**Règle d’architecture :**  
les flux vont du **terrain** vers le **savoir** vers le **patrimoine** vers le **départ**.  
Aucun flux “entrepôt → build queue → caps”.

---

## 9. Critères d’acceptation

L’architecture bâtiments est validée si :

1. Un joueur peut nommer **5 structures** et leur **capacité** en une phrase chacune.  
2. Aucun bâtiment n’existe “pour stocker plus”.  
3. Améliorer un bâtiment change une **décision** (je peux unir / je peux recon / je peux exposer / je peux partir).  
4. Le playtest ne dit plus “je max mon QG”.  
5. Les drones vivent au **Bureau**, le vivant au **Vivarium**, la mémoire à la **Galerie**.  
6. La Baie ne s’ouvre pas sans patrimoine digne.

---

## 10. Liste officielle finale (Phase 1)

### Bâtiments jouables

1. **Bureau des expéditions** — capacité d’explorer et d’instrumenter le terrain  
2. **Laboratoire central** — capacité de comprendre et transformer le vivant  
3. **Vivarium / Archives vivantes** — capacité de porter et préserver le patrimoine vivant  
4. **Galerie des Fondateurs** — capacité d’inscrire et mémoriser les lignées  
5. **Baie de projection** — capacité de quitter le berceau avec ce patrimoine  

### Non-bâtiments

- **Campus de l’Institut** — fiction d’arrivée, pas de progression  

### Explicitement exclus de la Phase 1

- Centre de commandement  
- Centre militaire  
- Entrepôt industriel  
- Infirmerie standalone  
- Tout bâtiment dont la seule fonction est un cap, un passif ou une armée  

---

## 11. Engagement Architecte Gameplay

> **Cinq instruments. Une règle. Zéro colonie.**

Si une future structure ne débloque ni science ni exploration,  
elle n’entre pas sur le campus.

---

*Fin de `05_Batiments.md` — architecture bâtiments Phase 1.*
