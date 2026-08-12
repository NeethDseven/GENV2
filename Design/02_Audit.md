# 02 — Audit (Bible) — ce que GENESIS V1 enseigne, et ce qu’on refuse

**Statut :** canon REBORN — **document historique d’import V1**  
**Rôle :** Game Director  
**Objet audité :** le **prototype V1** (code à la racine, tag `v1-last-prototype`)  
**Canon amont :** `00_Philosophie.md` · `01_Core_Gameplay.md`  
**Posture :** on juge ce que le joueur *fait*, pas ce que les docs *disent*

> Ce document ne décrit pas le jeu cible en détail (→ `03`–`11`).  
> Il **tranche** : garder, transformer, supprimer, déplacer.

> **Amendement créatures (alignement `07`) :** le vocabulaire V1 « Branche / Lignée / Dons / Maison / Galerie des Fondateurs » dans les **recommandations de destination** est **supersédé**.  
> Cible actuelle = **collection d’espèces** : fiche simple, mutations, croisements, musée — **pas** d’arbre généalogique (`07_Creatures.md` · `00`–`03` · `12`).  
> Les tableaux ci-dessous gardent le **diagnostic V1** ; toute destination créature se lit via `07`.

---

## 0. Cadre d’évaluation

### 0.1 Six questions (par mécanique V1)

| Code | Question |
|------|----------|
| **W** | Pourquoi elle existe (intention) |
| **L** | Ce qu’elle **apprend réellement** au joueur |
| **P2** | Prépare-t-elle la Phase 2 / le multi ? |
| **IS** | Renforce-t-elle le fantasy **Institut scientifique** ? |
| **PB** | Renforce-t-elle le **patrimoine biologique** ? |
| **CB** | Ressemble-t-elle à du **city-builder** générique ? |

### 0.2 Double test REBORN (obligatoire pour garder)

| # | Question (`00`) |
|---|-----------------|
| **A** | Réutilisable **avec d’autres joueurs** ? |
| **B** | Identité à **montrer / protéger / monnayer** ? |

Si **A = non** et **B = non** → ❌ ou 🔄 profond.

### 0.3 Classification

| Icône | Sens |
|-------|------|
| ❤️ | **Indispensable** — noyau d’identité |
| 🔄 | **À transformer** — idée utile, forme toxique |
| ❌ | **À supprimer** — bruit / contredit la vision |
| 🌌 | **Phase 2 seulement** — pas en prologue |

### 0.4 Boucle de référence (`01`)

```
Rencontrer → Comprendre → Transformer → Éprouver → Inscrire
```

Toute mécanique qui n’enrichit **aucun** de ces verbes est suspecte.

---

## 1. Verdict global sur V1

```
CE QUE V1 ENSEIGNE                    CE QUE REBORN DOIT ENSEIGNER
─────────────────────────────────     ──────────────────────────────
Maxer bâtiments et caps               Écrire un patrimoine digne d’export
Staffing multi-jobs                   Composer une équipe d’Institut
Farmer biomasse / matériel            Rencontrer / comprendre le vivant
Checklist pop + lab + spatio          MVE patrimonial + readiness départ
Min-max prep score                    Éprouver des branches avec risque
Gérer 3 jauges pop                    Protéger berceau + équipe + collection
```

**Verdict :**

> V1 est un **city-builder gêné** qui a greffé un **excellent noyau** génétique/patrimonial  
> sans tuer le centre de gravité colonial.  
> Les docs disent patrimoine ; le **HUD et la sortie** disent encore colonie.

| Score | /10 |
|-------|-----|
| Alignement vision patrimoine | **4** |
| Potentiel après purge | **9** |

**Conséquence REBORN :** on ne « polish » pas V1.  
On reconstruit l’identité ; V1 devient **carrière de pièces** (`Prototype/`, Phase 8).

---

## 2. Synthèse par classification

### 2.1 ❤️ Indispensable (noyau d’or)

| Mécanique V1 / concept | Pourquoi garder | Verbe(s) | A/B multi |
|------------------------|-----------------|----------|-----------|
| Auth / session persistante | Identité joueur | — | A |
| Monde procédural + biomes / zones | Berceau unique, fit | Rencontrer | A+B |
| Stabilité écologique (ISMN) | Tension berceau | Transformer / éthique | A |
| Missions + résolution + récit | Rencontrer / Éprouver | Rencontrer, Éprouver | A+B |
| Composition créatures en expé | Patrimoine en action | Éprouver | A+B |
| Fit Nature ↔ biome | Muscle galactique | Rencontrer, Éprouver | A |
| Analyse progressive | Comprendre | Comprendre | A+B |
| Mutations stratégiques → identité | Transformer (forme cible = capacités / pureté, `07`) | Transformer | A+B |
| Croisement / union | Cœur émotionnel | Transformer | A+B |
| Exposition / mémoire | Musée (collection, pas lignée) | Inscrire | A+B |
| Musée / mémoire / mémoriaux | Histoire | Inscrire | A+B |
| Phrase d’identité / silhouette | Lisibilité sociale | Tous | A+B |
| Échantillons / prélèvements | Savoir brut | Comprendre | A |
| Essences / signature terrain | Lien monde–forge | Transformer | A |
| Spatioport comme **seuil** départ | Partir (pas vanity) | Inscrire / Partir | A |
| Game over écologique | Conséquence morale | — | A |
| Ending / new run | Fermeture digne | — | — |
| Tick / timers sur **rituels** | Asynchrone juste | Comprendre, Transformer | — |
| Heritage / MVE / export (couches récentes) | Pont P2 | Inscrire | A+B |
| Journal / narration | Mémoire | Inscrire | B |

