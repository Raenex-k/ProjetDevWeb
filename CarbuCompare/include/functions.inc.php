<?php

// Nettoie une chaine pour l'afficher dans le HTML
function clean($texte) {
    if ($texte === null) return '';
    return htmlspecialchars(trim($texte), ENT_QUOTES, 'UTF-8');
}

// Compte les communes dans le CSV (hors ligne d'entete)
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

// Compte les departements (on enleve les DOM-TOM : regions 01 a 06)
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


// Date de derniere modif du fichier des communes
function date_maj_donnees() {
    $fichier = __DIR__ . '/../data/communes.csv';
    if (!file_exists($fichier)) {
        return '';
    }
    return date('d/m/Y', filemtime($fichier));
}



// Lit le compteur total de visites
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

// Incremente le compteur d'une page
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



function enregistrer_derniere_ville($insee, $ville, $cp) {
    $data = json_encode([
        'insee' => $insee,
        'ville' => $ville,
        'cp' => $cp,
    ]);
    setcookie('derniereville', $data, time() + 30*24 *3600, '/');
}



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