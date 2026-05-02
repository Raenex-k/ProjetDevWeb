<?php
// Page "Plan du site" : liste simple de toutes les pages
/**
 * @file plan.php
 * @brief Page "Plan du site" : liste simple de toutes les pages
 * @author Rayane Khitous / Hugo Delhelle
 * @date Avril 2026
 */
require_once "include/functions.inc.php";

$page_title="Plan du site";
$page_desc="Liste de toutes les pages du site CarbuCompare.";
$page_courante="plan";

incrementerCompteur('plan');

require_once "include/header.inc.php";
?>

<main>

<section class="page-titre">
    <div class="contenu">
        <h1>Plan du site</h1>
        <p>Liste de toutes les pages disponibles sur CarbuCompare.</p>
    </div>
</section>

<div class="contenu">

    <h2>Pages principales</h2>
    <ul class="liste-pages">
        <li>
            <a href="index.php"><strong>Accueil</strong></a>
            <span>Page de presentation du site et des modes de recherche.</span>
        </li>
        <li>
            <a href="carburants.php"><strong>Comparateur</strong></a>
            <span>Recherche par region, departement et ville via une carte interactive.</span>
        </li>
        <li>
            <a href="stations.php"><strong>A proximite</strong></a>
            <span>Stations-service proches de votre position estimee par IP.</span>
        </li>
        <li>
            <a href="statistiques.php"><strong>Statistiques</strong></a>
            <span>Nombre de visites et villes les plus consultees.</span>
        </li>
    </ul>

    <h2>Pages annexes</h2>
    <ul class="liste-pages">
        <li>
            <a href="tech.php"><strong>Page technique</strong></a>
            <span>Demonstration des flux JSON (Ghibli) et XML (geoloc par IP).</span>
        </li>
        <li>
            <a href="plan.php"><strong>Plan du site</strong></a>
            <span>Cette page : liste de toutes les pages disponibles.</span>
        </li>
    </ul>

</div>
</main>

<?php require_once "include/footer.inc.php"; ?>
