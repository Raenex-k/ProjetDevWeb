<?php
// API des prix des carburants (data.economie.gouv.fr)

const API_CARBURANTS = 'https://data.economie.gouv.fr/api/explore/v2.1/catalog/datasets/prix-des-carburants-en-france-flux-instantane-v2/records';

// Liste des 6 carburants suivis
const CARBURANTS = ['Gazole', 'SP95', 'SP98', 'E10', 'E85', 'GPLc'];

// Recupere les stations dans un rayon autour d'un point GPS
function stations_autour($lat, $lon, $rayon_m = 10000, $limite = 40) {
    // Filtre spatial fourni par l'API
    $filtre = "within_distance(geom, geom'POINT($lon $lat)', {$rayon_m}m)";
    $url = API_CARBURANTS . '?limit=' . $limite . '&where=' . urlencode($filtre);

    $reponse = appeler_url($url);
    if ($reponse === null) {
        
        return [];
    }

    $data = json_decode($reponse, true);
    if (!is_array($data) || empty($data['results'])) {
        return [];
    }

    // On convertit chaque station dans notre format
    $stations = [];
    foreach ($data['results'] as $brut) {
        $station = preparer_station($brut);
        if ($station === null) {
            continue;
        }

        // Calcul de la distance si on a les coordonnees
        if ($station['lat'] !== null) {
            $station['distance_km'] = distance_km($lat, $lon, $station['lat'], $station['lon']);
        }
        $stations[] = $station;
    }

    // Tri par distance croissante 
    usort($stations, function ($a, $b) {
        $da = $a['distance_km'] ?? PHP_FLOAT_MAX;
        $db = $b['distance_km'] ?? PHP_FLOAT_MAX;
        return $da <=> $db;
    });

    return $stations;
}

// Convertit une station brute (API) en format utilisable
function preparer_station($brut) {
    if (!is_array($brut) || empty($brut['id']) || empty($brut['ville'])) return null;

    // Coordonnees GPS : l'API renvoie latitude/longitude en PTV_GEODECIMAL
    // (= coord. standards multipliees par 100000). On divise pour avoir des GPS normales.
    $lat = null;
    $lon = null;
    if (isset($brut['latitude']) && isset($brut['longitude'])) {
        $lat = ((float) $brut['latitude'])  / 100000;
        $lon = ((float) $brut['longitude']) / 100000;
    }

    // Recuperation des prix (un champ par carburant dans l'API : gazole_prix, sp95_prix, ...)
    $prix = [];
    $champs_prix = [
        'Gazole' => 'gazole_prix',
        'SP95' => 'sp95_prix',
        'SP98' => 'sp98_prix',
        'E10' => 'e10_prix',
        'E85'  => 'e85_prix',
        'GPLc' => 'gplc_prix',
    ];
    foreach ($champs_prix as $nom => $champ) {
        if (!empty($brut[$champ])) {
            $prix[$nom] = (float) $brut[$champ];
        }
    }

    return [
        'id' => $brut['id'],
        'nom'  => $brut['enseigne'] ?? '',
        'adresse' => $brut['adresse']  ?? '',
        'ville' => $brut['ville']    ?? '',
        'cp'  => $brut['cp']  ?? '',
        'lat' => $lat,
        'lon'  => $lon,
        'autoroute' => ($brut['pop']   ?? '') === 'A',
        'prix' => $prix,
    ];
}
