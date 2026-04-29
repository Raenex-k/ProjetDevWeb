# CarbuCompare — Comparateur de prix des carburants

Projet universitaire réalisé par **Rayane KHITOUS** et **Hugo DELHELLE**
**Formation** : L2 Informatique  
**Module** : UE Développement Web S4  
**Université** : CY Cergy Paris Université  
**Année** : 2025-2026  
**Supervisé par** : Marc Lemaire  
**Réalisé par** : Rayane KHITOUS & Hugo DELHELLE  

## C'est quoi ce site ?

CarbuCompare est un site web qui permet de comparer les prix des carburants
(Gazole, SP95, SP98, E10, E85, GPLc) dans les stations-service de France
métropolitaine. Les données viennent directement du Ministère de l'Économie
et sont mises à jour en temps réel.


## Comment l'utiliser ?

**Option 1 — Recherche rapide**
Tapez le nom de votre ville dans le champ de recherche et appuyez sur Entrée.
Les stations autour de vous s'affichent directement avec leurs prix.

**Option 2 — Par la carte**
Cliquez sur votre région sur la carte de France, choisissez votre département,
puis votre ville. Les prix s'affichent en dessous.

**Option 3 — Par votre position**
Allez sur "À proximité". Le site détecte votre position approximative via votre
adresse IP et affiche les stations proches. Vous pouvez choisir le rayon de
recherche (5, 10, 20 ou 50 km).



## Pages du site
- **index.php** : Page d'accueil du site avec présentation des fonctionnalités.
- **carburants.php** : Comparateur principal avec carte interactive des régions, sélection du département et de la ville, et affichage des prix.
- **stations.php** : Affiche les stations proches de votre position détectée par adresse IP.
- **statistiques.php** : Montre les villes les plus consultées et le nombre de visites par page.
- **tech.php** : Démonstration technique des flux JSON (API Ghibli) et XML (géolocalisation IP).
- **plan.php** : Liste de toutes les pages du site.


## Structure des fichiers
CarbuCompare/
├── index.php
├── carburants.php
├── stations.php
├── statistiques.php
├── tech.php
├── plan.php
├── css/
│   ├── style.css       
│   └── nuit.css       
├── images/
│   ├── logo.png
│   ├── favicon.png
│   └── carte-france.jpg
├── data/
│   ├── regions.csv          (13 RM)
│   ├── departements.csv     (96 département )
│   ├── communes.csv         (35 511 communes avec coordonnées GPS)
│   ├── compteur.txt         (compteur de visites par page)
│   └── villes_consultees.csv (historique des villes consultées)
└── include/
    ├── header.inc.php       (en-tête commun)
    ├── footer.inc.php       (pied de page commun)
    ├── functions.inc.php    (fonctions utilitaires)
    ├── geo.inc.php          (géolocalisation par IP)
    └── api_carburants.inc.php (appel API prix carburants)



## APIs utilisées

- **data.economie.gouv.fr** : Prix des carburants en temps réel (JSON)
- **ghibliapi.vercel.app** : Films du studio Ghibli (JSON) — page technique
- **api.whatismyip.com** : Géolocalisation par IP (XML) — page technique
- **ipinfo.io** : Géolocalisation par IP pour la page "À proximité" (JSON)

---

## Fonctionnalités techniques

- **Cookie client** : mémorise la dernière ville consultée pendant 30 jours
- **Cookie thème** : mémorise le choix jour/nuit pendant 30 jours
- **Stockage serveur CSV** : enregistre chaque ville consultée avec horodatage
- **Compteur de visites** : compte les visites par page dans un fichier texte
- **Mode nuit** : thème sombre activable depuis n'importe quelle page
- **Carte interactive** : carte cliquable des 13 régions métropolitaines

---

## Installation

1. Copier le dossier `CarbuCompare/` sur votre serveur PHP
2. S'assurer que le dossier `data/` est accessible en écriture
3. Ouvrir `index.php` dans votre navigateur



## Sources des données

- Prix carburants : https://data.economie.gouv.fr
- Régions et départements : https://www.insee.fr
- Communes françaises : https://www.data.gouv.fr
- Portail open data : https://www.data.gouv.fr


## Code source

Le code source du projet est disponible sur GitHub : https://github.com/Raenex-k/ProjetDevWeb
