# 00 — Philosophie de GENESIS

**Statut :** document fondateur — **canon REBORN**  
**Autorité :** Game Director  
**Branche git :** `genesis-reborn`  
**Suite immédiate :** `01_Core_Gameplay.md` · lois : `12_Rules_Of_Genesis.md`  
**Créatures (canon) :** `07_Creatures.md`

---

## 0. Phrase unique

> **Le joueur dirige un Institut Scientifique chargé de créer, comprendre et préserver le vivant avant de partir explorer une galaxie vivante.**

Tout le reste du projet se juge à cette phrase.

---

## 1. Ce que GENESIS est

GENESIS est :

| GENESIS est… | Signifie |
|--------------|----------|
| Un jeu de **patrimoine biologique** | La progression, c’est ce que tu découvres, analyses, fais évoluer et emportes |
| Un **institut scientifique d’expédition** | Atelier + terrain, pas empire |
| Un **prologue** (Phase 1) vers un **univers persistant** | Le multi n’est pas un mode : c’est la seconde moitié |
| Un **MMO de biodiversité** (ambition) | La galaxie juge, échange, menace et célèbre les collections |

### 1.1 Ce que GENESIS n’est pas

| GENESIS n’est pas… | Pourquoi c’est interdit |
|--------------------|-------------------------|
| Un city-builder spatial | La fierté ne doit pas être les murs |
| Un clone d’OGame avec de la biomasse | Timers de build ≠ cœur |
| Un RPG d’unités à min-maxer | Les stats servent le terrain, pas un gear score |
| Un solo jetable qui meurt au décollage | Le patrimoine **voyage** |
| Un MMORPG de farm / niveau / puissance brute | Le capital, c’est le bestiaire nommé |
| Un simulateur de lignées / dynasties | Collection d’espèces, pas arbre généalogique |
| Deux jeux collés (solo puis “vrai multi”) | **Un seul jeu, deux échelles** |

---

## 2. Unité fondamentale

> **Le véritable personnage du jeu n’est pas le joueur-avatar.**  
> **C’est son patrimoine biologique.**

| Concept | Rôle | Propriétaire architecture |
|---------|------|---------------------------|
| **Patrimoine biologique** | Progression réelle (créatures, savoir, mémoire, collection musée, réputation naissante) | `Architecture/Heritage` |
| **Institut** | Atelier / organisation — **jamais** la finalité | `Architecture/Institute` |
| **Monde natal (berceau)** | Premier terrain d’apprentissage et de co-évolution | `Architecture/World` |
| **Créature** | Visage du patrimoine — espèce de la collection (stats, capacités, pureté) | `Architecture/Creatures` |
| **Musée / collection** | Bestiaire exposé, pas un arbre généalogique | `Architecture/Museum` |
| **Drones** | Instruments : voir et prélever **avant** les humains | `Architecture/Drones` |

Les bâtiments, stocks et camps peuvent changer.  
**Le patrimoine accompagne toute l’aventure.**

Canon créatures détaillé : **`07_Creatures.md`** (nom, espèce, rareté, 4 stats, 1–2 capacités, pureté, statut).

---

## 3. Les quatre actes de l’Institut

1. **Créer** — muter, croiser, enrichir le bestiaire  
2. **Comprendre** — analyser, cartographier, lire le monde et le vivant  
3. **Préserver** — soigner, mémoriser, exposer au musée, ne pas détruire le berceau  
4. **Partir** — emporter le patrimoine, pas la ville  

La boucle jouable détaillée est dans `01_Core_Gameplay.md` :

```
Rencontrer → Comprendre → Transformer → Éprouver → Inscrire
```

Équivalent terrain / créatures (`07`, `14`) :

```
Découvrir → Analyser → Muter / Croiser → Mission → Musée
```

---

## 4. Phase 1 — le prologue

> **Phase 1 = fonder un Institut sur un monde natal et y écrire le premier chapitre du patrimoine, assez digne pour entrer dans la galaxie.**

Ce n’est **pas** « maxer une colonie ».  
C’est **devenir quelqu’un biologiquement**.

### 4.1 Ce que la Phase 1 doit produire

Un joueur qui sait :

- lire un monde et une **espèce** ;  
- composer une expédition (personnel, **drones**, créatures) ;  
- analyser avant d’agir ;  
- muter / croiser avec risque (pureté) ;  
- constituer une **collection** digne (musée) ;  
- juger ce qui mérite d’être emporté (**MVE**).

### 4.2 Double test de toute mécanique Phase 1

| # | Question |
|---|----------|
| **A** | Apprend-elle quelque chose de réutilisable **avec d’autres joueurs** ? |
| **B** | Crée-t-elle une **identité** que le joueur voudra **montrer, protéger ou monnayer** ? |

Si **A = non** et **B = non** → **repenser ou supprimer**.

Voir `11_Multiplayer_Philosophy.md`.

---

