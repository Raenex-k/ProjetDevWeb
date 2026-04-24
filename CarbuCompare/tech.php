<?php
// Page "Tech" : demonstration des flux JSON (Ghibli) et XML (geoloc IP)

require_once "include/functions.inc.php";
require_once "include/geo.inc.php"; // pour appeler_url()

$page_title    = "Page technique";
$page_desc     = "Demonstration des flux JSON et XML utilises par le site.";
$page_courante = "tech";

incrementerCompteur('tech');

//  FLUX JSON : API Ghibli  liste des films du studio

$film_aleatoire = null;
$json_ghibli = appeler_url('https://ghibliapi.vercel.app/films');
if ($json_ghibli !== null) {
    $films = json_decode($json_ghibli, true);
    if (is_array($films) && count($films) > 0) {
        // On prend un film au hasard pour le footer
        $film_aleatoire = $films[array_rand($films)];
    }
}

// 2) FLUX XML : API whatismyip - geoloc IP au format XML

// Cle API : inscription gratuite sur https://www.whatismyip.com
// A remplacer par votre cle apres inscription. Sans cle, le bloc reste vide.
$cle_api_xml = '550cb4eb35cc369ec3f9c91a854e9fbe';

$ip_test = $_GET['ip'] ?? '193.54.115.192';
$geo_xml = null;

if ($cle_api_xml !== '') {
    $url_xml = "https://api.whatismyip.com/ip-address-lookup.php?key={$cle_api_xml}&input={$ip_test}&output=xml";
    $reponse_xml = appeler_url($url_xml);

    if ($reponse_xml !== null) {
    
        if ($xml !== false) {
            $geo_xml = [
                'ip'    => (string) ($xml->ip    ?? ''),
                'ville' => (string) ($xml->city  ?? ''),
                'pays'  => (string) ($xml->country_name ?? ''),
            ];
        }
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

    <!-- ============ PARTIE 1 : JSON GHIBLI ============ -->
    <h2>1. Flux JSON : API Ghibli</h2>
    <p>
        L'API <strong>ghibliapi.vercel.app/films</strong> fournit la liste des
        films du studio Ghibli au format JSON. A chaque chargement de cette page,
        un film est tire au hasard et affiche ci-dessous.
    </p>

    <?php if ($film_aleatoire !== null) { ?>
        <div class="film">
            <img src="<?= clean($film_aleatoire['image']) ?>"
                 alt="<?= clean($film_aleatoire['title']) ?>"
                 class="film-image" />
            <div>
                <h3><?= clean($film_aleatoire['title']) ?></h3>
                <p>
                    <strong>Titre original (japonais) :</strong>
                    <span lang="ja"><?= clean($film_aleatoire['original_title']) ?></span>
                </p>
                <p><strong>Annee :</strong> <?= clean($film_aleatoire['release_date']) ?></p>
                <p><strong>Realisateur :</strong> <?= clean($film_aleatoire['director']) ?></p>
                <p><?= clean($film_aleatoire['description']) ?></p>
            </div>
        </div>
    <?php } else { ?>
        <p class="rappel">L'API Ghibli n'a pas pu etre jointe pour le moment.</p>
    <?php } ?>

    <!-- ============ PARTIE 2 : XML WHATISMYIP ============ -->
    <h2>2. Flux XML : API whatismyip.com</h2>
    <p>
        L'API <strong>api.whatismyip.com</strong> renvoie la geolocalisation
        d'une adresse IP au format XML. Elle necessite une cle d'API gratuite
        (voir <a href="https://www.whatismyip.com/" target="_blank">whatismyip.com</a>).
    </p>

    <form method="get" action="tech.php" class="filtres">
        <div class="filtre">
            <label for="ip">Adresse IP a tester</label>
            <input type="text" id="ip" name="ip"
                   value="<?= clean($ip_test) ?>"
                   placeholder="ex. 193.54.115.192" />
        </div>
        <button type="submit" class="bouton bouton-petit">Tester</button>
    </form>

    <?php if ($cle_api_xml === '') { ?>
        <div class="rappel">
            <strong>Cle API non configuree.</strong><br />
            Pour activer cette demonstration, inscrivez-vous sur whatismyip.com
            et ajoutez votre cle dans la variable <code>$cle_api_xml</code>
            en haut de <code>tech.php</code>.
        </div>
    <?php } elseif ($geo_xml === null) { ?>
        <p class="rappel">
            L'API XML n'a pas pu etre jointe ou la reponse est invalide.
        </p>
    <?php } else { ?>
        <div class="position">
            <div>
                <span class="position-label">Resultat de l'API XML</span>
                <p class="position-ville">
                    <?= clean($geo_xml['ville']) ?>
                    <span class="position-region"><?= clean($geo_xml['pays']) ?></span>
                </p>
                <p class="position-coords">IP : <?= clean($geo_xml['ip']) ?></p>
            </div>
        </div>
    <?php } ?>

    <p class="rappel">
        Ces deux flux (JSON et XML) sont demandes dans la premiere partie du
        sujet du projet. Le flux XML necessite une cle d'API ; le flux JSON
        de Ghibli est libre.
    </p>

</div>
</main>

<?php require_once "include/footer.inc.php"; ?>
