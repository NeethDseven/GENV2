# 06 — Ressources : économie de l’Institut

**Rôle :** Game Economy Designer  
**Posture :** simplifier au maximum **sans** aplatir la profondeur (décisions, tension, Phase 2)  
**Documents frères :** `01_Core_Gameplay.md` · `03_Phase1.md` · `04_Drones.md` · `05_Batiments.md`

---

## 0. Principes d’économie GENESIS

### 0.1 Définition

> **Une ressource est un carburant d’acte** — scientifique, d’exploration, de préservation ou de projection.  
> Si elle n’alimente pas la boucle  
> `Rencontrer → Comprendre → Transformer → Éprouver → Inscrire (→ Partir)`  
> **elle sort** ou devient un **état**, pas une monnaie.

### 0.2 Ce qu’une ressource n’est pas

| Interdit | Pourquoi |
|----------|----------|
| Score de fierté HUD | Enseigne le city-builder |
| Quatrième monnaie pour le même acte | Redondance cognitive |
| Objectif de farm principal | Détourne du vivant |
| Cap visible comme trophée | “Maxer le stock” ≠ patrimoine |
| Monnaie de combat | Hors fantasy |

### 0.3 Profondeur ≠ nombre de monnaies

La profondeur vient de :

- **quoi** dépenser (organique vs prélèvement vs drone),  
- **sur quoi** (quelle branche, quelle zone),  
- **avec quel risque** (berceau, intégrité, perte d’instrument),  

…pas de cinq jauges parallèles qui coûtent toutes “un peu de tout”.

### 0.4 Test Phase 2

Pour chaque ressource :

> Est-ce qu’un explorateur galactique (camps, génomes, Maison) a encore besoin de **ce muscle économique** ?

Si non → locale jetable ou hors P1.

---

## 1. Inventaire exhaustif (état actuel + soft resources)

On analyse **tout ce qui se compte, se stocke, se dépense ou se regarde comme une jauge**.

### A. Monnaies / stocks classiques

| Ressource | Présence actuelle |
|-----------|-------------------|
| Biomasse | Stock principal, harvest, coûts labo/build |
| Matériel | Stock, build, missions, entrepôt |
| Échantillons (samples) | Stock, analyse/mutations |
| Drones (compteur) | Stock + slots expé |
| Essences de biome | Stocks typés, mutations élémentaires |
| Population | Effectif + caps |
| Spécialisations (sous-stocks) | Scientifiques, soldats, etc. |
| Crédits | Absents P1 (vision P2) |

### B. Jauges d’état (souvent traitées comme ressources)

| “Ressource” | Présence actuelle |
|-------------|-------------------|
| Moral | 0–100, sortie, events |
| Santé population | 0–100 |
| Fatigue | Missions / récup |
| ISMN (stabilité berceau) | Continuité planète |
| Contamination | Manipulations / soins |
| Prestige | Musée / classements futurs |
| Pureté / stability / anomaly | Créatures (multi-thermomètres) |
| Intégrité (modèle Branche) | Cible design récente |
| Knowledge | Profondeur d’analyse |
| Caps (bio, mat, samples, pop, créatures) | Multi-plafonds HUD |

### C. Patrimoine (souvent confondu avec “ressources”)

| Élément | Nature réelle |
|---------|----------------|
| Branches / créatures | **Objets de jeu / patrimoine**, pas monnaies |
| Lignées | Identité portable |
| Mémoire / souvenirs | Récit |
| % découverte | Progression territoriale |
| Dons | Identité de branche |

---

## 2. Analyse ressource par ressource

Légende amusante : **Oui** · **Faible** · **Non** · **Toxique** (amuse mal / mauvaise boucle)

---

### 2.1 Biomasse (matière organique)

| Critère | Évaluation |
|---------|------------|
| **Pourquoi elle existe** | Carburant universel bio : mutations, builds, soins, “vie comme fuel”. |
| **Que permet-elle** | Payer des actes de labo / terrain / construction. |
| **Amusante ?** | **Faible → Toxique** si harvest = core loop. **Oui** si rare et liée aux actes (retour de mission, sous-produit de prélèvement). |
| **Prépare P2 ?** | **Faible** — camps auront besoin de “matière de travail”, mais pas d’un empire de farm. |
| **Verdict** | **Garder** sous le nom **Matière organique**. Rôle unique : *carburant des actes sur le vivant* (forge, union, préservation, parfois recon). **Jamais** monarque du HUD. |

