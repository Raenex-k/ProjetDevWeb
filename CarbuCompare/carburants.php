<?php
// Page pour le comparateur : carte des regions -> departement -> ville -> prix
/**
 * @file carburants.php
 * @brief Page pour le comparateur : carte des regions -> departement -> ville -> prix
 * @author Rayane Khitous / Hugo Delhelle
 * @date Avril 2026
 */
require_once "include/functions.inc.php";
require_once "include/api_carburants.inc.php";
require_once "include/geo.inc.php";
require_once "include/header.inc.php";

$page_title="Comparateur par region";
$page_desc="Selectionnez une region, un departement et une ville pour voir les prix.";
$page_courante="carburants";

incrementerCompteur('carburants');

// Lecture des parametre GET
$region= $_GET['region'] ?? '';
$dep=$_GET['dep'] ?? '';
$ville= $_GET['ville']  ?? '';

// Recherche directe par nom de ville
$recherche= trim($_GET['recherche'] ?? '');
$resultats_recherche=[];

if ($recherche !== '' && strlen($recherche) >= 2) {
    $f = fopen(__DIR__ . '/data/communes.csv', 'r');
    fgetcsv($f, 0, ',', '"', '\\');
    while (($l = fgetcsv($f, 0, ',', '"', '\\')) !== false) {
        if (stripos($l[1], $recherche) === 0) {
            $resultats_recherche[$l[0]] = [
                'nom' => $l[1],
                'cp'  => $l[2],
                'lat' => (float) $l[3],
                'lon' => (float) $l[4],
            ];
        }
        if (count($resultats_recherche) >= 10) break;
    }
    fclose($f);

    // Si une seule ville trouvee, on recupere dep + region et on redirige
    if (count($resultats_recherche) === 1) {
        $insee_direct=array_key_first($resultats_recherche);
        $dep_direct =substr($insee_direct, 0, 2);
        $region_direct='';

        $f = fopen(__DIR__ . '/data/departements.csv', 'r');
        fgetcsv($f, 0, ',', '"', '\\');
        while (($l = fgetcsv($f, 0, ',', '"', '\\')) !== false) {
            if ($l[0] === $dep_direct) {
                $region_direct=$l[1];
                break;
            }
        }
        fclose($f);

        // Redirection vers la page avec les 3 parametres remplis
        header("Location: carburants.php?region={$region_direct}&dep={$dep_direct}&ville={$insee_direct}");
        exit;
    }
}

// Region choisie 
$regions=[];
$f=fopen(__DIR__ . '/data/regions.csv', 'r');
fgetcsv($f, 0, ',', '"', '\\');
while (($l = fgetcsv($f, 0, ',', '"', '\\')) !== false) {
    if ((int) $l[0] >= 11){
         $regions[$l[0]] = $l[5];
        }
    }
fclose($f);

// Départements de la region choisie
$departements=[];
if (isset($regions[$region])) {
    $f = fopen(__DIR__ . '/data/departements.csv', 'r');
    fgetcsv($f, 0, ',', '"', '\\');
    while (($l = fgetcsv($f, 0, ',', '"', '\\')) !== false) {
        if ($l[1] === $region) {

            $departements[$l[0]] = $l[5];
        }
        }
    fclose($f);
}

// villes du departement choisie
$villes=[];
if (isset($departements[$dep])) {
    $f=fopen(__DIR__ . '/data/communes.csv', 'r');
    fgetcsv($f, 0, ',', '"', '\\');
    while (($l = fgetcsv($f, 0, ',', '"', '\\')) !== false) {
        if (substr($l[0], 0, 2) === $dep) {
            $villes[$l[0]] = [
                'nom' => $l[1],
                'cp'  => $l[2],
                'lat' => (float) $l[3],
                'lon' => (float) $l[4],
            ];
        }
    }
    fclose($f);
    uasort($villes, fn($a, $b) => strcmp($a['nom'], $b['nom']));
}

