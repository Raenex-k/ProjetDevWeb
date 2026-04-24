<?php
// Geolocalisation par adresse IP via JSON (API ipinfo.io)

// Recupere l'IP du visiteur
function ip_visiteur() {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($parts[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

// Verifie si l'IP est publique (pas privee ni localhost)
function ip_publique($ip) {
    $flags = FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE;
    return filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false;
}

// Appelle une URL et retourne le contenu (ou null si echec)
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

// Geolocalise une IP via JSON. Retourne un tableau : ok, ip, ville, region, lat, lon, erreur
function geolocaliser($ip = '') {
    if ($ip === '') $ip = ip_visiteur();

    $resultat = [
        'ok'     => false,
        'ip'     => $ip,
        'ville'  => '',
        'region' => '',
        'lat'    => null,
        'lon'    => null,
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

    // Le champ 'loc' contient "latitude,longitude" en une chaine
    if (!empty($data['loc'])) {
        $coords = explode(',', $data['loc']);
        if (count($coords) === 2) {
            $resultat['lat'] = (float) $coords[0];
            $resultat['lon'] = (float) $coords[1];
        }
    }

    $resultat['ok'] = $resultat['lat'] !== null;
    return $resultat;
}

// Distance approximative en km entre 2 points GPS (Pythagore simplifie)
// Precision suffisante pour des distances courtes (< 100 km)
function distance_km($lat1, $lon1, $lat2, $lon2) {
    // 1 degre de latitude = environ 111 km
    // 1 degre de longitude = environ 73 km en France
    $dx = ($lon2 - $lon1) * 73;
    $dy = ($lat2 - $lat1) * 111;
    return sqrt($dx * $dx + $dy * $dy);
}
