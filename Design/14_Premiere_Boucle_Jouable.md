# 14 — Première boucle jouable

**Statut :** spécification prototype  
**Rôle :** définir la première version jouable de GENESIS, sans dérive de scope.  
**Ancrage canon :** `01_Core_Gameplay.md` · `03_Phase1.md` · `04_Drones.md` · `05_Batiments.md` · `06_Ressources.md` · `07_Creatures.md` · `08_UI_UX.md` · `09_GENESIS_AI.md` · `12_Rules_Of_Genesis.md` · `13_Roadmap_Production.md`

---

## 0. But de cette version

Cette première boucle doit prouver que GENESIS fonctionne avec peu d'éléments mais avec une identité forte.

Le joueur doit pouvoir :

- comprendre son monde natal ;
- utiliser des drones avant les humains ;
- découvrir et analyser une espèce ;
- tenter une mutation risquée ;
- envoyer la créature en mission, puis l’exposer au musée.

Si la version ne permet pas cela, elle n'est pas un prototype GENESIS.

---

## 1. Séquence jouable minimale

### Étape 1 — Arrivée

Le joueur apparaît sur un monde natal unique.

À ce moment, il voit :

- le nom du monde ;
- un premier état du berceau ;
- l'état de son Institut ;
- sa petite équipe de départ ;
- son parc de drones initial.

Décision attendue : où commencer à lire le monde.

### Étape 2 — Reconnaissance

Le joueur envoie un petit Vol de drones vers une zone inconnue.

Résultat attendu :

- carte locale ;
- niveau de risque ;
- un premier indice de vivant ;
- un premier besoin scientifique.

Décision attendue : faut-il pousser l'exploration ou revenir analyser ?

### Étape 3 — Analyser

Le joueur amène le spécimen au laboratoire.

Résultat attendu :

- espèce nommée ;
- rareté ;
- caractéristiques (Force, Vitesse, Résistance, Intelligence) ;
- une capacité spéciale ;
- pureté génétique ;
- mutations et missions débloquées.

Décision attendue : faut-il muter maintenant ou d’abord préparer une mission ?

### Étape 4 — Évoluer (mutation)

Le joueur tente une mutation sur la créature analysée.

Résultat attendu : stats renforcées et/ou nouvelle capacité, pureté en baisse, part de risque acceptée.

Décision attendue : accepter le prix organique et le risque génétique, ou reporter.

### Étape 5 — Mission

Le joueur envoie la créature en expédition.

Résultat attendu :

- succès ;
- épuisement ;
- blessure ;
- efficacité liée aux stats et au statut.

Décision attendue : est-ce que la créature tient sur le terrain ?

### Étape 6 — Musée

Le joueur expose ce qui mérite d’entrer dans la collection.

Résultat attendu :

- pièce de musée ;
- mémoire d’acte ;
- retour de GENESIS ;
- pas de lignée ni d’arbre généalogique.

Décision attendue : qu’est-ce qui mérite d’être conservé dans le bestiaire ?

---

## 2. Contenu minimal du prototype

### Monde

- 1 planète natale ;
- 2 à 3 biomes de départ ;
- une génération simple mais unique ;
- des zones lisibles ;
- un niveau de stabilité du berceau.

### Drones

- 2 classes seulement au départ ;
- reconnaissance ;
- prélèvement ;
- retour de données simple.

### Créatures (voir `07_Creatures.md`)

- 1 spécimen de départ (bestiaire minimal) ;
- structure : nom, espèce, rareté, 4 stats, 1–2 capacités, pureté ;
- analyse labo obligatoire avant mutation / mission ;
- mutation risquée (pureté) ;
- musée = collection, pas généalogie.

### Institut

- Bureau des expéditions ;
- Laboratoire central ;
- Vivarium ;
- Musée ;
- Baie de projection verrouillée au départ.

### Ressources

- matière organique ;
- prélèvements ;
- logistique.

### IA

- une phrase de lecture du monde ;
- une phrase de tension ;
- une phrase de seuil.

---

## 3. Ce que la première version ne doit pas faire

- pas de marché ;
- pas de multi ;
- pas de combats de créatures ;
- pas de plusieurs planètes ;
- pas de build empire ;
- pas de farming passif ;
- pas de dizaines de ressources ;
- pas de HUD encombré ;
- pas de niveau de laboratoire infini ;
- pas de progression par bâtiments-trophées.

---

## 4. Critères de réussite

Cette première boucle est bonne si un joueur testeur peut dire :

1. j'ai compris quelque chose sur le monde ;
2. j'ai dû utiliser des drones ;
3. j'ai analysé une espèce et tenté une mutation ;
4. j'ai ressenti un risque en mission ;
5. j'ai exposé une créature au musée.

### Critère de qualité supérieur

Le testeur doit pouvoir résumer sa session en une phrase du type :

> J'ai envoyé mes drones lire la zone, j'ai analysé une espèce, je l'ai fait muter, elle a tenu en mission, et je l'ai exposée au musée.

Si la phrase ressemble à une liste de travaux de base, le prototype est raté.

---

## 5. Pipeline conseillé pour l'équipe

1. Générer le monde natal.
2. Afficher l'état du berceau.
3. Lancer une reconnaissance drone.
4. Lire une fiche créature.
5. Appliquer une mutation simple.
6. Lancer une mission.
7. Exposer au musée.

---

## 6. Définition de terminé

La première boucle jouable est terminée quand :

- elle est compréhensible sans tutoriel long ;
- elle oblige à prendre au moins une décision de risque ;
- elle montre que le patrimoine est la vraie progression ;
- elle donne envie de recommencer avec une autre approche ;
- elle ne repose pas sur la croissance de la base.
