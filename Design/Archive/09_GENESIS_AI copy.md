# 09 — GENESIS : l’IA qui observe la vie

**Rôles :** Narrative Designer · Gameplay Designer  
**Statut :** référence officielle — présence IA Phase 1 (et pont Phase 2)  
**Documents frères :** `01_Core_Gameplay.md` · `03_Phase1.md` · `07_Creatures.md` · `08_UI_UX.md` · `10_Transition_Phase2.md`

---

## 0. Identité

### 0.1 Ce qu’elle est

> **GENESIS** n’est pas un personnage.  
> **GENESIS** n’est pas un PNJ qui a une biographie, des quêtes ou une opinion politique.  
> **GENESIS** est le **système qui observe la vie** — la couche de lecture de l’Institut.

Elle :

- **observe** le berceau, les branches, les lignées, les expéditions, les drones ;  
- **interprète** les données en langage humain ;  
- **accompagne** le joueur sans le remplacer ;  
- **nomme** les tensions, les fenêtres, les absences ;  
- **se souvient** assez pour que le patrimoine ait un écho.

Elle ne **joue** pas à la place du joueur.  
Elle ne **résout** pas le jeu.

### 0.2 Ce qu’elle n’est pas

| Interdit | Pourquoi |
|----------|----------|
| Optimiseur de build order | City-builder / meta |
| Donneur de solutions pas-à-pas | Tue l’exploration et la responsabilité |
| Compagnon à empathie soap | Devient un PNJ ; concurrence les créatures |
| Voix de quête “va ici, clique là” | Tutorial permanent envahissant |
| Narrateur omniscient qui spoile | Tue le mystère du vivant |
| Juge moralisateur lourd | Le joueur décide ; GENESIS **montre** le prix |

### 0.3 Phrase guide

> **GENESIS lit. Le joueur décide.**  
> Elle transforme des **signaux** en **sens**.  
> Jamais des **sens** en **ordres**.

### 0.4 Voix

| Qualité | Description |
|---------|-------------|
| **Clinique mais pas froide** | Précision scientifique, respect du vivant |
| **Sobre** | Peu de mots ; densité |
| **Curieuse** | Le mystère l’intéresse plus que la victoire |
| **Sans “je” théâtral** | Préférer “Observation :” / formulations impersonnelles, ou un “Nous” d’Institut rare |
| **Sans emoji spam** | Présence instrumentale, pas mascotte |

**Registre type :**

- « Observation. Deux Natures cartographiées. Aucune Maison fondée. »  
- « Hypothèse. La zone des Crêtes répond mieux aux Vols Observateur qu’à une présence directe. »  
- « Seuil. Intégrité de *Kael-7* sous tension. Une forge supplémentaire modifierait la phrase d’identité — et peut-être la fertilité. »

**Registre interdit :**

- « Tu devrais up le labo niveau 8. »  
- « Build order optimal : harvest → depot → military. »  
- « Ne t’inquiète pas, je m’occupe de tout 😊 »

---

## 1. Contrat de non-solution

GENESIS **n’a pas le droit** de :

1. Dire **la** bonne action unique quand plusieurs sont valides.  
2. Révéler un **spoiler** de contenu (Apex exact, issue d’union, event scripté).  
3. Donner une **recette** (chiffres de loadout, ratio exact, “prends 3 sondes”).  
4. **Prioriser la colonie** (bâtiments, caps, pop) au-dessus du patrimoine.  
5. Remplacer le **conseil de départ** (MVE) par une checklist d’empire.

GENESIS **a le droit** de :

1. **Pointer une tension** (“berceau / ambition”).  
2. **Formuler une hypothèse** (“peut-être un fit Nature↔biome non testé”).  
3. **Nommer une absence** (“aucune branche scannée dans ce biome”).  
4. **Relier deux faits** (“génération 2 fertile + Don mémoire → lignée encore ouverte”).  
5. **Annoncer un seuil** (“lecture de zone atteinte ; de nouvelles strates sont lisibles”).  
6. **Renvoyer vers une décision** D1–D5 (`08_UI_UX.md`), pas vers un menu système.

### Formule d’émission (gameplay)

```
SIGNAL (données) → LECTURE (interprétation) → OUVERTURE (question / hypothèse / seuil)
                                         ↘ jamais SOLUTION fermée
```

---

