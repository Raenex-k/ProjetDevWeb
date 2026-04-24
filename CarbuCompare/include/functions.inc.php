<?php
// Fonctions utilitaires du site

// Nettoie une chaine pour l'afficher dans le HTML
function clean($texte) {
    if ($texte === null) return '';
    return htmlspecialchars(trim($texte), ENT_QUOTES, 'UTF-8');
}

// Compte les communes dans le CSV (hors ligne d'entete)
function nb_communes() {
    $fichier = __DIR__ . '/../data/communes.csv';
    if (!file_exists($fichier)) return 0;

    $n = 0;
    $f = fopen($fichier, 'r');
    fgets($f); // on saute l'entete
    while (fgets($f) !== false) $n++;
    fclose($f);
    return $n;
}

// Compte les departements (on enleve les DOM-TOM : regions 01 a 06)
function nb_departements() {
    $fichier = __DIR__ . '/../data/departements.csv';
    if (!file_exists($fichier)) return 0;

    $dom_tom = ['01', '02', '03', '04', '06'];
    $n = 0;
    $f = fopen($fichier, 'r');
    fgetcsv($f, 0, ',', '"', '\\'); // on saute l'entete
    while (($ligne = fgetcsv($f, 0, ',', '"', '\\')) !== false) {
        $code_region = trim($ligne[1], '"');
        if (!in_array($code_region, $dom_tom)) $n++;
    }
    fclose($f);
    return $n;
}

// Nombre de regions metropolitaines (fixe depuis 2016)
function nb_regions() {
    return 13;
}

// Date de derniere modif du fichier des communes
function date_maj_donnees() {
    $fichier = __DIR__ . '/../data/communes.csv';
    if (!file_exists($fichier)) return '';
    return date('d/m/Y', filemtime($fichier));
}

// Lit le cookie de la derniere ville consultee
function lire_derniere_ville() {
    if (empty($_COOKIE['derniereville'])) return null;

    $data = json_decode($_COOKIE['derniereville'], true);
    if (!is_array($data) || empty($data['insee']) || empty($data['ville'])) return null;

    return [
        'insee' => $data['insee'],
        'ville' => $data['ville'],
        'cp'    => $data['cp']   ?? '',
    ];
}

// Lit le compteur total de visites
function lireCompteurTotal() {
    $fichier = __DIR__ . '/../data/compteur.txt';
    if (!file_exists($fichier)) return 0;

    $total = 0;
    $lignes = file($fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lignes as $l) {
        $parts = explode(':', $l);
        if (count($parts) === 2) $total += (int) $parts[1];
    }
    return $total;
}

// Incremente le compteur d'une page
function incrementerCompteur($page) {
    $fichier = __DIR__ . '/../data/compteur.txt';

    // Lire les compteurs existants
    $compteurs = [];
    if (file_exists($fichier)) {
        foreach (file($fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) {
            $parts = explode(':', $l);
            if (count($parts) === 2) $compteurs[$parts[0]] = (int) $parts[1];
        }
    }

    // Ajouter 1 a la page courante
    $compteurs[$page] = ($compteurs[$page] ?? 0) + 1;

    // Reecrire le fichier
    $contenu = '';
    foreach ($compteurs as $p => $n) $contenu .= $p . ':' . $n . "\n";
    file_put_contents($fichier, $contenu);
}