---

### 2.2 Matériel

| Critère | Évaluation |
|---------|------------|
| **Pourquoi elle existe** | Construire bâtiments, crafts, coûts d’expé “équipement”. |
| **Que permet-elle** | Progression structurelle city-builder + quelques coûts mission. |
| **Amusante ?** | **Non** en l’état (farm / entrepôt). **Faible** si réduite à logistique d’expé. |
| **Prépare P2 ?** | **Faible** — logistique de camp oui ; stock de fierté non. |
| **Verdict** | **Fusionner** dans **Logistique d’expédition** (voir §4). Supprimer harvest matériel et caps trophées. |

---

### 2.3 Échantillons (samples / prélèvements)

| Critère | Évaluation |
|---------|------------|
| **Pourquoi elle existe** | Savoir brut ramené du terrain pour analyser / manipuler. |
| **Que permet-elle** | Comprendre et transformer (coûts labo). |
| **Amusante ?** | **Oui** — tangible (“j’ai ramené de la science”). |
| **Prépare P2 ?** | **Oui fort** — génomes, data, bourse, contrats. |
| **Verdict** | **Garder** comme **Prélèvements**. Cœur économique du savoir. |

---

### 2.4 Essences de biome

| Critère | Évaluation |
|---------|------------|
| **Pourquoi elle existe** | Lier forge / Nature au terrain ; coût thématique. |
| **Que permet-elle** | Mutations élémentaires, forges “de lieu”. |
| **Amusante ?** | **Oui** si rares et lisibles ; **Faible** si 8 stocks parallèles illisibles. |
| **Prépare P2 ?** | **Oui** — signatures de mondes partagés. |
| **Verdict** | **Garder 1 couche** : soit stocks d’essences **simples** (par famille de biome), soit **prélèvements typés** (grade + tag de terrain). **Pas** essence + sample + biomasse sans hiérarchie. Voir simplification §4. |

---

### 2.5 Drones (compteur stock)

| Critère | Évaluation |
|---------|------------|
| **Pourquoi elle existe** | Support mission ; “produire des drones”. |
| **Que permet-elle** | Aujourd’hui : +slots génériques. Cible : instruments (voir `04_Drones.md`). |
| **Amusante ?** | **Non** comme +999 ; **Oui** comme parc limité d’instruments. |
| **Prépare P2 ?** | **Oui fort** — coût de présence hors berceau. |
| **Verdict** | **Garder** comme **Parc de drones** (instruments), **pas** comme monnaie de production. Économie = usure / perte / reconfig, pas farm. |

---

### 2.6 Population + spécialisations

| Critère | Évaluation |
|---------|------------|
| **Pourquoi elle existe** | Staff, missions, croissance, game over démographique, jobs. |
| **Que permet-elle** | Limiter les actes parallèles ; fantasy “colons”. |
| **Amusante ?** | **Faible** (census) ; spés multiples **Non** (RH). |
| **Prépare P2 ?** | **Contre-productif** si empire pop ; **Oui faible** si petite équipe d’expé. |
| **Verdict** | **Transformer** en **Personnel d’Institut** (pool borné, 2–3 rôles). Plus de monnaies “soldats/généralistes”. Plus de croissance comme win. |

---

### 2.7 Moral

| Critère | Évaluation |
|---------|------------|
| **Pourquoi elle existe** | Happiness coloniale, efficacité, seuil sortie/GO. |
| **Que permet-elle** | Buff/malus globaux ; mini-jeu care. |
| **Amusante ?** | **Non** (jauge à nourrir). |
| **Prépare P2 ?** | **Non**. |
| **Verdict** | **Supprimer** comme ressource. Absorber dans **État de l’équipe** (soft). |

---

### 2.8 Santé population

