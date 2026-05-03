<?php

/**
 * @file tech.php
 * @brief Page "Technique" : demonstration des flux JSON et XML
 * @author Rayane Khitous / Hugo Delhelle
 * @date Avril 2026
 */


require_once "include/functions.inc.php";
require_once "include/geo.inc.php";

$page_title = "Page technique";
$page_desc= "Demonstration des flux JSON et XML utilises par le site.";
$page_courante="tech";

incrementerCompteur('tech');

// PArtie 1 : JSON : On utilise l'Api Ghilbi 
$film = null;
$json = appeler_url('https://ghibliapi.vercel.app/films');
if ($json !== null) {
    $films = json_decode($json, true);
    if (is_array($films)) {
        $film = $films[array_rand($films)];
    }
}

// Partie 1 :XML : geolocalistion IP via whatismyip
$cle_api = '550cb4eb35cc369ec3f9c91a854e9fbe'; // clé  générer apres avoir créer un compte 
$ip_test = $_GET['ip'] ?? '193.54.115.192';
$geo = null;

$url = "https://api.whatismyip.com/ip-address-lookup.php?key={$cle_api}&input={$ip_test}&output=xml";
$reponse = appeler_url($url);

if ($reponse !== null) {
    $xml = @simplexml_load_string($reponse);
    if ($xml !== false) {

        $data = $xml->server_data;
        $geo = [
            'ip' => (string) $data->ip,
            'pays'  => (string) $data->country,
            'region' => (string) $data->region,
            'ville' => (string) $data->city,
            'cp' => (string) $data->postalcode,
            'isp' => (string) $data->isp,
            'lat' => (string) $data->latitude,
            'lon' => (string) $data->longitude,
        ];
    }
}

require_once "include/header.inc.php";
?>

<main>

<section class="page-titre">
    <div class="contenu">
        <h1>Page technique</h1>
        <p>Demonstration des flux JSON et XML utilises par le site.</p>
    </div>
</section>

<div class="contenu">

    <h2>1. Flux JSON : Api Ghibli</h2>
    <?php if ($film !== null) { ?>
        <div class="film">
            <img src="<?= clean($film['image']) ?>" alt="<?= clean($film['title']) ?>" class="film-image" />
            <div>
                <h3><?= clean($film['title']) ?></h3>
                <p><strong>Titre original :</strong>  <span lang="ja"><?= clean($film['original_title']) ?></span></p>

                <p><strong>Annee :</strong> <?= clean($film['release_date']) ?></p>
                <p><strong>Realisateur :</strong> <?= clean($film['director']) ?></p>
                <p><?= clean($film['description']) ?></p>
            </div>
        </div>
    <?php } else { ?>
        <p class="rappel">L'API Ghibli n'a pas pu etre jointe.</p>
    <?php } ?>


    
    <h2>2. Flux XML : geolocalisation par adresse IP</h2>
    <form method="get" action="tech.php" class="filtres">
        <div class="filtre">
            <label for="ip">Adresse IP</label>
            <input type="text" id="ip" name="ip" value="<?= clean($ip_test) ?>" />
        </div>
        <button type="submit" class="bouton bouton-petit">Tester</button>
    </form>

    <?php if ($geo === null) { ?>
    
        <p class="rappel">L'API n'a pas pu etre jointe ou la reponse XML est invalide.</p>
    <?php } else { ?>
        <div class="position">
            <div>
                <span class="position-label">Resultat extrait du XML</span>
                <p class="position-ville">
                    <?= clean($geo['ville']) ?>
                    <span class="position-region"><?= clean($geo['region']) ?></span>
                </p>
                <p class="position-coords">
                    Adresse IP : <?= clean($geo['ip']) ?> ·
                    Pays : <?= clean($geo['pays']) ?> ·
                    Code postal : <?= clean($geo['cp']) ?> ·
                    Coordonnées : <?= clean($geo['lat']) ?>, <?= clean($geo['lon']) ?>
                </p>
            </div>
        </div>
    <?php } ?>


</div>
</main>

<?php require_once "include/footer.inc.php"; ?>