## 5. Phase 2 — la seconde moitié (pas un nouveau jeu)

La Phase 2 **n’est pas conçue ici en détail**.  
Elle est **préparée** ici.

| Phase 1 | Phase 2 (même grammaire) |
|---------|---------------------------|
| Une planète | Une galaxie |
| Collection privée | Collection sous le regard des autres |
| Camps non | Camps d’expédition temporaires |
| Savoir pour soi | Licences, contrats, comptoir |
| Stabilité du berceau | Éthique des mondes partagés |

**Règle d’or :**

> La Phase 2 n’ajoute pas de règles fondamentales.  
> Elle augmente uniquement l’échelle.

Détail de continuité : `10_Transition_Phase2.md`.

---

## 6. Charte de design (9 lois)

Ces lois sont **non négociables**. Elles sont reprises et étendues dans `12_Rules_Of_Genesis.md`.

1. **Une mécanique doit préparer la suivante.**  
2. **Une information affichée doit aider à prendre une décision.**  
3. **Une créature est un être de collection, pas une unit de score** — structure simple (`07`).  
4. **Les drones explorent avant les humains.**  
5. **L’exploration crée le besoin de construire, jamais l’inverse.**  
6. **Chaque bâtiment débloque une nouvelle capacité scientifique.**  
7. **La découverte est toujours plus importante que la production.**  
8. **Le patrimoine biologique est la véritable progression du joueur.**  
9. **La Phase 2 n’ajoute pas de règles fondamentales : elle augmente uniquement l’échelle.**

---

## 7. Test unique de toute mécanique

> Est-ce que cela rend le joueur meilleur **explorateur scientifique**,  
> ou enrichit son **patrimoine biologique** d’une façon que la galaxie pourra  
> **juger, échanger, menacer ou célébrer** ?

Si non → **supprimer** ou **repenser**.  
« On a déjà codé ça dans le prototype » n’est **pas** un argument.

---

## 8. Fantaisie joueur

> Je dirige un **Institut**.  
> Mon équipe est petite.  
> Mes **créatures** sont le cœur de mon identité.  
> Mes **drones** me permettent d’aller là où le personnel ne peut pas.  
> Mon **musée** est ce que le monde saura de moi.  
> Un jour, je pars — **avec elles**, pas avec mes murs.

Émotion de fin de Phase 1 cible :

> « Je ne pars pas avec une base. Je pars avec mon **bestiaire**. »

---

## 9. GENESIS (l’IA)

**GENESIS** n’est pas un PNJ.  
C’est le **système qui observe la vie** : il interprète, hypothèse, annonce des seuils.

Il **ne donne jamais de solutions**.  
Détail : `09_GENESIS_AI.md` · ownership : `Architecture/Observation`.

---

## 10. Comment lire le reste du projet

| Ordre | Dossier / doc | Question |
|-------|---------------|----------|
| 1 | **Ce fichier (00)** | Pourquoi GENESIS existe ? |
| 2 | `01_Core_Gameplay.md` | Quelle boucle ? Quelles décisions ? |
| 3 | `02` → `11` | Domaines de design produit |
| 4 | `12_Rules_Of_Genesis.md` | Lois checklist |
| 5 | `14` / `15` | Spec & backlog prototype |
| 6 | `src/` · `public/` | Code — **en dernier** |

**Avant toute feature code :**

```
00 → 01 → doc système concerné → 11 si multi/export → 12 → 14/15 → code
```

---

## 11. Mandat du Game Director (REBORN)

- Supprimer sans nostalgie.  
- Refuser le « on a déjà codé ça ».  
- Préférer **une décision forte** à **trois jauges**.  
- Toute UI qui ressemble à un tableur de colonie est suspecte.  
- Toute ressource qui n’existe que pour remplir un bâtiment est coupable jusqu’à preuve du contraire.  
- Toute mécanique qui ne prépare pas la suivante est hors charte.  
- Toute feature Phase 1 doit passer le **double test** multi (§4.2).  
- Le Design **précède** le code ; le prototype **n’impose** rien.  
- **`07` prime** sur tout vocabulaire legacy (branche, lignée, Don, dynastie).

---

## 12. Signature de stabilité

| Critère | État cible |
|---------|------------|
| Phrase unique claire | Oui (§0) |
| Est / n’est pas | Oui (§1) |
| Unité fondamentale = patrimoine | Oui (§2) |
| Créatures = collection (`07`) | Oui |
| Phase 1 = prologue d’identité | Oui (§4) |
| Phase 2 = échelle, pas nouveau jeu | Oui (§5) |
| Charte 9 lois | Oui (§6) |
| Lien Rules / Multi / Production | Oui |

**Document 00 : stabilisé pour REBORN (aligné `07`).**  
Les amendements se font ici en premier ; le reste du Design s’aligne ensuite.

---

*Fin de `00_Philosophie.md` — fondation de GENESIS REBORN.*