| Critère | Évaluation |
|---------|------------|
| **Pourquoi elle existe** | Survival soft, épidémies, malus. |
| **Que permet-elle** | Même famille que moral. |
| **Amusante ?** | **Non** en triple jauge. |
| **Prépare P2 ?** | **Faible** (risque d’expé). |
| **Verdict** | **Fusionner** → État de l’équipe. |

---

### 2.9 Fatigue

| Critère | Évaluation |
|---------|------------|
| **Pourquoi elle existe** | Anti-spam missions. |
| **Que permet-elle** | Rythme d’exploration. |
| **Amusante ?** | **Faible** seule ; **Oui** comme “l’équipe doit souffler” si lisible. |
| **Prépare P2 ?** | **Oui** — rotation de camp. |
| **Verdict** | **Garder le concept** dans **État de l’équipe / disponibilité**, pas 3ᵉ jauge HUD. |

---

### 2.10 ISMN (stabilité du berceau)

| Critère | Évaluation |
|---------|------------|
| **Pourquoi elle existe** | Prix écologique des ambitions ; co-évolution. |
| **Que permet-elle** | Tension centrale ; game over écologique ; frein à la forge abusive. |
| **Amusante ?** | **Oui** — décisions morales/stratégiques. |
| **Prépare P2 ?** | **Oui fort** — ne pas raser les mondes publics. |
| **Verdict** | **Garder** comme **état du monde** (pas une monnaie à “farmer”). Affichage bande clair. |

---

### 2.11 Contamination

| Critère | Évaluation |
|---------|------------|
| **Pourquoi elle existe** | Risque d’échec génétique → pollution. |
| **Que permet-elle** | 2ᵉ jauge rouge parallèle à l’ISMN. |
| **Amusante ?** | **Faible** (redondante). |
| **Prépare P2 ?** | **Faible** si fusionnée dans stabilité / intégrité. |
| **Verdict** | **Fusionner** dans **Stabilité du berceau** et/ou **Intégrité** des branches. Une tension, pas deux monnaies de peur. |

---

### 2.12 Prestige

| Critère | Évaluation |
|---------|------------|
| **Pourquoi elle existe** | Classements, renommée musée. |
| **Que permet-elle** | Score abstrait. |
| **Amusante ?** | **Faible** en solo P1. |
| **Prépare P2 ?** | **Oui** en multi social. |
| **Verdict** | **Phase 2**. En P1 : mémoire qualitative, pas jauge prestige. |

---

### 2.13 Pureté / stability / anomaly (créature)

| Critère | Évaluation |
|---------|------------|
| **Pourquoi elle existe** | Risque génétique historique multi-couches. |
| **Que permet-elle** | Trois lectures du même “état de santé du génome”. |
| **Amusante ?** | **Non** (confusion). |
| **Prépare P2 ?** | **Oui** via **une** qualité d’intégrité échangeable. |
| **Verdict** | **Une ressource d’état par branche : Intégrité**. |

---

### 2.14 Knowledge

| Critère | Évaluation |
|---------|------------|
| **Pourquoi elle existe** | Mesurer ce qui est compris. |
| **Que permet-elle** | Progression Comprendre ; valeur d’export. |
| **Amusante ?** | **Oui** (révélation). |
| **Prépare P2 ?** | **Oui fort** — génomes cartographiés valent plus. |
| **Verdict** | **Garder** comme **état de branche / de savoir**, pas stock empire. |

---

### 2.15 Caps (tous)

| Critère | Évaluation |
|---------|------------|
| **Pourquoi elle existe** | Balancing anti-hoard + progression bâtiments. |
| **Que permet-elle** | “Upgrade pour stocker plus”. |
| **Amusante ?** | **Toxique** si visibles comme but. |
| **Prépare P2 ?** | **Non**. |
| **Verdict** | **Techniques invisibles** ou contraintes narratives (vivarium). Jamais fierté. |

---

### 2.16 Branches, lignées, mémoire, % découverte

| Critère | Évaluation |
|---------|------------|
| **Nature** | **Patrimoine / progression**, pas monnaies. |
| **Amusantes ?** | **Oui** — cœur du jeu. |
| **P2 ?** | **Oui fort**. |
| **Verdict** | Rester hors “économie de stocks”. Le HUD les traite comme **identité**, pas comme gold. |

