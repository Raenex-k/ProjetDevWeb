<?php
/**
 * @file functions.inc.php
 * @brief Fonctions utilitaires.
 * @details Ce fichier contient les outils de traitement des fichiers CSV.
 * @author Rayane Khitous / Hugo Delhelle
 * @date Avril 2026
 */



/**
 * Nettoie une chaîne de caractères pour un l'affichage HTML.
 * @param string|null $texte La chaîne de caractères à traiter.
 * @return string La chaîne nettoyée.
 * @see https://www.php.net/manual/fr/function.htmlspecialchars.php
 * @see https://www.php.net/manual/fr/function.trim.php
 */
function clean($texte) {
    if ($texte === null) return '';
    return htmlspecialchars(trim($texte), ENT_QUOTES, 'UTF-8');
}

/**
 * Calcule le nombre total de communes.
 * @return int Nombre de communes .
 * @see https://www.php.net/manual/fr/function.file-get-contents.php
 */
function nb_communes() {
    $fichier=__DIR__ . '/../data/communes.csv';
    if (!file_exists($fichier)) {
        return 0;
        }
    $n=0;
    $f=fopen($fichier, 'r');
    fgets($f); 
    while (fgets($f) !== false) $n++;
    fclose($f);
    return $n;
}

/**
 * Compte les départements du fichier CSV
 * @details exclut les dom-tom donc 01 à 06.
 * @return int Nombre de départements.
 * @see https://www.php.net/manual/fr/function.fgetcsv.php
 */
function nb_departements() {
    $fichier = __DIR__ . '/../data/departements.csv'; 
    if (!file_exists($fichier)) {

        return 0;
    }
    $dom_tom= ['01', '02', '03', '04', '06'];
    $n=0;
    $f=fopen($fichier, 'r'); 
    fgetcsv($f, 0, ',', '"', '\\');
    while (($ligne = fgetcsv($f, 0, ',', '"', '\\')) !== false) {
        $code_region = trim($ligne[1], '"');
        if (!in_array($code_region, $dom_tom)) { 
            $n++;
        }
    }
    fclose($f);
    return $n;
}


/**
 * Récupère la date de dernière mise à jour du fichier des communes.
 * @return string Date ou chaîne vide.
 * @see https://www.php.net/manual/fr/function.filemtime.php
 */
function date_maj_donnees() {
    $fichier = __DIR__ . '/../data/communes.csv';
    if (!file_exists($fichier)) {
        return '';
    }
    return date('d/m/Y', filemtime($fichier));
}



/**
 * Calcule le nombre de visites enregistrées pour toutes les pages.
 * @return int Cumul total des visites.
 */
function lireCompteurTotal() {
    $fichier= __DIR__ . '/../data/compteur.txt';
    if (!file_exists($fichier)) {
        return 0;
    }
    $total=0;
    $lignes=file($fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lignes as $l) {
        $parts=explode(':', $l);
        $total +=(int) $parts[1];
    }
    return $total;
}

/**
 * Incrémente le compteur de visites pour une page spécifique.
 * @param string $page Identifiant de la page.
 * @see https://www.php.net/manual/fr/function.file-put-contents.php
 */
function incrementerCompteur($page) {
    $fichier = __DIR__ . '/../data/compteur.txt';

    // Lire les compteurs enregistrés dans le fichier
    $compteurs = [];
    if (file_exists($fichier)) {
        foreach (file($fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) {
            $parts = explode(':', $l);
            $compteurs[$parts[0]] = (int) $parts[1];
        }
    }

    $compteurs[$page] = ($compteurs[$page] ?? 0) + 1;

    // Reecrire le fichier
    $contenu = '';
    foreach ($compteurs as $p => $n) {
            $contenu .= $p . ':' . $n . "\n";
        } 
    file_put_contents($fichier, $contenu);
}


/**
 * Mémorise la dernière ville consultée dans un cookie pour 30 jours.
 * @param string $insee Code INSEE de la commune.
 * @param string $ville Nom de la ville.
 * @param string $cp Code postal.
 * @see https://www.php.net/manual/fr/function.json-encode.php
 * @see https://www.php.net/manual/fr/function.setcookie.php
 */
function enregistrer_derniere_ville($insee, $ville, $cp) {
    $data = json_encode([
        'insee' => $insee,
        'ville' => $ville,
        'cp' => $cp,
    ]);
    setcookie('derniereville', $data, time() + 30*24 *3600, '/');
}

/**
 * Enregistre une consultation de ville dans un fichier log.
 * @param string $insee Code INSEE.
 * @param string $ville Nom.
 * @param string $cp Code postal.
 */
function logger_ville_consultee($insee, $ville, $cp) {
    $fichier = __DIR__ . '/../data/villes_consultees.csv';

    // Si le fichier n'existe pas, on ecrit l'entete d'abord
    $nouveau = !file_exists($fichier);

    $f= fopen($fichier, 'a');
    if ($nouveau) {
        fputcsv($f, ['horodatage', 'insee', 'ville', 'cp'], ';', '"', '\\');
    }
    fputcsv($f, [date('Y-m-d H:i:s'), $insee, $ville, $cp], ';', '"', '\\');
    fclose($f);
}



/**
 * Récupère le classement des villes les plus consultées à partir du log.
 * @param int $limite Nombre de résultats souhaités (par défaut 10).
 * @return array Tableau associatif [Nom de ville => nombre de visites].
 * @see https://www.php.net/manual/fr/function.arsort.php
 */
function villes_les_plus_consultees($limite = 10) {
    $fichier = __DIR__ . '/../data/villes_consultees.csv';
    if (!file_exists($fichier)) {
        return [];
        }
    $compteur = [];
    $f = fopen($fichier, 'r');
    fgetcsv($f, 0, ';', '"', '\\'); // entete
    while (($ligne = fgetcsv($f, 0, ';', '"', '\\')) !== false) {
        if (count($ligne) < 4) {
            continue;
        }
        $nom = $ligne[2];
        if (!empty($ligne[3])) {
            $nom .= ' (' . $ligne[3] . ')';
        }
        
        
        $compteur[$nom] = ($compteur[$nom] ?? 0) + 1;
    }
    fclose($f);

    arsort($compteur);

    
    return array_slice($compteur,0, $limite, true);
}