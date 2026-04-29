<?php

?>
<footer class="pied">
    <div class="contenu">
        <div class="pied-colonnes">
            <div>
                <h2>Comparateur de prix </h2>
                <p>
                    Comparateur de prix des carburants en France metropolitaine,
                    base sur les donnees ouvertes du gouvernement.
                </p>
                <p>Projet pedagogique — UE Developpement Web<br /> L2 Informatique — CY Cergy Paris Universite</p>
                <p>Binome : <strong>Rayane KHITOUS</strong> &amp; <strong>Hugo DELHELLE</strong></p>
            </div>

            <div>
                <h3>Navigation</h3>
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="carburants.php">Comparateur</a></li>
                    <li><a href="stations.php">A proximite</a></li>
                    <li><a href="statistiques.php">Statistiques</a></li>
                    <li><a href="tech.php">Page technique</a></li>
                    <li><a href="plan.php">Plan du site</a></li>
                </ul>
            </div>


            <div>

                <h3>Donnees &amp; sources</h3>
                <ul>
                    <li><a href="https://data.economie.gouv.fr/" target="_blank">data.economie.gouv.fr</a></li>
                    <li><a href="https://www.insee.fr/fr/accueil" target="_blank">INSEE</a></li>
                    <li><a href="https://www.data.gouv.fr/datasets/base-officielle-des-codes-postaux" target="_blank">data.gouv.fr</a></li>
                    <li><a href="https://ipinfo.io/" target="_blank">ipinfo.io</a></li>
                </ul>
            </div>



            <div>
                <h3>Informations</h3>
                <p><strong>Visites totales :</strong> <?= lireCompteurTotal() ?></p>
                <p><strong>Heure serveur :</strong> <?= date('d/m/Y H:i') ?></p>
            </div>

        </div>
    </div>




    <div class="pied-bas">
        <div class="contenu">
            <div>© <?= date('Y') ?> Comparateur de prix <br /> Projet L2 Informatique</div>
            <div>
                <a href="plan.php">Plan du site</a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
