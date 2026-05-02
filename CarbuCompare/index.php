<?php

/**
 * @file index.php
 * @brief Page principale.
 * @author Rayane Khitous / Hugo Delhelle
 * @date Avril 2026
 */

require_once "include/functions.inc.php";

$page_title="Accueil";
$page_desc="Comparateur de prix des carburants en France metropolitaine.";
$page_courante="index";

incrementerCompteur('index');

require_once "include/header.inc.php";
?>

<main>

    <section class="accroche">
        <div class="contenu">
            <h1>Comparez les prix des carburants pres de chez vous.</h1>
            <p>
                Service national reposant sur les donnees ouvertes du Ministere
                de l'Economie. Selectionnez votre region, votre departement et
                votre ville pour consulter les prix en direct des stations-service.
            </p>
            <div class="accroche-boutons">
                <a class="bouton" href="carburants.php">Commencer la recherche</a>
                <a class="bouton bouton-vide" href="stations.php">Voir les stations a proximite</a>
            </div>
        </div>
    </section>

    <div class="contenu">

        <!-- Les 3 modes de recherche -->
        <h2>Nos trois modes de recherche</h2>
        <div class="cartes">

            <a class="carte" href="carburants.php">
                <span class="carte-label">Mode guide</span>
                <h3>Par la carte de France</h3>
                <p>
                    Parcours en quatre etapes : region, departement, ville, prix. Carte interactive des 13 regions metropolitaines.
                </p>
                <span class="carte-fleche">Acceder au comparateur →</span>
            </a>

            <a class="carte" href="stations.php">
                <span class="carte-label">Mode rapide</span>
                <h3>Stations a proximite</h3>
                <p>
                    Geolocalisation approximative a partir de votre adresse IP pour afficher les stations dans un rayon parametrable.
                </p>
                <span class="carte-fleche">Me localiser →</span>
            </a>

            <a class="carte" href="statistiques.php">
                <span class="carte-label">Analyse</span>
                <h3>Statistiques d'utilisation</h3>
                <p>
                    Histogramme des villes les plus consultees, repartition des visites par page et compteur global des visiteurs.
                </p>
                <span class="carte-fleche">Voir les statistiques →</span>
            </a>

        </div>

        <!-- données a partir des CSV -->
        <h2>Chiffres cles</h2>
        <p style="color: var(--gris);">
            La base de donnees statiques couvre l'ensemble du territoire metropolitain.
        </p>

        <div class="chiffres">
            <div class="chiffre">
                <p class="chiffre-valeur" role="heading" aria-level="3">13</p>
                <p class="chiffre-label">regions metropolitaines</p>
            </div>
            <div class="chiffre">
                <p class="chiffre-valeur" role="heading" aria-level="3"><?= nb_departements() ?></p>
                <p class="chiffre-label">departements</p>
            </div>
            <div class="chiffre">
                <p class="chiffre-valeur" role="heading" aria-level="3" ><?= number_format(nb_communes(), 0, ',', ' ') ?></p>
                <p class="chiffre-label">communes</p>
            </div>
            <div class="chiffre">
                <p class="chiffre-valeur" style="font-size: 1.4rem;"  role="heading" aria-level="3"><?= date_maj_donnees() ?></p>
                <p class="chiffre-label">derniere mise a jour des donnees</p>
            </div>
        </div>

    </div>

    <!--sources officielles -->
    <section class="sources">
        <div class="contenu">
            <h2>Sources et ressources</h2>
            <p style="color: var(--gris);">
                Toutes les donnees proviennent de sources publiques francaises.
            </p>

            <div class="sources-liste">
                <a class="source" href="https://data.economie.gouv.fr/explore/dataset/prix-des-carburants-en-france-flux-instantane-v2/" target="_blank">
                    <span class="source-titre">Prix des carburants</span>
                <p class="source-desc">Flux instantane du Ministere de l'Economie</p>
                </a>
                
                <a class="source" href="https://www.insee.fr/fr/information/3363419" target="_blank">
                    <span class="source-titre">Regions &amp; departements</span>
                    <p class="source-desc">Code officiel geographique de l'INSEE</p>
                
                </a>
                <a class="source" href="https://www.data.gouv.fr/datasets/base-officielle-des-codes-postaux" target="_blank">
                    <span class="source-titre">Communes francaises</span>

                    <p class="source-desc">Base officielle des codes postaux</p>
               
               
                </a>
                <a class="source" href="https://www.data.gouv.fr/" target="_blank">

                    <span class="source-titre">data.gouv.fr</span>
                    <p class="source-desc">Portail francais des donnees publiques</p>
                </a>
            </div>
        </div>
    </section>

</main>

<?php require_once "include/footer.inc.php"; ?>