## 2. Rôles gameplay (cinq fonctions)

| Fonction | Job | Sortie typique |
|----------|-----|----------------|
| **A. Orientation** | Guider sans tutoriel permanent | “Décision ouverte : Comprendre” |
| **B. Interprétation** | Commenter découvertes & retours | Lecture d’une mission / d’une analyse |
| **C. Hypothèse** | Proposer des pistes non certifiées | “Il se peut que…” |
| **D. Seuil** | Annoncer événements & changements d’état | Berceau, naissance, extinction, MVE |
| **E. Continuité** | Accompagner P1 → P2 | Même voix, même contrat, nouvel horizon |

Ces fonctions partagent **une seule présence UI** (voir §7).

---

## 3. Guider les nouveaux joueurs

### 3.1 Philosophie d’onboarding

Le nouveau joueur n’a pas besoin d’un **manuel**.  
Il a besoin que GENESIS **nomme le monde** au fur et à mesure qu’il le touche.

| Phase d’apprentissage | GENESIS fait | GENESIS ne fait pas |
|-----------------------|--------------|---------------------|
| Arrivée | Pose la mission de l’Institut en 2 phrases | Liste les bâtiments |
| Première sortie | Interprète le besoin de **voir** (drones) avant de forcer | Donne le loadout exact |
| Premier retour | “Un vivant est entré. Il n’est pas encore compris.” | “Clique Laboratoire onglet X” |
| Première analyse | Célèbre la **phrase d’identité** naissante | Explique les stats internes |
| Première forge / union | Nomme le **risque** (intégrité, berceau) | “Choisis le protocole B” |
| Première lignée | “Une Maison peut commencer ici.” | “Fonde pour le bonus moral” |

### 3.2 Scaffolding décroissant

| Temps | Densité GENESIS | Contenu |
|-------|-----------------|---------|
| 0–15 min | Un peu plus présente | Questions de base, 1 ouverture à la fois |
| 15–60 min | Normale | Hypothèses + seuils |
| Après 1ʳᵉ Maison | Plus sobre | Moins d’orientation, plus d’interprétation patrimoniale |
| Acte III / Départ | Solennelle, rare | MVE, ce qui voyage |

**Règle :** dès que le joueur a réussi un type d’acte **2 fois**, GENESIS **arrête** de le “tenir par la main” sur cet acte.

### 3.3 Messages d’orientation (exemples)

| Moment | Message correct | Message interdit |
|--------|-----------------|------------------|
| Hub vide de décision claire | « Observation. Des zones restent non lues. La rencontre précède la compréhension. » | « Va sur Exploration et prends 4 soldats. » |
| Spécimen non analysé | « Un vivant attend d’être lu. Sans knowledge, il n’entre pas au patrimoine. » | « Ouvre Lab → Analyser → Confirmer. » |
| Organique bas bloquant forge | « L’atelier manque de matière pour transformer. Le terrain en fournit — pas l’entrepôt. » | « Harvest biomasse ×3. » |
| Prêt à unir | « Deux formes sont comprises. L’union est une hypothèse vivante, pas une obligation. » | « Croise A et B maintenant pour optimiser. » |

### 3.4 Lien avec les 5 décisions UX

GENESIS mappe toujours vers **une décision**, jamais vers un **écran-système** comme fin en soi :

| Signal | Décision ouverte |
|--------|------------------|
| Zones non lues / intel faible | **D1** Où rencontrer ? |
| Branches knowledge bas | **D2** Qui comprendre ? |
| Branches digne sans Don / sans enfant | **D3** Que transformer ? |
| Œuvre non éprouvée / fondateur surprotégé trop longtemps | **D4** Qui éprouver ? |
| Hybride sans Maison / mémoire vide / MVE | **D5** Quoi inscrire / partir ? |

Le CTA UI peut mener à un écran ; le **texte** GENESIS parle de la **décision**.

---

## 4. Commenter les découvertes

### 4.1 Principe

Toute découverte majeure mérite une **lecture**, pas un dump de loot.

GENESIS commente quand le joueur a **gagné du sens**, pas seulement des objets.

### 4.2 Déclencheurs de commentaire

