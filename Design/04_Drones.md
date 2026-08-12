# 04 — Drones

**Statut :** canon REBORN  
**Rôle :** Lead Gameplay Designer  
**Aligné sur :** `00` · `01` · `02` · `03` · `Architecture/Drones` · `Decision_Log/2026-07-28_drones-avant-humains.md`  
**Loi charte :** *Les drones explorent avant les humains.*

---

## 0. Pitch

> Les drones sont les **yeux, les mains et la mémoire de terrain** de l’Institut.  
> Sans eux, le joueur **devine**. Avec eux, il **observe, cartographie, prélève, prépare et analyse** — plus loin, plus sûr, plus juste.

Ils ne combattent **jamais**.  
Ils rendent l’exploration **intelligente**.

---

## 1. Pourquoi ce pilier existe

### 1.1 Échec V1 (rappel `02`)

Drones = stock colonie + slot générique + produce spam → **zéro décision**, zéro muscle P2.

### 1.2 Cible REBORN

Trois piliers de l’expérience terrain :

| Pilier | Rôle | Owner Architecture |
|--------|------|-------------------|
| **Créatures** | Cœur émotionnel & patrimoine (collection, `07`) | `Creatures` |
| **Personnel** | Jugement scientifique | `Institute` |
| **Drones** | Portée, carte, prélèvement, risque déplacé | `Drones` |

Si le joueur peut **ignorer** les drones et bien jouer → le pilier a **échoué**.

### 1.3 Double test multi

| A — multi | B — identité |
|-----------|----------------|
| Camps, recon de sites, courses de carte, coût de présence | Collections nées de vols mémorables ; journal de filets perdus |

---

## 2. Fantaisie & interdits

### Fantaisie

> Je dirige un **parc d’instruments**.  
> Je configure recon, filets, balises.  
> Je choisis ce que je risque : une sonde, un échantillon, une créature aimée.

### Promesse (7)

1. Voir l’opaque  
2. Cartographier le flou  
3. Prélever hors de portée humaine  
4. Réduire le risque sans l’effacer  
5. Préparer la prochaine expé avec de l’intel  
6. Nourrir l’analyse labo  
7. Préparer P2 (présence hors berceau)

### Interdits

| Interdit | Pourquoi |
|----------|----------|
| Combat / DPS | Hors fantasy |
| Spam farm / +999 | City-builder |
| Remplacer les créatures | Tue le cœur |
| Win condition « max drones » | Instruments ≠ trophée |

---

## 3. Huit principes

| # | Principe |
|---|----------|
| 1 | Instruments, pas soldats |
| 2 | Rareté lisible (pool petit) |
| 3 | Configurer > stacker |
| 4 | Intel avant force |
| 5 | Risque **déplacé**, pas effacé |
| 6 | Données = progression (carte, signatures, grades) |
| 7 | Même verbes en P2 (échelle ↑) |
| 8 | Pas de combat |

---

## 4. Boucle drones dans la boucle GENESIS

Amplifie surtout **Rencontrer**, **Comprendre** (via data), préparation d’**Éprouver**.

```
PARC DE DRONES
      │
      ├─► RECON (Vols seuls) ──► carte, risque nommé, POI
      ├─► EXPÉDITION (mixte) ──► prélèvement, extraction, moins de trauma
      └─► RETOUR LABO ────────► knowledge accéléré, prélèvements de grade
                │
                ▼
         PROCHAINE DÉCISION (D1–D5)
```

### Trois modes

| Mode | Acte | Résultat |
|------|------|----------|
| **A. Reconnaissance** | 1–3 drones, peu/pas de personnel | Carte, indices, risque nommé |
| **B. Escorte** | Vols + humains + créatures | Prélèvement, extraction, intel live |
| **C. Relais** | Traitement post-mission | Layers carte, grades, brief labo |

**A → B → C** = jouer GENESIS.  
**B seul** = jouer « OK ».

---

## 5. Anatomie

### 5.1 Attributs (lisibles)

