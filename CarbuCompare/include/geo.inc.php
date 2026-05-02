<?php

/**
 * @file geo.inc.php
 * @brief géolocalisation par IP et calculs de distances GPS.
 * @details Ce fichier permet de détecter l'emplacement (a peu près) de l'utilisateur
 *  fournit des fonctionspour situer les stations à proximité.
 * @author Rayane Khitous / Hugo Delhelle
 * @date Mai 2026
 * @see https://www.php.net/manual/en/features.remote-files.php
 */

/**
 * Récupère l'adresse IP réelle du visiteur.
 * @details Gère les cas où le visiteur passe par un proxy (via HTTP_X_FORWARDED_FOR).
 * @return string L'adresse IP détectée ou '127.0.0.1' par défaut.
 */
function ip_visiteur() {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($parts[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

/**
 * Vérifie si une adresse IP est publique et géolocalisable.
 * @details Exclut les plages d'adresses privées (réseau local) et réservées.
 * @param string $ip L'adresse IP à tester.
 * @return bool True si l'IP est publique, False sinon.
 * @see https://www.php.net/manual/fr/function.filter-var.php
 */
function ip_publique($ip) {
    $flags = FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
    return filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false;
}

/**
 * Effectue une requête HTTP pour récupérer le contenu d'une URL.
 * @details Tente d'utiliser l'extension cURL si disponible, sinon bascule sur file_get_contents.
 * @param string $url L'adresse de la ressource à récupérer.
 * @return string|null Le contenu de la réponse ou null en cas d'échec technique.
 * @note Le délai d'attente (timeout) est fixé à 5 secondes pour ne pas bloquer le site.
 * @see https://www.php.net/manual/fr/function.file-get-contents.php
 * @see https://www.php.net/manual/en/features.remote-files.php
 */
function appeler_url($url) {
    // Avec cURL si dispo
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'CarbuCompare/1.0');
        $reponse = curl_exec($ch);
        return $reponse !== false ? $reponse : null;
    }

    // Sinon fallback avec file_get_contents
    $contexte = stream_context_create(['http' => ['timeout' => 5]]);
    $reponse = @file_get_contents($url, false, $contexte);
    return $reponse !== false ? $reponse : null;
}

/**
 * Géolocalise une adresse IP en interrogeant l'API ipinfo.io.
 * @details Récupère les coordonnées GPS, la ville et la région au format JSON.
 * @param string $ip L'IP à localiser (si vide, utilise l'IP du visiteur actuel).
 * @return array Tableau associatif contenant le statut 'ok', les infos géo et d'éventuelles erreurs.
 * @see https://www.php.net/manual/fr/function.json-decode.php
 * @see https://www.php.net/manual/fr/function.urlencode.php
 */
function geolocaliser($ip = '') {
    if ($ip === '') $ip = ip_visiteur();

    $resultat = [
        'ok' => false,
        'ip'  => $ip,
        'ville'  => '',
        'region' => '',
        'lat' => null,
        'lon' => null,
        'erreur' => '',
    ];

    // IP privee ou locale : on ne peut pas la geolocaliser
    if (!ip_publique($ip)) {
        $resultat['erreur'] = 'IP privee ou locale (non geolocalisable).';
        return $resultat;
    }

    // Appel de l'API ipinfo.io (format JSON)
    $url = 'https://ipinfo.io/' . urlencode($ip) . '/geo';
    $reponse = appeler_url($url);
    if ($reponse === null) {
        $resultat['erreur'] = 'Service de geolocalisation indisponible.';
        return $resultat;
    }

    $data = json_decode($reponse, true);
    if (!is_array($data)) {
        $resultat['erreur'] = 'Reponse JSON invalide.';
        return $resultat;
    }

    $resultat['ville']  = $data['city']   ?? '';
    $resultat['region'] = $data['region'] ?? '';

    // 'loc' contient "latitude,longitude" en une chaine
    if (!empty($data['loc'])) {
        $coords = explode(',', $data['loc']);
        if (count($coords) === 2) {
            $resultat['lat']=(float) $coords[0];
            $resultat['lon']=(float) $coords[1];
        }
    }

    $resultat['ok'] = $resultat['lat'] !== null;
    return $resultat;
}

/**
 * Calcule la distance approximative à vol d'oiseau entre deux points GPS.
 * @details Utilise une approximation adaptée au territoire français.
 * @param float $lat1 Latitude du premier point.
 * @param float $lon1 Longitude du premier point.
 * @param float $lat2 Latitude du second point.
 * @param float $lon2 Longitude du second point.
 * @return float Distance en kilomètres
 */
function distance_km($lat1, $lon1, $lat2, $lon2) {
    // 1 degre de latitude = environ 111 km
    // 1 degre de longitude = environ 73 km en France
    $dx = ($lon2 - $lon1) * 73;
    $dy = ($lat2 - $lat1) * 111;
    return sqrt($dx * $dx + $dy * $dy);
}