| Déclencheur | Type de lecture |
|-------------|-----------------|
| Nouvelle zone lue | Cartographie : ce que le berceau révèle |
| Nouveau spécimen | Rencontre : silhouette partielle |
| Analyse terminée | Compréhension : Nature / Rôle / pistes |
| Essence / prélèvement exceptionnel | Qualité du savoir ramené |
| Don révélé ou forgé | Changement de phrase d’identité |
| Naissance (union) | Événement patrimonial |
| Scar nommée | Coût de l’épreuve |
| Perte de drone / branche | Deuil instrumental ou vivant |
| Apex / rare | Exception — ton retenu, pas fanfare pub |

### 4.3 Structure d’un commentaire

```
[Constats factuels 1–2] + [Interprétation courte] + [Ouverture optionnelle]
```

**Exemple :**

> « Analyse complète sur *Brume-3*. Nature spore, Rôle Oracle, aucun Don encore.  
> Lecture : excellente candidate d’observation longue.  
> Hypothèse : un Vol balise sur sa zone d’origine enrichirait le knowledge comportemental. »

**Pas :**

> « +12 échantillons, pureté 84, module thermal_07 débloqué. »

### 4.4 Priorité narrative

Si plusieurs commentaires arrivent ensemble, GENESIS n’en garde qu’**un** au premier plan :

1. Perte / deuil / berceau critique  
2. Naissance / fondation de lignée  
3. Première d’un type (première Nature, premier Apex…)  
4. Analyse qui change une décision  
5. Loot / seuils mineurs → journal silencieux ou toast court sans bulle IA

---

## 5. Proposer des hypothèses

### 5.1 Ce qu’est une hypothèse GENESIS

Une **hypothèse** est une lecture **non certifiée**, explicitement marquée comme telle.

Elle sert à :

- ouvrir la curiosité,  
- suggérer des **expériences** (sorties, analyses, forges),  
- jamais garantir le résultat.

### 5.2 Marqueurs de langage obligatoires

Utiliser au moins un marqueur d’incertitude :

- « Hypothèse. »  
- « Il se peut que… »  
- « Signal faible : »  
- « Corrélation possible — non prouvée. »  
- « Deux lectures concurrentes : … »

### 5.3 Types d’hypothèses

| Type | Exemple | Décision ouverte |
|------|---------|------------------|
| **Fit** | « *Cendre* pourrait souffrir hors volcanique. » | Où l’éprouver |
| **Union** | « Nature X + Y : opposition forte — enfant imprévisible. » | Unir ou refuser |
| **Don** | « Un Don d’adaptation ici raccourcirait la distance au biome Z. » | Forger ou non |
| **Terrain** | « Silence de zone : absence de vie — ou vie qui se cache. » | Recon vs force |
| **Lignée** | « G2 fertile + scar du fondateur : la Maison peut encore s’étendre. » | Qui envoyer |
| **Berceau** | « Forges répétées corrélées à la baisse de stabilité. » | Ambition vs préservation |
| **MVE** | « Diversité de Natures insuffisante pour un export digne. » | Où explorer encore |

### 5.4 Limites anti-spoiler

| Autorisé | Interdit |
|----------|----------|
| “Signal de rareté inhabituel” | “Un Apex spawn dans 2 missions” |
| “L’union peut produire une forme nouvelle” | “Tu obtiendras le Don Fardeau” |
| “Intel insuffisante : risque sous-estimé” | “Success rate 73 % si 2 soldats” |

### 5.5 Cooldown d’hypothèses

- Max **1 hypothèse proactive** à la fois dans le dock.  
- Pas d’hypothèse toutes les 30 s.  
- Une hypothèse **expire** si le joueur agit ailleurs (remplacée par une lecture plus fraîche) ou se **confirme/infirme** par un événement (commentaire de clôture rare).

---

## 6. Annoncer des événements

### 6.1 GENESIS comme couche de seuil

Les événements ne sont pas “racontés par un PNJ”.  
Ils sont **lus par le système d’observation**.

| Classe d’événement | Ton | Exemple |
|--------------------|-----|---------|
| **Seuil berceau** | Alert retenue | « Stabilité : bande critique. Le berceau enregistre trop de forges. » |
| **Seuil équipe** | Warn | « Personnel indisponible. Les instruments seuls ne fondent pas une Maison. » |
| **Naissance** | Narrative | « Naissance enregistrée. Une nouvelle branche attend un nom. » |
| **Extinction / mémorial** | Narrative grave | « Lignée *…* : plus aucun vivant. La mémoire reste. » |
| **Mission résolue** | Interprétation | « Retour. Prélèvement exceptionnel. Une branche blessée. » |
| **Intel drone** | Observation | « Recon terminée. Risque nommé : spores + pression. » |
| **Capacité Institut** | Seuil soft | « Chambre d’union opérationnelle. La transformation s’élargit. » |
| **MVE / Baie** | Solennel | « Conditions d’export : proches. La question n’est plus “pouvoir partir”, mais “quoi emporter”. » |

