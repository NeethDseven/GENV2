# 13 — Roadmap de production GENESIS

**Statut :** document de démarrage opérationnel  
**But :** transformer la bible REBORN en plan de production concret, du premier prototype jouable jusqu'à la boucle Phase 1 stabilisée.  
**Ancrage canon :** `00_Philosophie.md` · `01_Core_Gameplay.md` · `03_Phase1.md` · `04_Drones.md` · `05_Batiments.md` · `06_Ressources.md` · `07_Creatures.md` · `08_UI_UX.md` · `09_GENESIS_AI.md` · `10_Transition_Phase2.md` · `11_Multiplayer_Philosophy.md` · `12_Rules_Of_Genesis.md`

---

## 0. Objectif de cette roadmap

Cette roadmap existe pour éviter deux pièges :

- construire trop de systèmes avant d'avoir une boucle fun ;
- réintroduire, par habitude, des réflexes de city-builder.

Le but du projet n'est pas de produire un gros dashboard. Le but est de faire tenir une expérience jouable autour d'une idée forte :

> Le joueur explore le vivant, l'analyse, le mute, l'éprouve en mission et l'expose au musée.

---

## 1. Ordre de construction recommandé

### 1.1 Ce qu'il faut faire d'abord

1. Le noyau de boucle : `Découvrir → Analyser → Muter → Mission → Musée` (verbes `01` équivalents).
2. Le monde natal : génération, lecture de zone, biomes, danger lisible.
3. Les créatures (`07`) : espèce, rareté, 4 stats, capacités, pureté, statut — **pas de lignée**.
4. Les drones : reconnaissance, cartographie, prélèvement.
5. L'Institut : 5 instruments maximum (dont Musée), pas de hub empire.
6. Les ressources d'acte : organique, prélèvements, logistique.
7. La première fin de Phase 1 : baie de projection et MVE.

### 1.2 Ce qu'il faut éviter au départ

- le marché galactique complet ;
- le PvP complet ;
- les classements ;
- les combats de créatures ;
- la monétisation ;
- la décoration de colonie ;
- les arbres de tech à rallonge ;
- les centaines de filtres et d'états visibles.

---

## 2. Découpage en livrables

### Phase 0 — Fondations de design

But : figer ce qu'est le jeu avant de produire du contenu.

Livrables :

- boucle principale verrouillée ;
- vocabulaire officiel ;
- périmètre Phase 1 ;
- liste des systèmes autorisés / interdits ;
- définition du MVE.

Critère de sortie : toute mécanique nouvelle peut être reliée à au moins un verbe de boucle et à une décision joueur claire.

### Phase 1 — Vertical slice jouable

But : obtenir une version courte mais complète du cœur du jeu.

Contenu minimum :

- un monde natal procédural simple ;
- une carte de berceau lisible ;
- une expédition basique ;
- 1 à 2 drones opérationnels ;
- 1 à 3 créatures / signaux de départ ;
- une fiche créature lisible (`07`) ;
- une action d'analyse ;
- une action de mutation ;
- une action de mission ;
- une première exposition au musée.

Critère de sortie : un joueur peut faire une boucle complète et raconter ce qu'il a créé.

### Phase 2 — Phase 1 stabilisée

But : enrichir sans changer la grammaire.

Contenu :

- plus de biomes ;
- plus d'espèces ;
- plus de décisions de risque ;
- meilleure lecture de l'état du patrimoine / pureté / statut ;
- premiers seuils d'export / projection ;
- meilleure IA d'observation.

Critère de sortie : la boucle reste la même, mais elle est plus profonde, plus lisible et plus solennelle.

### Phase 3 — Pont vers le multi

But : préparer la seconde moitié du jeu.

Contenu :

- réputation d'Institut / collection ;
- lecture sociale des bestiaires ;
- préfiguration du comptoir galactique ;
- contrats scientifiques ;
- premières interactions limitées entre joueurs.

Critère de sortie : ce que le joueur a appris en Phase 1 devient utile devant d'autres joueurs.

---

## 3. MVP conseillé

Le MVP ne doit pas chercher à couvrir tout GENESIS. Il doit juste valider que la promesse est réelle.

### MVP = une boucle complète sur un seul berceau

1. Le joueur arrive sur un monde natal unique.
2. Il envoie une reconnaissance avec drones.
3. Il comprend une zone ou un vivant.
4. Il mute une créature (ou croise, plus tard).
5. Il l'éprouve en mission.
6. Il expose le résultat au musée (mémoire de collection).

### Ce que le MVP doit prouver

- que l'exploration crée une vraie décision ;
- que les créatures sont intéressantes comme êtres vivants ;
- que les drones changent la manière de jouer ;
- que la progression vient du patrimoine, pas du bâtiment ;
- que le joueur peut raconter sa session en une phrase.

---

## 4. Priorités système

### Priorité 1 — Jouabilité

- boucle principale ;
- génération du monde ;
- expédition ;
- créatures ;
- analyse ;
- mutation ;
- mission + musée.

### Priorité 2 — Lecture et décision

- UI décisionnelle ;
- fiche créature ;
- carte de risque ;
- état du patrimoine ;
- aide contextuelle de GENESIS.

### Priorité 3 — Systèmes d'extension

- bâtiments instrumentaux ;
- ressources d'acte ;
- progression de l'Institut ;
- seuil de projection.

### Priorité 4 — Continuité multi

- collections lisibles socialement ;
- réputation ;
- contrats ;
- patrimonisation exportable.

---

## 5. Règles de production

1. Chaque système doit améliorer une décision.
2. Chaque ajout doit renforcer le patrimoine biologique.
3. Chaque écran doit servir un acte, pas exposer un sous-système.
4. Chaque créature doit rester un être, jamais un chiffre.
5. Chaque bâtiment doit débloquer une capacité scientifique ou d'exploration.
6. Chaque feature doit avoir une utilité Phase 2 ou une identité forte.
7. Si une feature ressemble à du city-builder générique, elle est suspecte.

---

## 6. Risques principaux

### Risque 1 — Trop de systèmes trop tôt

Symptôme : le projet accumule des menus, des caps et des tableaux avant d'être fun.

Réponse : couper sans nostalgie et valider la boucle avant d'ajouter du contenu.

### Risque 2 — Retour du city-builder

Symptôme : le joueur commence à optimiser une base au lieu de vivre une aventure biologique.

Réponse : tout ce qui ne sert pas la boucle ou le patrimoine sort du centre.

### Risque 3 — Créatures réduites à des stats

Symptôme : l'attachement n'existe plus, seul le min-max reste.

Réponse : garder la silhouette (`07`), la collection, l'histoire et la décision, cacher le moteur abstrait.

### Risque 4 — Multi prématuré

Symptôme : on ajoute des fonctions sociales avant que le solo soit lisible.

Réponse : ne construire le social qu'après une Phase 1 claire et mémorable.

---

## 7. Prochaines étapes immédiates

1. Définir le premier flux jouable, écran par écran.
2. Transformer le MVP en backlog de production.
3. Découper les systèmes Phase 1 en tâches courtes.
4. Écrire les critères d'acceptation du vertical slice.

---

## 8. Résultat attendu

À la fin de cette roadmap, GENESIS doit être capable de démontrer trois choses :

- le jeu a une identité unique ;
- la boucle est réellement amusante ;
- le patrimoine biologique est plus fort que la base.

Si ces trois points ne tiennent pas, il faut corriger le design avant d'accélérer la production.
