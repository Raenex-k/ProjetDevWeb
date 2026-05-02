<?php


/**
 * @file statistiques.php
 * @brief Page "Statistiques" : visites par page + villes les plus consultees
 * @author Rayane Khitous / Hugo Delhelle
 * @date Avril 2026
 */

require_once "include/functions.inc.php";

$page_title="Statistiques";
$page_desc="Villes les plus consultées et visites du site.";
$page_courante="stats";

incrementerCompteur('statistiques');

// Compteur de visites par page 
$compteurs = [];
$fichier = __DIR__ . '/data/compteur.txt';
if (file_exists($fichier)) {
    foreach (file($fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $l) {
        $parts = explode(':', $l);
        if (count($parts) === 2){

        
        $compteurs[$parts[0]] = (int) $parts[1];
        }
    }

}
$total_visites=array_sum($compteurs);
$max= !empty($compteurs) ? max($compteurs) : 1;


$noms_pages= [
    'index'  => 'Accueil',
    'carburants' => 'Comparateur',
    'stations'  => 'A proximite',
    'statistiques' => 'Statistiques',
    'tech' => 'Tech',
    'plan' => 'Plan du site',
];

// Top 10 des villes les plus consultées 
$top_villes=villes_les_plus_consultees(10);
$max_ville =!empty($top_villes) ? max($top_villes) : 1;

require_once "include/header.inc.php";
?>

<main>

<section class="page-titre">
    <div class="contenu">
        <h1>Statistiques du site</h1>
        <p>Villes les plus consultées et activite globale.</p>
    </div>
</section>

<div class="contenu">

<h2>Chiffres globaux</h2>

    <div class="chiffres">
        <div class="chiffre">

            <p class="chiffre-valeur" role="heading" aria-level="2"><?= $total_visites ?></p>
            <p class="chiffre-label">visites totales sur le site</p>
        </div>
        <div class="chiffre">
            <p class="chiffre-valeur" role="heading" aria-level="2"><?= array_sum($top_villes) ?></p>
            <p class="chiffre-label">consultations de villes enregistrées</p>
        </div>
        <div class="chiffre">

            <p class="chiffre-valeur" role="heading" aria-level="2"><?= count($top_villes) ?></p>
            <p class="chiffre-label">villes differentes consultées</p>

        </div>
    </div>

    <!--  villes les plus consultées -->
    <h2>Top 10 des villes les plus consultées</h2>

    <?php if (empty($top_villes)) { ?>
        <p class="rappel"> Aucune ville consultee pour le moment. Allez sur le <a href="carburants.php">comparateur</a> pour 
        selectionner une ville.
        </p>
    <?php } else { ?>
        <table class="histogramme">
            <thead>
                <tr>
                    <th>Ville</th>
                    <th>Consultations</th>
                    <th>Histogramme</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($top_villes as $ville => $n) {
                    $largeur = ($n / $max_ville) * 100;
                ?>
                    <tr>
                       
                        <td>
                            <?php
                                // On extrait juste le nom de la ville sans le cp entre parenthèses 
                                $nom_seul = preg_replace('/\s*\(.*\)$/', '', $ville);
                            ?>
                            <a href="carburants.php?recherche=<?= urlencode($nom_seul) ?>">
                                <?= clean($ville) ?>
                            </a>
                        </td>
                        <td class="nombre"><?= $n ?></td>
                        <td>
                            <div class="barre">
                                <div class="barre-remplie" style="width: <?= $largeur ?>%;"></div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>

    <!-- visites par page -->
    <h2>Visites par page</h2>

    <?php if (empty($compteurs)) { ?>
        <p>Aucune visite enregistree.</p>
    <?php } else { ?>
        <table class="histogramme">
            <thead>
                <tr>
                    <th>Page</th>
                    <th>Visites</th>
                    <th>Histogramme</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($compteurs as $page => $n) {
                    $nom = $noms_pages[$page] ?? $page;
                    $largeur = ($n / $max) * 100;
                ?>
                    <tr>
                    <td><?= clean($nom) ?></td>
                        <td class="nombre"><?= $n ?></td>
                     <td>
                        <div class="barre">
                            <div class="barre-remplie" style="width: <?= $largeur ?>%;"></div>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>



</div>
</main>

<?php require_once "include/footer.inc.php"; ?>