---

## 3. Redondances (carte)

### 3.1 Même job, plusieurs monnaies

| Job économique | Ressources qui le font aujourd’hui | Problème |
|----------------|--------------------------------------|----------|
| Payer le labo | Biomasse + samples + parfois essence + moral | Trop de portes pour un acte |
| Payer la construction | Matériel + biomasse + timers + staff | City-builder |
| Tenir la colonie en vie | Moral + santé + fatigue + care + events | Triple maintenance |
| Mesurer le danger écologique | ISMN + contamination + events | Double alarme |
| Mesurer la “qualité génétique” | Pureté + stability + anomaly + corruption | Triple thermomètre |
| Limiter ce qu’on porte | Caps samples + caps bio + slots créatures + pop cap | Quatre plafonds |
| Soutenir l’expédition | Matériel mission + drones stock + spés soldats | Trois “supports” |

### 3.2 Redondances critiques à tuer

1. **Matériel ≈ logistique de build** + **biomasse ≈ fuel universel** → deux fuels d’infrastructure.  
2. **Samples ≈ essences ≈ “bits de science”** sans hiérarchie claire.  
3. **Moral ≈ santé ≈ fatigue** → un seul **état d’équipe**.  
4. **ISMN ≈ contamination** → un seul **état berceau**.  
5. **Pureté ≈ stability ≈ anomaly** → une **Intégrité**.  
6. **Drones monnaies ≈ matériel de mission** → drones = instruments, logistique = soft cost.  
7. **Pop + 5 spés** → personnel borné.

---

## 4. Système simplifié (officiel)

### 4.1 Philosophie en une phrase

> **Peu de monnaies d’acte ; beaucoup de sens sur le vivant, le berceau et les instruments.**

### 4.2 Couches économiques

| Couche | Nature | Exemples | Visible HUD ? |
|--------|--------|----------|----------------|
| **1. Acte** | Stocks dépensables | Organique, Prélèvements, (Essences) | Secondaire |
| **2. Instruments** | Parc limité | Drones (Vols) | Dans l’expé / Bureau |
| **3. Agents** | Pool borné | Personnel d’Institut | Disponibilité, pas census |
| **4. États** | Jauges de tension | Berceau, Équipe, Intégrité branche | Bandes claires |
| **5. Patrimoine** | Progression d’identité | Branches, lignées, knowledge, mémoire | **Primaire** |

Les couches 4–5 ne sont **pas** des monnaies à farmer.

---

### 4.3 Liste blanche — monnaies d’acte Phase 1

**Exactement trois monnaies** (+ une option de signature terrain).

#### R1 — Matière organique

| | |
|--|--|
| **Nom fiction** | Matière organique (ex-biomasse) |
| **Rôle unique** | Carburant des **actes sur le vivant** et de la préservation |
| **Permet** | Forge, union, soins d’intégrité, parfois maintenance de terrain douce |
| **Sources** | Retours d’expédition, sous-produits de prélèvement, très faible passif **invisible** max |
| **Interdit** | Harvest spam comme boucle ; coût principal de “bâtiments vanity” |
| **Amusant si** | On la regarde comme “j’ai de quoi travailler au labo”, pas “j’ai 9999” |
| **P2** | Consommable de camp / labo mobile — même muscle |

#### R2 — Prélèvements

| | |
|--|--|
| **Nom fiction** | Prélèvements (ex-échantillons) |
| **Rôle unique** | **Savoir brut** — payer Comprendre et nourrir Transformer |
| **Permet** | Analyses, protocoles de forge/union, preuves de terrain |
| **Sources** | Missions, Préleveurs drones, parfois observation longue |
| **Grade** | Standard / soigné / exceptionnel (profondeur, pas 10 monnaies) |
| **Amusant si** | Chaque prélèvement raconte une sortie |
| **P2** | Data / génomes — cœur de bourse et contrats |

#### R3 — Logistique d’expédition