| Attribut | Sens |
|----------|------|
| **Classe** | Observateur · Cartographe · Préleveur · Soutien |
| **Module** | 1 charge utile active |
| **État** | opérationnel · abîmé · hors-service · perdu |
| **Adaptation terrain** | Tags mineurs (froid, pression…) — pas arbre de combat |
| **Signature** | Discrétion face au vivant (prélèvement) |

Pas de niveau 50. Pas d’XP.  
Progression = **config + usage + entretien du parc**.

### 5.2 Un drone = classe + 1 module

Reconfig à l’Institut (temps + logistique).  
Figé en mission.

| Module (ex.) | Classe | Effet d’intention |
|--------------|--------|-------------------|
| Optique longue / spectre bio | Observateur | POI, signatures rares |
| Balise cartographe / sonde de sol | Cartographe | Lecture zone, essences/tags |
| Filet doux / micro-carottage | Préleveur | Vivant intact vs data agressive |
| Stabilisateur / télémétrie / conteneur | Soutien | Extraction, brief, grade préservé |

**Règle :** un module change **ce que la mission peut accomplir**, pas un % de dégâts.

### 5.3 Unité de gestion : le Vol

| Vol | Taille | Exemple |
|-----|--------|---------|
| Léger | 1 | Sonde extrême |
| Standard | 2 | Observateur + Préleveur |
| Complet | 3 | Carte + Observateur + Soutien |

Phase 1 mature (ordre de grandeur) : **6–10 drones** → **3–5 Vols** max déployables (pas tous à la fois).

---

## 6. Quatre classes (pas plus)

### Observateur — voir sans forcer

Signatures, comportements, risque **nommé**, POI de découverte.  
**Décision :** brûler un vol solo pour savoir si la zone vaut une grosse sortie ?

### Cartographe — rendre le monde lisible

Lecture de zone **permanente** (World stocke), couloirs, couches.  
**Décision :** investir un départ en pure carte (peu de loot vivant) ?

### Préleveur — ramener le fragile

Spécimen, grade d’échantillon, tags terrain ; accès hors personnel.  
**Décision :** filet doux (éthique) vs carottage agressif (data + risque) ?

### Soutien — revenir avec l’histoire

Moins de trauma, extraction, télémétrie, conteneur stérile.  
**Décision :** sacrifier un slot découverte pour protéger une créature rare ?

---

## 7. Cinq verbes drones

| Verbe | Output typique |
|-------|----------------|
| **Observer** | Indices de vie, Apex annoncé, silence inquiétant |
| **Cartographier** | Lecture zone ↑, objectifs de prochaine expé |
| **Prélever** | Vivant / grade / tag terrain ; trade-off doux vs agressif |
| **Analyser** (relais) | Labo nourri — ne remplace pas Laboratory |
| **Préparer** | Risque nommé, loadout en **réponse** à l’intel |

Sans intel :

> Risque : « Élevé ? »

Avec intel :

> Risque : « Élevé — spores + pression ; éviter sans Soutien »

---

## 8. Intégration expédition

### Composition (esprit)

| Slot | Plage | Tension |
|------|-------|---------|
| Personnel | 1–3 | Jugement humain |
| Créatures | 0–2 | Patrimoine en jeu |
| Vols | 1–3 | Intel / prélèvement / sécurité |

- Certaines zones **exigent** un type de Vol.  
- Trop de drones = moins de slots créatures.  
- Créatures sans Soutien en zone dure = imprudence narrative.

### Phases de mission (non-combat)

```
Approche → Contact → Interaction → Complication → Extraction → Retour
```

### Risque déplacé

| Avec bons drones | Trade-off |
|------------------|-----------|
| ↓ perte humaine / trauma créature | Drone exposé / slot perdu |
| ↓ mission aveugle | Temps de recon avant |
| ↓ échantillon perdu | Module conteneur |

> Risque_perçu = Danger − Intel − Fit_créatures − Couverture_drones  
> Risque_réel **jamais** nul.

