# 12 — Rules of GENESIS

**Statut :** canon REBORN — **lois non négociables**  
**Autorité :** au-dessus des implémentations  
**Lire après :** `00` → `01` → doc système concerné → `11` si multi/export → **ce fichier** → code  
**Créatures :** `07_Creatures.md` prime sur tout vocabulaire legacy.

Croisé avec : `00`–`11` · `14`–`15` · prototype `src/`

---

## A. Identité du jeu

1. **GENESIS est un jeu de patrimoine biologique**, pas un city-builder, pas un MMO de farm.  
2. **Le multijoueur est la seconde moitié du jeu** ; la Phase 1 est un **tutoriel vivant** pour un univers persistant.  
3. **La progression réelle est le patrimoine** (créatures, savoir, mémoire, collection musée) — pas bâtiments, stocks, niveau.  
4. **La Phase 2 n’ajoute pas de règles fondamentales : elle augmente uniquement l’échelle.**  
5. **Une mécanique doit préparer la suivante** (et en P1 : multi A et/ou identité B).

---

## B. Créatures (`07`)

6. **Une créature est un être de collection, pas une unit de score.**  
7. **Silhouette en 5 secondes :** `[Nom] — Espèce · Rareté · Capacités · Pureté`.  
8. **Capacités ≤ 2** (signatures).  
9. **Structure seule autorisée :** nom, espèce, rareté, Force · Vitesse · Résistance · Intelligence, capacités, pureté, statut.  
10. **Statut :** prête · épuisée · blessée.  
11. **Interdit face joueur :** XP/niveaux, gear score, modules/flags UI, arbres de lignée, Dons, dynasties.  
12. **Intérêt légitime :** découverte, analyse, mutation, croisement, mission, musée.  
13. **Parents d’hybride = informatifs seulement** — pas de transmission d’héritage.

---

## C. Boucle & décisions

14. **Boucle unique :** Rencontrer → Comprendre → Transformer → Éprouver → Inscrire.  
15. **Équivalent terrain :** Découvrir → Analyser → Muter → Mission → Musée.  
16. **L’UI est construite autour des décisions**, pas des systèmes.  
17. **Une information affichée doit aider à prendre une décision.**  
18. **Cinq décisions :** Où ? Qui analyser ? Que muter/croiser ? Qui risquer ? Quoi exposer / emporter ?  
19. **La découverte est toujours plus importante que la production.**

---

## D. Exploration & drones

20. **Les drones explorent avant les humains.**  
21. **L’exploration crée le besoin de construire, jamais l’inverse.**  
22. **Drones = instruments** (observer, cartographier, prélever, soutenir) — pas usine, pas combat.  
23. **Sans recon, le joueur joue à l’aveugle** — voulu s’il néglige les drones.

---

## E. Institut & économie

24. **Chaque bâtiment débloque une capacité scientifique ou d’exploration.** Sinon fusion / suppression.  
25. **Cinq instruments P1 :** Bureau · Labo · Vivarium · **Musée** · Baie.  
26. **Trois monnaies d’acte :** Organique · Prélèvements · Logistique.  
27. **États ≠ monnaies** (berceau, équipe, pureté/statut ne se farm pas).  
28. **Pas de harvest / prod passive comme core loop.**  
29. **Personnel d’Institut borné**, pas empire démographique.

---

## F. GENESIS (IA)

30. **Observe et interprète — ne donne jamais de solutions.**  
31. **Présence permanente, parole rare** ; silence légitime.  
32. **Interdit :** build order, spoiler de mutation/croisement, tips « max le QG ».

---

## G. Multi & export

33. **Voyage :** créatures, savoir, mémoire, collection, origine — pas la ville.  
34. **Phrase d’identité portable** P1 ↔ P2 (fiche créature + bestiaire).  
35. **Aucun profil multi supérieur** par design.  
36. **PvP existe, jamais gratuit** ; core loop reste scientifique.  
37. **Réputation > puissance brute.**  
38. **Comptoir = réseau scientifique**, pas AH de stats.  
39. **Double test P1 :** (A) multi · (B) identité. Non et non → repenser.

---

## H. Process REBORN

40. **Design précède le code.** Ordre : `00 → 01 → doc système → 11? → 12 → 14/15 → code`.  
41. **Le prototype est une boîte à outils**, pas une autorité.  
42. **Import sélectif :** ✅ · 🔧 · ❌.  
43. **Contradiction entre docs** → résoudre le jour même ; **`07` prime** sur créatures.  
44. **Feature contredit ces Rules** → la feature a tort.  
45. **Feature code** sur branches git `feature/*`, pas en direct sur `genesis-reborn` une fois le dev lancé.  
46. **Quatre règles qualité :** design avant code · flux avant UI · lien philo · prépare la suite + patrimoine.

---

## Checklist express (avant toute idée / PR)

```
[ ] Sert le patrimoine ou l’explorateur scientifique ?
[ ] Prépare la mécanique suivante / le multi (A/B) ?
[ ] Pas de city-builder / farm / lignée / gear score ?
[ ] Décision joueur claire ?
[ ] Créature = collection simple (07), pas unit ?
[ ] Drones avant humains si terrain inconnu ?
[ ] Bâtiment = capacité, pas cap vanity ?
[ ] Silhouette lisible si créature ?
[ ] Design lu (00, 01, 07, 12) ?
```

---

## Signature de stabilité (Bible complète)

| Doc | Statut |
|------|--------|
| 00 Philosophie | ✅ aligné `07` |
| 01 Core Gameplay | ✅ aligné `07` |
| 02 Audit | ✅ (historique V1) |
| 03 Phase 1 | ✅ aligné `07` |
| 04 Drones | ✅ |
| 05 Bâtiments | ✅ |
| 06 Ressources | ✅ |
| 07 Creatures | ✅ **source créatures** |
| 08 UI_UX | ✅ |
| 09 GENESIS_AI | ✅ |
| 10 Transition P2 | ✅ |
| 11 Multiplayer | ✅ |
| **12 Rules** | ✅ **stabilisé REBORN (aligné `07`)** |

**PHASE 1 — Bible du jeu : TERMINÉE.**

---

*Fin de `12_Rules_Of_Genesis.md` — la loi au-dessus du code.*