| | |
|--|--|
| **Nom fiction** | Logistique (fusion **matériel** + coûts d’ops) |
| **Rôle unique** | Payer ce qui n’est **pas** le vivant : recon, reconfig drones, projection, sortie équipée |
| **Permet** | Lancer certaines expés, réparer/reconfigurer drones, activer la Baie (coût soft) |
| **Sources** | Retours d’expé (faible), événements rares, **pas** harvest industriel |
| **Stock** | Petit, souvent “assez / juste / insuffisant” plutôt que +999 |
| **Amusant si** | Crée le choix “est-ce que cette sortie vaut mon reserve logistique ?” |
| **P2** | Coût de pose de camp / rotation — même muscle |

#### R4 (option officielle retenue) — Signatures de terrain

| | |
|--|--|
| **Nom fiction** | Essences / signatures de biome |
| **Rôle unique** | **Lier une forge au monde** (Nature ↔ lieu) |
| **Règle anti-redondance** | **Une seule** couche de “typage terrain” |
| **Implémentation recommandée** | **Prélèvements tagués** (un prélèvement volcanique) **OU** petit stock d’essences par **famille** (≤4–6 familles), pas 20 monnaies |
| **Si trop lourd en playtest** | Absorber 100 % dans prélèvements typés → **3 monnaies** seulement |
| **P2** | Signatures de mondes partagés — fort |

**Décision d’économie (canon de ce document) :**

> Phase 1 ship avec **3 monnaies d’acte** (Organique, Prélèvements, Logistique)  
> + **tags de terrain sur les prélèvements** (essences = propriété du prélèvement, pas 4ᵉ stock empire).  
> Si le fantasy “fiole d’essence” est indispensable en UX, essences = **vue filtrée** des prélèvements typés, pas une double comptabilité.

---

### 4.4 Instruments (pas monnaies)

#### Parc de drones

| | |
|--|--|
| **Nature** | Instruments d’observation / carte / prélèvement / soutien |
| **Économie** | Taille de parc, usure, perte, reconfig (coût Logistique) |
| **HUD** | Composition d’expé + Bureau — **jamais** à côté de l’organique comme score |
| **P2** | Coût de présence — pilier |

Voir `04_Drones.md`.

---

### 4.5 Agents

#### Personnel d’Institut

| | |
|--|--|
| **Nature** | Pool borné (dizaines, pas centaines) |
| **Rôles max** | Scientifique · Opérateur de terrain · Préservateur (ou équivalent 2–3) |
| **Économie** | Disponibilité / fatigue d’équipe, pas formation multi-jobs |
| **Sources** | Stable ; pas de “croissance pour gagner” |
| **P2** | Petites équipes de camp |

---

### 4.6 États (tensions, pas farm)

| État | Porte sur | Rôle | Anciennes jauges fusionnées |
|------|-----------|------|------------------------------|
| **Stabilité du berceau** | Monde natal | Prix de l’ambition ; GO écologique | ISMN + contamination |
| **État de l’équipe** | Personnel | Disponibilité à sortir / travailler | Moral + santé + fatigue |
| **Intégrité** | Branche | Santé du vivant ; fertilité ; scars | Pureté + stability + anomaly + corruption partielle |
| **Knowledge** | Branche / savoir | Ce qui est compris | Profondeurs d’analyse |
| **Lecture de zone** | Carte | Ce qui est cartographié | % découverte (renommé, non monétisé) |

**Règle :** on ne “collecte” pas un état. On le **fait bouger** par des actes.

---

### 4.7 Patrimoine (hors économie de stock)

| Élément | Rôle économique |
|---------|-----------------|
| Branches | Objets centraux — coût d’opportunité (slots vivarium, risque) |
| Lignées | Identité — pas monétisées en P1 |
| Mémoire | Condition MVE — pas une jauge à farmer |
| Dons | Identité — issus de dépenses Organique/Prélèvements + risque |

Le “vrai wealth” du joueur = **patrimoine**, pas les stocks R1–R3.

---

## 5. Qui paie quoi (matrice des actes)