### Découvertes **uniquement** drones (ex.)

Trace spectrale · couloir sûr · prélèvement fantôme · carte de tags terrain · comportement sauvage · zone silencieuse · échantillon exceptionnel.

---

## 9. Contenus dédiés

| Contenu | But |
|---------|-----|
| **Vol de recon** (court) | Carte + risque nommé ; peu de loot vivant |
| **Campagne cartographie** | Zone « lue » ; travail d’Institut |
| **Prélèvement ciblé** | Intel préalable obligatoire |
| **Balise longue** (async) | Migration, fenêtre de capture |

P2 : mêmes structures = yeux sur mondes partagés.

---

## 10. Économie du parc

| Sujet | Règle |
|-------|--------|
| Obtention | Dotation + capacité Bureau + réassemblage rare — **pas** spam produce dashboard |
| Usure | Abîmé / hors-service / perdu |
| Réparation | Temps + **logistique** (`Economy`) |
| Reconfig | À l’Institut seulement |

---

## 11. Décisions joueur (checklist de vie)

Si le joueur ne se pose pas régulièrement ces questions, le pilier est mort.

1. Recon d’abord ou sortie directe ?  
2. Quelle classe manque à mon intel ?  
3. Quel module fixe mon intention ?  
4. Soutien pour une créature aimée, au prix d’une découverte ?  
5. Combien je garde en réserve ?  
6. Sacrifier un drone pour un échantillon unique ?  
7. Prélèvement doux ou agressif ?  
8. Ces données changent-elles mon plan d’analyse / de mutation ?

---

## 12. Coexistence avec les créatures

| | Drones | Créatures |
|--|--------|-----------|
| Premier contact inconnu | Idéal | Risqué |
| Épreuve / mission | Support | **Cœur** |
| Capture rare | Préleveur + intel | Optionnelle |
| Légende de collection | Cités dans le journal | Héros de la fiche / musée |

Les drones **servent** le patrimoine ; ils ne le **remplacent** pas.

---

## 13. Phase 2 (échelle, mêmes règles)

| P1 | P2 |
|----|-----|
| Sonde locale | Sonde orbitale / atmosphérique |
| Carte de zone | Carte de site partagé |
| Usure de sortie | Coût de **présence** de camp |
| Recon avant créature rare | Recon avant camp multi |

**Gameplay de Vols identique.** Seuls distance et coût changent.  
Pas de deathball, pas de combat drone.

---

## 14. UI (intentions — Phase 6 dessinera)

- Drones dans **composition d’expédition** et Bureau — **pas** à côté de l’organique comme gold.  
- Icônes classe + module + état.  
- Effets en langage humain.  
- Intel zone : Inconnu · Entrevue · Lue · Cartographiée.

---

## 15. Critères d’acceptation playtest

Les joueurs disent :

1. « Sans recon, je joue à l’aveugle. »  
2. « Composer mes Vols compte autant que mes créatures. »  
3. « J’ai découvert un truc impossible sans sonde. »  
4. « J’ai sacrifié un drone pour sauver une créature. »  
5. « La carte de mon monde, c’est mon travail. »  
6. « Je sais déjà gérer un parc pour des camps ailleurs. »

Ils ne disent **pas** : « Je spam des drones pour le % de win. »

---

## 16. Signature de stabilité

| Critère | État |
|---------|------|
| Aligné `00`–`03` | Oui |
| 4 classes, 0 combat | Oui |
| Vols + modules | Oui |
| Modes A/B/C | Oui |
| Risque déplacé | Oui |
| Muscle P2 explicite | Oui |
| Double test A/B | Oui |
| Owner Architecture | `Drones` |
| Interdit stock colonie V1 | Oui (`02`) |

**Document 04 : stabilisé pour REBORN.**  
Implémentation future : domaine `Architecture/Drones` + `feature/drones` — **après** Phases Bible → Flux.

---

*Fin de `04_Drones.md` — pilier instruments d’exploration.*
