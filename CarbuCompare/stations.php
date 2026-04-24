<?php
// Page "Stations a proximite" : geoloc par IP + liste des stations autour

require_once "include/functions.inc.php";
require_once "include/geo.inc.php";
require_once "include/api_carburants.inc.php";

$page_title    = "Stations a proximite";
$page_desc     = "Stations-service proches de votre position estimee par IP.";
$page_courante = "stations";

incrementerCompteur('stations');

// Lecture des parametres GET
$ip_test = $_GET['ip']         ?? '';
$rayon   = (int) ($_GET['rayon'] ?? 10);
$carb    = $_GET['carb']       ?? '';

// Validation simple des valeurs
if (!in_array($rayon, [5, 10, 20, 50]))  $rayon = 10;
if ($carb !== '' && !in_array($carb, CARBURANTS)) $carb = '';

// On choisit l'IP a geolocaliser
if ($ip_test !== '' && ip_publique($ip_test)) {
    $ip = $ip_test;
} else {
    $ip = ip_visiteur();
}

$geo = geolocaliser($ip);

// Recherche des stations (seulement si la geoloc a reussi)
$stations = [];
if ($geo['ok']) {
    $stations = stations_autour($geo['lat'], $geo['lon'], $rayon * 1000, 40);
}

require_once "include/header.inc.php";
?>

<main>

<!-- Titre de la page -->
<section class="page-titre">
    <div class="contenu">
        <h1>Stations a proximite</h1>
        <p>Les stations-service les plus proches de votre position estimee par IP.</p>
    </div>
</section>

<div class="contenu">

    <!-- Bloc : position estimee + champ pour tester une autre IP -->
    <div class="position">
        <div>
            <span class="position-label">Position estimee</span>
            <?php if ($geo['ok']) { ?>
                <p class="position-ville">
                    <?= clean($geo['ville']) ?>
                    <span class="position-region"><?= clean($geo['region']) ?></span>
                </p>
                <p class="position-coords">
                    <?= clean($geo['ip']) ?> ·
                    <?= number_format((float) $geo['lat'], 4, ',', '') ?>°N,
                    <?= number_format((float) $geo['lon'], 4, ',', '') ?>°E
                </p>
            <?php } else { ?>
                <p class="position-ville">Position indisponible</p>
                <p class="position-coords"><?= clean($geo['erreur']) ?></p>
            <?php } ?>
        </div>

        <form method="get" action="stations.php">
            <label for="ip">Tester une autre IP :</label>
            <div class="ligne">
                <input type="text" id="ip" name="ip" value="<?= clean($ip_test) ?>"
                       placeholder="ex. 193.54.115.192" />
                <input type="hidden" name="rayon" value="<?= $rayon ?>" />
                <input type="hidden" name="carb"  value="<?= clean($carb) ?>" />
                <button type="submit" class="bouton bouton-petit">Localiser</button>
            </div>
        </form>
    </div>

    <!-- Filtres : rayon + carburant -->
    <form method="get" action="stations.php" class="filtres">
        <input type="hidden" name="ip" value="<?= clean($ip_test) ?>" />

        <div class="filtre">
            <label for="rayon">Rayon de recherche</label>
            <select id="rayon" name="rayon" onchange="this.form.submit()">
                <?php foreach ([5, 10, 20, 50] as $r) { ?>
                    <option value="<?= $r ?>" <?= $r === $rayon ? 'selected' : '' ?>>
                        <?= $r ?> km
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="filtre">
            <label for="carb">Carburant</label>
            <select id="carb" name="carb" onchange="this.form.submit()">
                <option value="">Tous les carburants</option>
                <?php foreach (CARBURANTS as $c) { ?>
                    <option value="<?= $c ?>" <?= $c === $carb ? 'selected' : '' ?>>
                        <?= $c ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </form>

    <!-- Resultats de la recherche -->
    <?php if (!$geo['ok']) { ?>

        <!-- Cas : geoloc impossible -->
        <div class="vide">
            <h2>Impossible de vous localiser</h2>
            <p>
                Essayez avec une autre adresse IP dans le formulaire ci-dessus,
                ou utilisez le <a href="carburants.php">comparateur par region</a>.
            </p>
        </div>

    <?php } elseif (empty($stations)) { ?>

        <!-- Cas : aucune station trouvee -->
        <div class="vide">
            <h2>Aucune station dans ce rayon</h2>
            <p>
                Aucune station trouvee dans un rayon de <?= $rayon ?> km autour
                de <strong><?= clean($geo['ville']) ?></strong>.
            </p>
        </div>

    <?php } else { ?>

        <!-- Cas : on a des stations a afficher -->
        <h2 class="resultats">
            <span class="resultats-nb"><?= count($stations) ?></span>
            stations trouvees dans un rayon de <?= $rayon ?> km
            <?php if ($carb !== '') { ?>
                · filtre <span class="resultats-filtre"><?= clean($carb) ?></span>
            <?php } ?>
        </h2>

        <div class="stations">
            <?php foreach ($stations as $st) {

                // Si un filtre carburant est actif, on saute les stations qui ne l'ont pas
                if ($carb !== '' && !isset($st['prix'][$carb])) continue;

                // Carburants a afficher dans la carte
                if ($carb !== '') {
                    $liste_carbs = [$carb];
                } else {
                    $liste_carbs = CARBURANTS;
                }
            ?>
                <article class="station">
                    <header class="station-haut">
                        <h3 class="station-nom">
                            <?= clean($st['nom'] !== '' ? $st['nom'] : 'Station-service') ?>
                        </h3>
                        <?php if (isset($st['distance_km'])) { ?>
                            <span class="station-distance">
                                <?= number_format($st['distance_km'], 1, ',', '') ?> km
                            </span>
                        <?php } ?>
                    </header>

                    <p class="station-adresse">
                        <?= clean($st['adresse']) ?><br />
                        <?= clean($st['cp'] . ' ' . $st['ville']) ?>
                        <?php if ($st['autoroute']) { ?>
                            <span class="station-tag">Autoroute</span>
                        <?php } ?>
                    </p>

                    <?php if (!empty($st['prix'])) { ?>
                        <div class="prix">
                            <?php foreach ($liste_carbs as $c) {
                                if (!isset($st['prix'][$c])) continue;
                                $actif = $c === $carb ? 'actif' : '';
                            ?>
                                <div class="prix-ligne <?= $actif ?>">
                                    <span class="prix-nom"><?= $c ?></span>
                                    <span class="prix-valeur">
                                        <?= number_format($st['prix'][$c], 3, ',', '') ?>
                                        <span class="prix-unite">€/L</span>
                                    </span>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <p class="prix-aucun">Prix non renseignes</p>
                    <?php } ?>
                </article>
            <?php } ?>
        </div>

        <p class="rappel">
            La geolocalisation par IP est approximative : elle situe le noeud de
            routage de votre fournisseur, parfois a plusieurs kilometres de chez
            vous. Pour une recherche precise, utilisez le
            <a href="carburants.php">comparateur par region et ville</a>.
        </p>

    <?php } ?>

</div>
</main>

<?php require_once "include/footer.inc.php"; ?>
