<?php
// Pied de page commun
?>
<footer class="pied">
    <div class="contenu">
        <div class="pied-colonnes">

            <!-- Colonne 1 : marque et infos binome -->
            <div>
                <h2>CarbuCompare</h2>
                <p>
                    Comparateur de prix des carburants en France metropolitaine,
                    base sur les donnees ouvertes du gouvernement.
                </p>
                <p>Projet pedagogique — UE Developpement Web<br />
                L2 Informatique — CY Cergy Paris Universite</p>
                <p>Binome : <strong>Rayane KHITOUS</strong> &amp; <strong>Hugo DELHELLE</strong></p>
            </div>

            <!-- Colonne 2 : liens de navigation -->
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

            <!-- Colonne 3 : sources des donnees -->
            <div>
                <h3>Donnees &amp; sources</h3>
                <ul>
                    <li><a href="https://data.economie.gouv.fr/" target="_blank">data.economie.gouv.fr</a></li>
                    <li><a href="https://www.insee.fr/" target="_blank">INSEE</a></li>
                    <li><a href="https://www.data.gouv.fr/" target="_blank">data.gouv.fr</a></li>
                    <li><a href="https://ipinfo.io/" target="_blank">ipinfo.io</a></li>
                </ul>
            </div>

            <!-- Colonne 4 : infos techniques -->
            <div>
                <h3>Informations</h3>
                <p><strong>Visites totales :</strong> <?= lireCompteurTotal() ?></p>
                <p><strong>Heure serveur :</strong> <?= date('d/m/Y H:i') ?></p>
            </div>

        </div>
    </div>

    <!-- Bande du bas : copyright + liens supplementaires -->
    <div class="pied-bas">
        <div class="contenu">
            <div>© <?= date('Y') ?> CarbuCompare · Projet pedagogique L2 Informatique</div>
            <div>
                <a href="plan.php">Plan du site</a>
                <a href="readme.md">README</a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>