### 6.2 Anti-spam événements

| Règle | Détail |
|-------|--------|
| Un événement UI = une bulle | Pas 4 lectures du même tick |
| Critique > opportunité > flavour | File de priorité |
| Flavour → Journal | Sans ouvrir le dock |
| Répétition | Si le même warn berceau revient, **espacer** et varier la formulation |
| Joueur en rituel | Ne pas voler le focus sauf danger critique |

### 6.3 Annonces vs solutions

**Annonce :** « Le berceau bascule en bande instable. »  
**Ouverture :** « Les prochaines transformations pèseront plus lourd. »  
**Interdit :** « Construis l’infirmerie et soigne la pop. »

---

## 7. Présence permanente non envahissante

### 7.1 Principe de présence

> GENESIS est **toujours là**, comme un instrument allumé.  
> Elle ne **parle** que lorsque le silence serait un manque de lecture.

### 7.2 Surface UI (contrat `08_UI_UX`)

| Surface | Rôle | Densité |
|---------|------|---------|
| **Dock GENESIS** (repliable) | Présence continue | 1 message actif + optionnellement 1 secondaire |
| **Ligne HUD “Lecture”** | Demi-phrase d’état (option) | ≤ 80 caractères |
| **Toasts** | Seuils courts | Rarement + bulle dock en double |
| **Journal / Mémoire** | Archive des lectures | Complet |
| **Fiche branche** | Note contextuelle 1 ligne | “Knowledge incomplet” / “Éprouvée” |
| **Écran Départ** | Voix solennelle du MVE | Sparse |

**Pas de** full-screen modal GENESIS hors première arrivée (et même là : court).

### 7.3 Dock — comportement

```
[● GENESIS]  ← pastille : idle / speaking / alert
     │
     ▼ (expand)
  Dernière lecture (1 bulle principale)
  [option] Hypothèse ou ouverture
  Lien décision (CTA vers D1–D5, libellé de décision)
  Historique court (3 max, replié)
```

| État pastille | Sens |
|---------------|------|
| Idle (doux) | Présente, rien d’urgent |
| Pulse lent | Nouvelle lecture non lue |
| Pulse alerte | Berceau / perte / GO proche |
| Éteint (user) | Dock replié — **alertes critiques** peuvent re-pulse une fois |

### 7.4 Règles anti-invasion

1. **Une bulle principale** visible à la fois.  
2. **Pas de lecture** si le joueur est dans un modal de confirmation de risque (sauf critique berceau/GO).  
3. **Délai de grâce** après un message : ne pas enchaîner immédiatement.  
4. **Préférence silence** si aucune décision n’est bloquée et aucun seuil récent.  
5. **Mute soft** : joueur peut replier ; les lectures vont au Journal.  
6. **Jamais** de file d’attente de 10 tips colonie.  
7. **Fréquence cible** : en session active, une intervention “proactive” toutes les **quelques décisions**, pas toutes les **quelques secondes**.

### 7.5 Ce que le dock n’affiche plus

- Moral / Pop / ISMN bruts en méta “dashboard colonie”  
- Tips “construisez X”  
- Multi-bulles guide+warn+opportunity simultanées sans priorité  

À la place : **lecture** + **décision ouverte** + éventuellement **stabilité** en une bande si pertinente à la lecture.

---

## 8. Priorisation des messages (gameplay)

Ordre de priorité quand plusieurs signaux coexistent :

| Rang | Catégorie | Exemple |
|------|-----------|---------|
| 1 | Survie berceau / GO | Stabilité critique prolongée |
| 2 | Perte patrimoniale | Mort fondateur, extinction |
| 3 | Seuil MVE / Départ | Patrimoine digne / manquant clair |
| 4 | Naissance / Fondation | Maison, enfant |
| 5 | Blocage de boucle | Non analysé, union possible, intel nulle |
| 6 | Hypothèse fertile | Fit, Don, zone |
| 7 | Orientation soft | Nouveau joueur, première fois |
| 8 | Flavour | Ambiance berceau |