// stations autour de la ville choisie
$infos_ville=null;
$stations= [];
if (isset($villes[$ville])) {
    $infos_ville = $villes[$ville];
    enregistrer_derniere_ville($ville, $infos_ville['nom'], $infos_ville['cp']);
    logger_ville_consultee($ville, $infos_ville['nom'], $infos_ville['cp']);
    $stations = stations_autour($infos_ville['lat'], $infos_ville['lon'], 10000, 20);
}


?>

<main>

<section class="page-titre">
    <div class="contenu">
        <h1>Comparateur par region</h1>
        <p>Selectionnez votre region, votre departement et votre ville.</p>
    </div>
    </section>
     <div class="contenu">
    <h2>Recherche rapide</h2>

    <form method="get" action="carburants.php" class="filtres">
        <div class="filtre">
            <label for="recherche">Nom de la ville</label>
            <input type="text" id="recherche" name="recherche" value="<?= clean($recherche) ?>" placeholder="ex. Cergy, Paris, Lyon..." />
        
        
        </div>
        <button type="submit" class="bouton">Rechercher</button>
    </form>
    </div>






    <?php if ($recherche !== '' && empty($resultats_recherche)) { ?>
        <p class="rappel">Aucune ville trouvee pour "<?= clean($recherche) ?>".</p>
    <?php } elseif (count($resultats_recherche) > 1) { ?>


        <p>Plusieurs villes correspondent, choisissez :</p>
        <div class="boutons-regions">
            <?php foreach ($resultats_recherche as $insee => $v) { ?>
                <a href="?recherche=<?= urlencode($v['nom']) ?>"  class="bouton-region">
                    <?= clean($v['nom']) ?> (<?= clean($v['cp']) ?>)
                </a>
            <?php } ?>
        </div>
    <?php } ?>

<div class="contenu">

    <!-- carte cliquable des regions -->
<h2>1. Choisissez votre region</h2>
<div class="etape-region">

    <div class="carte-france">
        <img src="images/carte-france.jpg" alt="Carte des regions" usemap="#regions" />
        <map name="regions">
            <area shape="rect" coords="362,26,488,167" alt="Hauts-de-France" href="?region=32" />
            <area shape="rect" coords="182,149,340,219" alt="Normandie" href="?region=28" />
            <area shape="rect" coords="365,184,458,248" alt="Ile-de-France" href="?region=11" />
            <area shape="rect" coords="493,125,686,285" alt="Grand Est" href="?region=44" />
            <area shape="rect" coords="38,208,182,293" alt="Bretagne" href="?region=53" />
            <area shape="rect" coords="168,284,285,343" alt="Pays de la Loire" href="?region=52" />
            <area shape="rect" coords="281,276,453,375" alt="Centre-Val de Loire" href="?region=24" />
            <area shape="rect" coords="453,292,625,432" alt="Bourgogne-Franche-Comte" href="?region=27" />
            <area shape="rect" coords="182,375,396,500" alt="Nouvelle-Aquitaine" href="?region=75" />
            <area shape="rect" coords="406,417,614,552" alt="Auvergne-Rhone-Alpes" href="?region=84" />
            <area shape="rect" coords="271,516,531,615" alt="Occitanie" href="?region=76" />
            <area shape="rect" coords="531,531,693,625" alt="Provence-Alpes-Cote-d-Azur" href="?region=93" />
            <area shape="rect" coords="740,635,787,698" alt="Corse" href="?region=94" />
        </map>
    </div>
    
    <div class="boutons-regions">
    <p class="aide">Ou choisissez directement dans la liste :</p>
        <?php foreach ($regions as $code => $nom) { ?>
        
        <a href="?region=<?= $code ?>" class="bouton-region <?= $code === $region ? 'actif' : '' ?>">
            <?= clean($nom) ?>
        </a>
        <?php } ?>
    </div>
    