| Acte | Organique | Prélèvements | Logistique | Drones | Personnel | Berceau | Intégrité |
|------|:---------:|:------------:|:----------:|:------:|:---------:|:-------:|:---------:|
| Recon / cartographier | — | — | ● | ●● | ○ | ○ | — |
| Expédition complète | ○ | **loot** | ● | ●● | ●● | ○ | ○ (si branches) |
| Analyse | ○ | ●● | — | — | ● | — | — |
| Forge (Don) | ●● | ● | — | — | ● | ● | ● |
| Union | ●● | ● | — | — | ● | ● | ●● |
| Préservation / soins | ● | ○ | — | — | ● | — | **récup** |
| Reconfig / réparation drones | — | — | ●● | **état** | ○ | — | — |
| Palier de bâtiment (rare) | ● | ○ | ● | — | — | — | — |
| Baie / départ | ○ | — | ● | ○ | ● | — | — |

●● coût principal · ● coût secondaire · ○ parfois / soft · **loot** = source · **récup** = restauration

**Lecture :** plus un acte touche le **vivant**, plus il coûte **Organique + Prélèvements + risque**.  
Plus un acte touche le **terrain instrumenté**, plus il coûte **Logistique + Drones**.

---

## 6. Sources & sinks (boucle saine)

### 6.1 Sources (alignées exploration)

| Source | Organique | Prélèvements | Logistique |
|--------|-----------|--------------|------------|
| Mission / recon | ● | ●● | ● |
| Prélèvement drone réussi | ○ | ●● | — |
| Sous-produit d’analyse | ● | — | — |
| Harvest dashboard spam | **interdit** | **interdit** | **interdit** |
| Prod passive bâtiment | **interdit** (ou négligeable invisible) | — | — |

### 6.2 Sinks (alignés science)

| Sink | Ressource |
|------|-----------|
| Analyse / forge / union / soins | Organique, Prélèvements |
| Sorties, drones, Baie | Logistique |
| Pertes | Drones, Intégrité, parfois Personnel |
| Ambition excessive | Stabilité berceau |

### 6.3 Anti-patterns économiques

| Pattern | Effet | Verdict |
|---------|-------|---------|
| Triple coût identique sur chaque clic | Bruit | Interdit |
| Farm pour build pour farm | City-builder | Interdit |
| Stocks qui montent sans décision | Ennui | Caps soft invisibles + sinks forts |
| Ressource qui n’achète qu’un buff plat | Creux | Interdit |
| 6 monnaies pour 1 rituel | Friction | Max 2 monnaies par acte + 1 risque |

---

## 7. HUD économique (ce que le joueur voit)

### Priorité d’affichage

```
1. Patrimoine (lignées, branches clés, knowledge)
2. Expéditions en cours / carte
3. Drones & personnel disponibles
4. Stabilité du berceau · État de l’équipe
5. Organique · Prélèvements · Logistique   ← monnaies, en bas ou compact
```

### Ce qui disparaît du HUD “score”

- Caps matériels / pop  
- Moral / santé / fatigue séparés  
- Contamination séparée  
- Prestige  
- Compteur drones style gold  
- Pureté + stability côte à côte  

---

## 8. Comparatif avant / après

| Avant (typique) | Après (officiel) |
|-----------------|------------------|
| Biomasse | **Matière organique** |
| Matériel | **Logistique d’expédition** |
| Échantillons | **Prélèvements** (+ tags terrain) |
| Essences (stocks parallèles) | **Tags** sur prélèvements (ou vue essence sans double compte) |
| Drones stock | **Parc d’instruments** |
| Population + 5 spés | **Personnel** 2–3 rôles |
| Moral + santé + fatigue | **État de l’équipe** |
| ISMN + contamination | **Stabilité du berceau** |
| Pureté + stability + anomaly | **Intégrité** |
| Prestige | **P2** |
| Crédits | **P2** |
| Multi-caps fierté | **Invisibles / vivarium** |
| Branches / lignées | **Patrimoine** (wealth réel) |

**Monnaies d’acte joueur :** **3**  
**États de tension majeurs :** **2** globaux (berceau, équipe) + **par branche** (intégrité, knowledge)

---

## 9. Profondeur conservée (ce qu’on ne simplifie pas)

Simplifier les monnaies **n’enlève pas** :

