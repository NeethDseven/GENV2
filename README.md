# GENESIS

**Jeu de découverte et de création du vivant.**

Explorez des zones, découvrez des espèces, **croisez-les** pour créer des hybrides inédits.  
Le musée conserve la mémoire — il n’est pas l’objectif.

## Lancer

```
http://localhost/projet/GenV2/public/
```

## Tests

```bash
c:\xampp\php\php.exe tests/game_loop_test.php
```

## Structure

```
src/
  bootstrap.php
  Game/
    Data/Catalog.php      # espèces + zones
    CreatureFactory.php
    StateFactory.php
    GameQueries.php
    IntentResolver.php    # CTA : croisement d’abord, mutation secondaire
    GameEngine.php        # actions + session
public/
  index.php
  assets/styles.css
tests/
  game_loop_test.php
Design/                   # canon produit
legacy/                   # ancien monolithe prototype (archivé)
```

## Boucle scientifique

```
Explorer → Découvrir → Analyser → 2ᵉ espèce → Croiser (preview) → Mission / escorte → Musée → Recommencer
```

- **7 zones / 7 espèces** (chaleur, minéral, froid, abysse, feu, spore, vent)  
- Croisement multi-biomes → synergies narratives + preview  
- MVE · Baie · manifeste d’export · sauvegarde fichier  
- Mutations = option secondaire uniquement  

Voir `README2.md` pour l’état détaillé.