</div>
    
    <!--choix du département-->
    <?php if (isset($regions[$region])) { ?>
    <h2>2. Choisissez votre departement</h2>
    <p>Region : <strong><?= clean($regions[$region]) ?></strong></p>
    
    <form method="get" action="carburants.php" class="filtres">
            <input type="hidden" name="region" value="<?= clean($region) ?>" />
            <div class="filtre">
                <label for="dep">Departement</label>
                <select id="dep" name="dep" onchange="this.form.submit()">
                    <option value="">-- choisir un departement --</option>
                    <?php foreach ($departements as $code => $nom) { ?>
                    <option value="<?= $code ?>" <?= $code === $dep ? 'selected' : '' ?>>
                            <?= $code ?> — <?= clean($nom) ?>
                        </option>
                        <?php } ?>
                    </select>
                </div>
        </form>
        <?php } ?>
        

    <!--Choix de la ville -->
    <?php if (isset($departements[$dep])) { ?>
        <h2>3. Choisissez votre ville</h2>
        <p>Departement : <strong><?= clean($departements[$dep]) ?></strong>
           (<?= count($villes) ?> communes)</p>

        <form method="get" action="carburants.php" class="filtres">
            <input type="hidden" name="region" value="<?= clean($region) ?>" />
            <input type="hidden" name="dep" value="<?= clean($dep) ?>" />
            <div class="filtre">
                <label for="ville">Ville</label>
                <select id="ville" name="ville" onchange="this.form.submit()">
                    <option value="">-- choisir une ville --</option>
                    <?php foreach ($villes as $insee => $v) { ?>
                        <option value="<?= $insee ?>" <?= $insee === $ville ? 'selected' : '' ?>>
                            <?= clean($v['nom']) ?> (<?= clean($v['cp']) ?>)
                        </option>
                    <?php } ?>
                </select>
            </div>
        </form>
    <?php } ?>


    <!--prix des stations autour de la ville -->
    <?php if ($infos_ville !== null) { ?>
        <h2>4. Prix a <?= clean($infos_ville['nom']) ?></h2>
        <p>Stations dans un rayon de 10 km autour de<strong><?= clean($infos_ville['nom']) ?> (<?= clean($infos_ville['cp']) ?>)</strong>.
    </p>

        <?php if (empty($stations)) { ?>
            <div class="vide">
                <h2>Aucune station trouvee</h2>
                <p>Aucune station-service dans cette zone.</p>
            </div>
        <?php } else { ?>
            <div class="stations">
                <?php foreach ($stations as $st) {
                    if (!isset($st['distance_km'])) continue;
                ?>
                    <article class="station">
                        <header class="station-haut">
                            <h3 class="station-nom">
                                <?= clean($st['nom'] ?: 'Station-service') ?>
                            </h3>
                            <span class="station-distance">
                                <?= number_format($st['distance_km'], 1, ',', '') ?> km
                            </span>
                        </header>

                        <p class="station-adresse">
                            <?= clean($st['adresse']) ?><br />
                            <?= clean($st['cp'] . ' ' . $st['ville']) ?>
                            <?php if ($st['autoroute']) { ?>
                                <span class="station-tag">Autoroute</span>
                            <?php } ?>
                        </p>

                        <?php if (empty($st['prix'])) { ?>
                            <p class="prix-aucun">Prix non renseignes</p>
                            <?php } else { ?>
                            <div class="prix">
                                <?php foreach (CARBURANTS as $c) {
                                    if (!isset($st['prix'][$c])) continue;
                                ?>
                                    <div class="prix-ligne">
                                    <span class="prix-nom"><?= $c ?></span>
                                    <span class="prix-valeur">
                                        <?= number_format($st['prix'][$c], 3, ',', '') ?>
                                    <span class="prix-unite">€/L</span>
                                    </span>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    </article>
            <?php } ?>
        </div>
    <?php } ?>
    <?php } ?>
</div>
</main>

<?php require_once "include/footer.inc.php"; ?>