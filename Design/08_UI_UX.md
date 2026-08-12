# 08 — UI / UX (philosophie des décisions)

**Statut :** canon REBORN  
**Objet :** philosophie d’interface — **pas** l’esthétique.  
**Phase wireframes :** roadmap Phase 6 (`UI/`, Excalidraw…) **après** les flux.

> **Montre la décision. Cache le système.**  
> **Une action primaire par écran. Une fiche créature lisible partout.**  
> **Hub = Institut, pas QG de colonie.**

**Aligné :** `01` (D1–D5) · `03` · `07` · `Architecture/*`

---

## 0. Diagnostic V1

UI construite autour des **systèmes** (Commandement, multi-HUD, Lab+Biologie, checklist T8).  
Le joueur doit **traduire** le système en décision.

**Cible :** UI autour des **décisions** D1–D5.

---

## 1. Principes

1. Decision First  
2. One Primary Action  
3. Progressive disclosure  
4. Creature = unit of UI (phrase d’identité)  
5. Institute, not Empire  
6. Click budget : acte core ≤ **3 clics** depuis hub  
7. HUD = état de décision  
8. Feedback = histoire  
9. Conseiller = ouverture de décision (pas solution)  
10. Même mental model P2  

---

## 2. Cinq décisions = nav mentale

| # | Question | Verbe |
|---|----------|-------|
| D1 | Où rencontrer ? | Rencontrer |
| D2 | Qui analyser ? | Comprendre |
| D3 | Que muter / croiser ? | Transformer |
| D4 | Qui envoyer en mission ? | Éprouver |
| D5 | Quoi exposer / emporter ? | Inscrire / Partir |

---

## 3. Arborescence officielle

```
INSTITUT      → hub des 5 décisions
MONDE         → où ? (carte)
EXPÉDITION    → qui / quoi risquer ?
LABO          → qui analyser ? → RITUEL (muter/croiser)
MUSÉE         → quoi exposer ?
DÉPART        → partir digne ? (MVE + Baie)
MÉMOIRE       → secondaire (journal)
FICHE CRÉATURE → overlay partout
```

**Biologie** n’est plus nav top-level : c’est le **mode Rituel** depuis le Labo.

### Mapping V1 → REBORN

| V1 | REBORN |
|----|--------|
| Commandement | **Institut** |
| Monde natal | **Monde** |
| Exploration | **Expédition** |
| Laboratoire | **Labo** |
| Biologie | **Rituel** (muter / croiser) |
| Musée | **Musée** (collection) |
| Sortie P1 | **Départ** |
| Journal | **Mémoire** |

---

## 4. Écrans — question + action primaire

| Écran | Question | Action primaire |
|-------|----------|-----------------|
| Institut | Que décider maintenant ? | CTA recommandée |
| Monde | Où le berceau m’appelle ? | Préparer sortie zone |
| Expédition | Qui risquons-nous ? | Lancer / Recon / Mission |
| Labo | Qui est mon patrimoine ? | Analyser / sélectionner |
| Rituel | Que mutons-nous, à quel prix ? | Confirmer (3–5 clics) |
| Musée | Quelle histoire exposons-nous ? | Exposer / consulter |
| Départ | Sommes-nous dignes ? | Partir / CTA manquant |
| Fiche créature | Qui est-elle ? | Actions contextuelles |

### Pattern rituel

```
1. Sujet(s)  2. Intention (muter / croiser / refuser)  3. Prix (organique, pureté)  4. Preview fiche  5. Confirmer
```

### Départ — piliers (pas 12 checks)

Collection · Espèces analysées · Diversité · Mémoire & preuves · Baie  

Un manquant = **un** CTA.

---

## 5. HUD décisionnel

**Afficher :** nom Institut · prochaine décision · stabilité berceau · équipe · drones parc · 3 monnaies · timers d’actes · lien Musée / collection  

**Ne plus afficher :** multi-caps · moral/santé séparés · spés · macarons harvest/build · ISMN 6 facteurs égaux  

---

## 6. Budgets clics cibles

| Acte | Clics |
|------|-------|
| Expé avec preset | ≤3 |
| Analyser (file) | ≤2 |
| Mutation / croisement (sujets choisis) | ≤4 |
| Identifier décision (hub) | <5 s |

---

## 7. Composants

- Carte créature = silhouette d’abord (`Nom — Espèce · Rareté · Capacités · Pureté`)  
- Bande de risque en **mots** + cause  
- Un CTA primaire  
- Confirm risque : créature / drone / berceau  
- Empty state = une décision  
- Blocked = raison + lien décision (pas « lab 10 »)  
- Toast narratif + lien fiche  

---

## 8. Signature de stabilité

| Critère | État |
|---------|------|
| D1–D5 | Oui |
| Arborescence 5 lieux + rituel + départ | Oui |
| HUD décisions | Oui |
| Aligné `01`/`03`/`07` | Oui |
| Pas de polish comme solution | Oui |

**Document 08 : stabilisé REBORN.**  
Dessins : Phase 6 roadmap.

---

*Fin de `08_UI_UX.md`.*