### 2.2 🔄 À transformer

| Mécanique V1 | Direction REBORN |
|--------------|------------------|
| HUD multi-ressources + caps | HUD décisions + 3 monnaies + états |
| Dashboard Commandement hub | **Institut** = hub des 5 décisions |
| Labo max 50 / gates par niveau | Paliers de **capacité** / savoir |
| Centre d’exploration multi-hangars ×4 | Bureau ; 1 expé mémorable > fleet |
| Réserve 15+ filtres | Archives ; ≤6 filtres (espèce, rareté, statut, pureté…) |
| Genetics UI console (protocoles, vœux…) | Rituel 3 choix + preview phrase |
| Biomasse + harvest core | Organique = carburant labo ; **pas** core loop |
| Matériel + entrepôt | Logistique d’expédition |
| Population empire + spés ×5 | Personnel d’Institut 2–3 rôles |
| Drones stock / produce | Parc d’**instruments** (Vols) |
| Moral + santé + fatigue séparés | **État de l’équipe** |
| ISMN + contamination | **Une** stabilité berceau |
| Pureté + stability + anomaly | **Une Intégrité** |
| Modules / flags face joueur | **Capacités ≤2** + 4 stats (`07`) |
| LOOP_STATE NEED_COLLECT / BUILD | États = 5 verbes |
| Victoire multi-seuils (pop, lab…) | **MVE + Baie** |
| Infirmerie standalone | Fusion **Vivarium** (préservation) |
| Musée bonus moral / pureté | Musée = collection pure (`07`) |
| Re-roll planète généreux | 0–1 narratif max |
| Tutoriel CTA commandement/harvest | Première sortie + analyse |

### 2.3 ❌ À supprimer (Phase 1 / identité)

| Mécanique V1 | Pourquoi |
|--------------|----------|
| Centre de commandement (fierté HQ) | Fantasy empire |
| Entrepôt industriel | Warehouse city-builder |
| Centre militaire | Guerre coloniale |
| Soldats / généralistes comme spés majeures | Bruit + mauvaise identité |
| Formation multi-orientation RH | Pipeline jobs |
| Staffing min/optimal joueur | DRH de colonie |
| Production passive mat/bio fierté | Idle city |
| Harvest matériel action | Farm pure |
| Croissance pop comme trophée / win | Empire démographique |
| Care population dashboard | Clicker de jauges |
| Mutations +stats basiques face joueur | Min-max unit |
| Vocabulaire colonie / empire / colons | Identité fausse |
| Build queue comme pilier de fun | OGame skeleton |
| Stats Force / niveaux créature face joueur | Contredit `00`/`07` |
| Culture scientifique JSON obscure | Bruit |

### 2.4 🌌 Phase 2 seulement

| Mécanique | Raison |
|-----------|--------|
| Crédits / marchés monétaires | Bourse galactique |
| Prestige classements chiffrés | Réputation sociale multi |
| Multi-sites / multi-camps | Galaxie |
| Contrats joueurs / licences | Social (`11`) |
| Force armée / PvP structuré | Conflit digne, pas P1 |
| Fleet ops coloniaux | Scale empire ≠ prologue |

---

## 3. Bâtiments V1 → verdict

| Bâtiment V1 | Verdict | Destination REBORN |
|-------------|---------|---------------------|
| Centre de commandement | ❌ | Campus narratif (non-bâtiment) |
| Laboratoire génétique | 🔄❤️ | **Laboratoire central** (plafond bas) |
| Réserve biologique | 🔄❤️ | **Vivarium** |
| Centre d’exploration | 🔄❤️ | **Bureau des expéditions** |
| Musée | ❤️ | **Musée** (collection, `07`) |
| Spatioport | 🔄❤️ | **Baie de projection** (seuil) |
| Infirmerie | 🔄 | Fusion Vivarium |
| Centre militaire | ❌ | — |
| Entrepôt industriel | ❌ | — |

Règle : `05_Batiments.md` — capacité scientifique/exploration ou mort.

---

## 4. Ressources V1 → verdict

