# 15 — Backlog vertical slice

**Statut :** backlog opérationnel  
**But :** transformer `13` + `14` en flux écran, tâches et critères d’acceptation.  
**Ancrage :** `01` · `04`–`09` · `12` · `14` · prototype `src/app.php`

---

## 0. Objectif du slice

Un joueur peut en une session courte :

```
Arrivée → Recon drones → Analyser → Muter (prix) → Mission (risque) → Musée
```

et raconter :

> J’ai envoyé mes drones lire la zone, j’ai analysé une espèce, je l’ai fait muter, elle a tenu (ou blessée), et je l’ai exposée au musée.

---

## 1. Flux écran par écran

| # | Écran | Décision (D1–D5) | Action primaire | Sortie attendue |
|---|-------|------------------|-----------------|-----------------|
| 1 | **Institut** | Où commencer ? | Voir campus + parc drones + monnaies | Naviguer vers Monde |
| 2 | **Monde** | Où explorer ? | Lancer recon (Vol drones) | Zone connue · risque nommé · signal vivant · monnaies bougent |
| 3 | **Expédition** | Que analyser ? Que muter ? | Analyser puis muter (prix / pureté) | Espèce · stats · capacité · pureté |
| 4 | **Musée** | Qui envoyer ? Quoi exposer ? | Mission puis exposer | succès / blessure / épuisement · pièce musée · phrase GENESIS |
| — | *(tous)* | — | Dock GENESIS (1 phrase max) | Observation / hypothèse / seuil — jamais solution |

### Cartographie instruments (lisibles, non-empire)

| Instrument | Visible dès | Capacité débloquée |
|------------|-------------|--------------------|
| Bureau des expéditions | Arrivée | Composer / lancer recon |
| Laboratoire central | Post-recon | Analyser / muter |
| Vivarium | Toujours | Fiche créature, statut (prête / épuisée / blessée) |
| Musée | Post-exposition | Collection d’espèces remarquables |
| Baie de projection | Fin de boucle | **Verrouillée** (seuil, pas jeu) |

---

## 2. État du prototype (baseline → lot 1)

| Élément Design 14 | Baseline code | Lot 1 (cible) |
|-------------------|---------------|---------------|
| Boucle Design 07 | ✅ linéaire | Explorer → Analyser → Muter → Mission → Musée |
| Drones avant humains | CTA texte seulement | **État parc** (2 classes) + recon coûte logistique |
| 3 monnaies d’acte | ❌ | organique · prélèvements · logistique |
| 5 instruments | ❌ | panel campus (états) |
| GENESIS | ❌ | 1 phrase contextuelle |
| Risque de mission | toujours succès | **succès / épuisée / blessée** |
| Mutation | 1 bouton | prix (organique) + pureté ↓ + capacité |
| Bestiaire | 1 créature | 1 active + découverte d’espèces (lot 2) |
| Baie verrouillée | ❌ | mention verrouillée |

---

## 3. Backlog priorisé

### P0 — Lot 1 (immédiat)

| ID | Tâche | Critère d’acceptation |
|----|-------|------------------------|
| **P0.1** | État `drones` (2 classes : recon, prélèvement) | UI montre le parc ; recon impossible si logistique = 0 |
| **P0.2** | État `resources` (3 monnaies) | Recon consomme logistique ; analyse gagne prélèvements ; mutation consomme organique |
| **P0.3** | Campus 5 instruments (lecture) | Panel Institut liste les 5 avec statut (actif / prêt / verrouillé) |
| **P0.4** | GENESIS dock | Après chaque acte, 1 phrase (Observation / Hypothèse / Seuil), jamais d’ordre |
| **P0.5** | Mission à risque | succès / épuisée / blessée ; stats influencent les chances |
| **P0.6** | Tests PHP | Boucle complète + monnaies + blessure possible encore verte |

### P1 — Lot 2 (après P0) — **implémenté**

| ID | Tâche | Critère d’acceptation | État |
|----|-------|------------------------|------|
| **P1.1** | Choix de zone (3 zones) | Décision réelle avant recon | ✅ |
| **P1.2** | 3 espèces découvrables | Bestiaire multi-fiches (Thermidé, Salinide, Cryopode) | ✅ |
| **P1.3** | Choix de mutation | 2 options (stats vs capacité) avec prix / pureté | ✅ |
| **P1.4** | Croisement simple | 2 créatures analysées → hybride unique (parents informatifs) | ✅ |
| **P1.5** | Exposition musée solennelle | Pièce structurée + echo GENESIS | ✅ |

### P2 — Hors slice (ne pas faire maintenant)

- Marché, multi, PvP, combats de créatures  
- Multi-planètes, tech tree, harvest passif  
- HUD tableur, niveaux labo, trophées bâtiments  

---

## 4. Critères d’acceptation du vertical slice

Le slice est **accepté** si un testeur sans tutoriel long peut cocher :

| # | Critère (from `14` §4) | Preuve |
|---|------------------------|--------|
| 1 | J’ai compris quelque chose sur le monde | Zone + risque nommés après recon |
| 2 | J’ai dû utiliser des drones | Recon = action drone + coût logistique |
| 3 | Vraie décision sur une créature | Analyse + mutation avec coût / pureté |
| 4 | J’ai ressenti un risque | Épreuve non triviale (scar possible) |
| 5 | Trace de ce qui s’est passé | Inscription + mémoires |

**Qualité supérieure :** résumé en une phrase patrimoine (pas liste de bâtiments).

**Definition of Done (`14` §6) :**

- [ ] Compréhensible sans long tutoriel  
- [ ] Au moins une décision de risque  
- [ ] Progression = patrimoine, pas base  
- [ ] Envie de rejouer autrement  
- [ ] Pas de croissance de base comme win  

---

## 5. Règles de non-dérive (checklist dev)

Avant merge d’une feature prototype :

1. Relie-t-elle un verbe de `01` ?  
2. Passe-t-elle le double test `00` §4.2 (A multi et/ou B identité) ?  
3. Respecte-t-elle `12` (pas de stats face joueur, drones avant humains, pas de solution GENESIS) ?  
4. Est-elle dans P0/P1 de ce fichier ? Sinon → out.

---

## 6. Ordre d’implémentation code

```
P0.2 monnaies → P0.1 drones → P0.3 campus → P0.5 risque → P0.4 GENESIS → P0.6 tests
```

Puis P1 si le testeur raconte encore une « base » plutôt qu’une collection / un musée.

---

*Fin de `15_Backlog_Vertical_Slice.md`.*