| Profondeur | Où elle vit |
|------------|-------------|
| Fit Nature ↔ biome | Décisions d’expé / forge |
| Choix de modules drones | Loadout |
| Sacrifice drone vs branche | Risque |
| Grade de prélèvement | Qualité d’analyse |
| Intégrité vs ambition | Forge / union |
| Stabilité berceau | Éthique d’Institut |
| Lignées & mémoire | Patrimoine |
| Knowledge progressif | Comprendre |

La profondeur est **décisionnelle et narrative**, pas monétaire.

---

## 10. Préparation Phase 2 (contrat économique)

| Muscle P1 | Usage P2 |
|-----------|----------|
| Gérer 3 monnaies d’acte | Camps : organique + data + logistique légère |
| Prélèvements comme wealth de savoir | Bourse / licences / contrats |
| Tags de terrain | Signatures de mondes partagés |
| Parc drones limité | Coût de présence |
| Petite équipe | Roster de camp |
| Stabilité berceau | Réputation écologique |
| Intégrité des branches | Qualité d’échange du patrimoine |
| Patrimoine > stocks | On ne trade pas une ville |

**Absents P1 volontairement :** crédits, prestige classements, marchés multi.

---

## 11. Règles d’économie (canon)

1. **3 monnaies d’acte max** en Phase 1.  
2. **1 coût principal** par type d’acte (vivant vs terrain).  
3. **0 harvest** en core loop.  
4. **0 production passive** de fierté.  
5. **États ≠ monnaies** : on ne farm pas le moral.  
6. **Patrimoine ≠ stock** : la wealth visible, c’est la Maison.  
7. **Caps techniques invisibles.**  
8. Toute nouvelle ressource doit **tuer une redondance** ou **refuser d’entrer**.

---

## 12. Liste officielle finale

### Monnaies d’acte (Phase 1)

1. **Matière organique** — carburant des actes sur le vivant  
2. **Prélèvements** — savoir brut (avec tags de terrain)  
3. **Logistique d’expédition** — carburant des ops / drones / projection  

### Instruments & agents

4. **Parc de drones** — instruments (économie d’usure, pas de farm)  
5. **Personnel d’Institut** — pool borné, disponibilité  

### États

6. **Stabilité du berceau**  
7. **État de l’équipe**  
8. **Intégrité** (par branche)  
9. **Knowledge** (par branche / savoir)  
10. **Lecture de zone** (carte)  

### Wealth réelle

11. **Patrimoine** — branches, lignées, mémoire, Dons  

### Phase 2 uniquement

- Crédits / marchés  
- Prestige social chiffré  

### Supprimés / fusionnés (officiel)

| Sortant | Destin |
|---------|--------|
| Matériel (stock empire) | → Logistique |
| Essences multi-stocks | → Tags prélèvements |
| Moral / santé / fatigue séparés | → État de l’équipe |
| Contamination | → Stabilité berceau |
| Pureté / stability / anomaly | → Intégrité |
| Spés multiples + pop empire | → Personnel |
| Prestige P1 | → P2 |
| Caps HUD | → Invisibles |
| Harvest comme ressource-loop | → Supprimé |

---

## 13. Critères d’acceptation (playtest économie)

L’économie est validée si les joueurs :

1. Peuvent expliquer les **3 monnaies** en dix secondes.  
2. Ne farm **jamais** “pour le stock”.  
3. Ressentent la rareté sur **drones, intégrité, berceau**, pas sur un 4ᵉ gold.  
4. Associent prélèvements à **science**, organique à **travail du vivant**, logistique à **sortie**.  
5. Disent que leur richesse, c’est leur **Maison**, pas leur HUD de ressources.  

Échec si : “il me faut plus de matériel pour up l’entrepôt.”

---

## 14. Engagement Game Economy Designer

> **Trois carburants. Deux tensions. Un patrimoine.**  
> Tout le reste est du bruit de colonie.

La simplification n’est pas un appauvrissement :  
c’est le prix pour que chaque dépense soit **lisible, mémorable, et utile en galaxie**.

---

*Fin de `06_Ressources.md` — économie Phase 1 simplifiée.*