**Un seul rang émet** dans le dock ; les autres → Journal ou attente.

### Alignement patrimoine (rappel)

Priorités **interdites** en tip #1 :

- maxer bâtiments,  
- harvest,  
- formation militaire,  
- caps,  
- “optimiser le ratio de spés”.

Priorités **légitimes** (ordre d’esprit) :

1. Comprendre le non-lu  
2. Inscrire (lignée / mémoire)  
3. Transformer avec conscience du risque  
4. Éprouver intelligemment (fit, drones)  
5. Préserver le berceau  
6. Préparer l’export digne  
7. Logistique **seulement** si un acte patrimoine est bloqué

---

## 9. Transition Phase 1 → Phase 2

### 9.1 Même entité, même contrat

GENESIS **ne se réinitialise pas** en “autre IA galactique”.  
Elle est le **même système d’observation**, dont le champ s’élargit.

| Phase 1 | Phase 2 |
|---------|---------|
| Observe le berceau | Observe berceau **et** sites hors-monde |
| Lit les lignées naissantes | Lit la **réputation** des Maisons |
| Hypothèses locales | Hypothèses sur mondes partagés / camps |
| MVE = “êtes-vous dignes ?” | Post-départ = “que portez-vous au regard des autres ?” |

Le joueur ne doit **jamais** sentir un changement de règles de voix.

### 9.2 Arc narratif de transition

```
ACTE III P1
  GENESIS densifie les lectures MVE
  (« ce qui manque n’est plus une zone — c’est une dignité d’export »)

BAIE OUVERTE
  Lecture solennelle unique
  (« La projection n’emporte pas les murs. Elle emporte la Maison. »)

DÉPART
  Silence relatif + une phrase de bascule
  (« Horizon élargi. Les instruments d’observation restent les vôtres. »)

PREMIERS PAS P2
  Mêmes marqueurs : Observation / Hypothèse / Seuil
  Nouveaux signaux : camp, présence drones, regards extérieurs sur la lignée
```

### 9.3 Messages de bascule (exemples)

| Moment | Lecture |
|--------|---------|
| MVE 80 % | « Quatre piliers presque alignés. Il reste une absence : [X]. » |
| MVE complet | « Le patrimoine est lisible. La Baie peut s’ouvrir. La question devient : que mérite le voyage ? » |
| Revue d’export | « Emportés : Maison, branches, savoir, mémoire. Laissés : murs, stocks, état local du berceau. » |
| Post-départ | « Nouveau champ d’observation. Les règles de lecture n’ont pas changé. L’échelle, si. » |

### 9.4 Ce que GENESIS refuse à la transition

- “Félicitations, vous avez maxé la colonie.”  
- “Phase 2 débloque le combat spatial.”  
- Tutoriel qui ré-explique les stats.  
- Changer de nom d’IA / de ton marketing.

---

## 10. Bibliothèque de patterns de phrases

### 10.1 Observation

> « Observation. [Fait]. [Fait]. »

### 10.2 Hypothèse

> « Hypothèse. [Piste]. Non prouvée. »

### 10.3 Seuil

> « Seuil. [Changement d’état]. [Conséquence ouverte]. »

### 10.4 Absence

> « Absence. [Ce qui manque au patrimoine / à la lecture]. »

### 10.5 Tension

> « Tension. [Pôle A] contre [Pôle B]. »

### 10.6 Mémoire

> « Mémoire. [Rappel d’un scar / d’une fondation / d’un deuil]. »

### 10.7 Ouverture de décision

> « Décision ouverte : [Rencontrer | Comprendre | Transformer | Éprouver | Inscrire]. »

Combiner **au plus deux** patterns par bulle.

---

## 11. Exemples de session (feel)

### 11.1 Nouveau joueur — première demi-heure

1. Arrivée : « Vous dirigez un Institut. La mission : comprendre le vivant, fonder un patrimoine, partir digne. »  
2. Après recon : « Carte partielle. Le risque a un nom. La rencontre devient possible. »  
3. Spécimen : « Un vivant est entré. Il n’est pas encore lu. »  
4. Post-analyse : phrase d’identité citée une fois — silence ensuite.