| Ressource V1 | Verdict | REBORN |
|--------------|---------|--------|
| Biomasse | 🔄 | Matière organique (carburant actes vivant) |
| Matériel | 🔄 | Logistique d’expédition |
| Échantillons | ❤️ | Prélèvements (+ tags terrain) |
| Essences multi-stocks | 🔄 | Tags sur prélèvements |
| Drones compteur | 🔄 | Parc instruments |
| Pop + 5 spés | 🔄 | Personnel borné |
| Moral / santé / fatigue | 🔄 | État d’équipe |
| ISMN + contamination | 🔄 | Stabilité berceau |
| Pureté / stability… | 🔄 | Intégrité |
| Prestige P1 | 🌌 | Social P2 |
| Caps HUD | 🔄 | Techniques invisibles |

Détail : `06_Ressources.md`.

---

## 5. Ce que le joueur apprend vraiment (comportement)

| Comportement V1 | Domaine coupable | Cible REBORN |
|-----------------|------------------|--------------|
| Maxer des bâtiments | Construction, sortie lab/pop | Constituer un bestiaire digne d’export |
| Farmer des stocks | Harvest, passifs | Chercher du vivant inconnu |
| Staffing RH | Formation, spés | Composer 1 expédition juste |
| Cocher des checks | T8 multi-seuils | Raconter une collection |
| Min-max prep | Loadout tableur | Parier une créature aimée |
| 4 alarmes rouges | Multi-jauges | Une tension berceau / ambition claire |

> **Le jeu enseigne ce qu’il récompense.**  
> V1 récompense encore la **ville**. REBORN doit récompenser le **patrimoine**.

---

## 6. Noyau d’or à libérer (pas à noyer)

Ces pièces V1 (ou concepts déjà greffés) **méritent d’être le centre** :

1. Missions sur biomes uniques  
2. Analyse progressive  
3. Mutations-à-identité + croisement  
4. Créatures en mission (statut, scars)  
5. Musée (collection)  
6. Tension berceau (stabilité)  
7. Export / MVE / départ  

Avec ce noyau seul, GENESIS est déjà un prologue d’Institut.  
Le reste de V1 est **squelette 2016**.

---

## 7. Implications pour les phases REBORN

| Phase REBORN | Ce que cet audit impose |
|--------------|-------------------------|
| **1 Bible** | `03`–`11` ne réintroduisent **rien** de la colonne ❌ |
| **2 Architecture** | Owners déjà posés ; pas de domaine « Colonie empire » |
| **3–5 Systems/Data/Flows** | Modéliser ❤️ et 🔄 seulement |
| **8 Import V1** | Matrice ✅/🔧/❌ = ce tableau |
| **9 Slice** | Boucle 5 verbes sans harvest/HQ |

---

## 8. Liste d’exécution « demain » (design, pas code)

### Supprimer de l’identité produit

1. Entrepôt · militaire · HQ empire  
2. Soldats / généralistes / formation RH  
3. Harvest comme action principale  
4. Progression par 9 bâtiments maxés  
5. Filtres réserve techniques  
6. Langage colonie / colons / empire  
7. Victoire pop 150 + lab 8 comme âme  

### Fusionner

1. Moral + santé + fatigue → état d’équipe  
2. Matériel → logistique  
3. Infirmerie → vivarium préservation  
4. Pureté/stability → pureté + statut (`07`)  
5. Modules/flags UI → capacités (`07`)  

### Mettre au centre

1. Labo · Vivarium · Bureau · **Musée** · Baie  
2. Créature · Collection · Savoir · Drones instruments  
3. MVE + silhouette créature (`07`)  

---

## 9. Critique philosophique (résumé)

V1 a **promis** un institut de vie et **contracté** une colonie asynchrone.  
Le patrimoine a été **ajouté** ; il n’a pas **remplacé** le centre de gravité.

La greffe (créatures, MVE, musée, tutoriel « pas city-builder ») est **juste**.  
Elle échoue tant que le joueur peut se sentir **gouverneur** avant **conservateur d’un bestiaire**.

**Conclusion Game Director :**

> La Phase 1 n’a pas besoin d’être améliorée.  
> Elle a besoin d’être **remplacée dans son centre de gravité**.  
>  
> **Garder :** le vivant, le risque, la collection, le berceau, le départ.  
> **Tuer :** la colonie comme finalité, le build queue comme progression, le HUD d’empire.

---

## 10. Signature de stabilité

| Critère | État |
|---------|------|
| Aligné `00` / `01` | Oui |
| Double test A/B appliqué | Oui |
| ❤️ / 🔄 / ❌ / 🌌 tranché | Oui |
| Lien import Phase 8 | Oui |
| Noyau d’or nommé | Oui |
| Interdit de réintroduire ❌ sans Decision_Log | Oui |

**Document 02 : stabilisé pour REBORN.**  
Détail exhaustif historique conservé en esprit ; ce fichier est la **loi d’audit**.  
Toute exception ❌ → entrée `Decision_Log/` + amendement ici.

---

*Fin de `02_Audit.md` — ce que V1 nous a appris à ne plus être.*