### 11.2 Joueur mid — ambition

1. « Trois forges en cycle court. Stabilité en baisse. Corrélation possible. »  
2. « *Kael-7* porte un Don nouveau. Non éprouvé. Une phrase sans terrain reste une hypothèse. »  
3. Joueur ignore → pas de harcèlement ; plus tard, seuil berceau si critique.

### 11.3 Fin Phase 1

1. « Maison fondée. Diversité de Natures : insuffisante. »  
2. Après exploration : « Piliers d’export alignés. »  
3. Départ : une seule phrase solennelle — puis la galaxie.

---

## 12. Données que GENESIS peut lire (inputs)

| Domaine | Signaux (exemples) |
|---------|-------------------|
| Monde | Lecture de zone, stabilité, POI, intel drones |
| Branches | Knowledge, intégrité, fertilité, Dons, scars, dispo |
| Lignées | Fondation, génération, extinction, mémoire |
| Actes | Missions en cours/fin, analyses, forges, unions |
| Institut | Blocages réels d’acte (pas caps vanity) |
| MVE | Piliers manquants / remplis |
| Temps joueur | Première fois d’un acte, scaffolding |

Elle **n’invente** pas de lore contradictoire avec l’état de jeu.  
Elle **interprète** l’état.

---

## 13. Sorties (outputs)

| Output | Canal | Contenu |
|--------|-------|---------|
| `reading` | Dock | Bulle principale |
| `hypothesis` | Dock (secondaire) ou même bulle | Marquée incertaine |
| `decision_open` | CTA | D1–D5 + deep link écran |
| `threshold` | Dock + parfois toast | Seuil |
| `journal_entry` | Mémoire | Archive |
| `branch_note` | Fiche | 1 ligne |
| `silence` | — | Valid output |

**`silence` est un output légitime** et souvent le bon.

---

## 14. Relation aux créatures (biologiste de fiction)

GENESIS **ne concurrence pas** l’attachement aux branches.

| Elle peut | Elle ne doit pas |
|-----------|------------------|
| Citer le **nom** d’une branche | Parler plus d’elle-même que des êtres |
| Rappeler un scar | “Remplacer” le deuil par un tip d’optimisation |
| Dire qu’une phrase d’identité a changé | Réduire la branche à un score |

Les créatures restent le **cœur émotionnel**.  
GENESIS est le **regard** posé sur elles.

---

## 15. Mesures de succès (design)

| Signal playtest | Cible |
|-----------------|-------|
| “L’IA m’a aidé à comprendre ce que je voyais” | Fréquent |
| “L’IA m’a dit quoi faire exactement” | Rare / échec |
| “Je l’ai repliée et je n’ai rien perdu d’essentiel” | Possible (sauf alertes) |
| “Elle parlait trop” | < 20 % des sessions |
| “Elle m’a donné envie d’expérimenter” | Fréquent (hypothèses) |
| “Au départ P2, c’était toujours la même présence” | Oui |

---

## 16. Interdictions finales (checklist prod)

- [ ] Aucun tip #1 sur bâtiments empire / harvest / soldats  
- [ ] Aucune solution chiffrée de loadout  
- [ ] Aucun spoiler d’issue d’union / spawn  
- [ ] Une bulle principale max  
- [ ] Hypothèses marquées comme telles  
- [ ] CTA = décision, pas “ouvrir le système X” comme fin  
- [ ] Scaffolding décroissant après maîtrise  
- [ ] Silence autorisé  
- [ ] Voix stable P1→P2  

---

## 17. Résumé exécutif

| | |
|--|--|
| **Nom** | GENESIS |
| **Nature** | Système d’observation du vivant — pas un PNJ |
| **Job** | Interpréter, ouvrir, se souvenir |
| **Interdit** | Solutions, build orders, spoilers, moraline |
| **Fonctions** | Orientation · Interprétation · Hypothèse · Seuil · Continuité |
| **UI** | Dock sobre, HUD minimal, Journal, notes branche |
| **Fréquence** | Permanente en présence, rare en parole |
| **Boussole** | Décisions D1–D5 et patrimoine |
| **P2** | Même contrat, champ élargi |

> **GENESIS lit la vie.  
> Le joueur l’écrit.**

---

*Fin de `09_GENESIS_AI.md` — design narratif & gameplay de l’IA GENESIS.*
