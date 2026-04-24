<?php
// Page "Statistiques" : compteur de visites et villes consultees

require_once "include/functions.inc.php";

$page_title    = "Statistiques";
$page_desc     = "Nombre de visites par page et villes les plus consultees.";
$page_courante = "stats";

incrementerCompteur('statistiques');

// Lecture du compteur de visites depuis le fichier
$compteurs = [];
$fichier = __DIR__ . '/data/compteur.txt';
if (file_exists($fichier)) {
    foreach (file($fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $ligne) {
        $parts = explode(':', $ligne);
        if (count($parts) === 2) {
            $compteurs[$parts[0]] = (int) $parts[1];
        }
    }
}

// Total de toutes les visites
$total = array_sum($compteurs);

// Trouver le maximum pour dimensionner les barres de l'histogramme
$max = !empty($compteurs) ? max($compteurs) : 1;

// Noms jolis pour les pages
$noms_pages = [
    'index'        => 'Accueil',
    'carburants'   => 'Comparateur',
    'stations'     => 'A proximite',
    'statistiques' => 'Statistiques',
    'tech'         => 'Tech',
    'plan'         => 'Plan du site',
];

require_once "include/header.inc.php";
?>

<main>

<section class="page-titre">
    <div class="contenu">
        <h1>Statistiques du site</h1>
        <p>Nombre de visites par page et aperçu de l'activite globale.</p>
    </div>
</section>

<div class="contenu">

    <!-- Chiffre global en haut -->
    <div class="chiffres">
        <div class="chiffre">
            <p class="chiffre-valeur"><?= $total ?></p>
            <p class="chiffre-label">visites totales sur le site</p>
        </div>
        <div class="chiffre">
            <p class="chiffre-valeur"><?= count($compteurs) ?></p>
            <p class="chiffre-label">pages consultees au moins une fois</p>
        </div>
        <div class="chiffre">
            <p class="chiffre-valeur"><?= $max ?></p>
            <p class="chiffre-label">visites sur la page la plus vue</p>
        </div>
    </div>

    <!-- Histogramme des visites par page -->
    <h2>Visites par page</h2>

    <?php if (empty($compteurs)) { ?>
        <p>Aucune visite enregistree pour le moment.</p>
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
                    // Largeur de la barre en pourcentage (entre 0 et 100)
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

    <p class="rappel">
        Les statistiques sont stockees dans un fichier texte sur le serveur
        (<code>data/compteur.txt</code>). Chaque chargement d'une page ajoute 1
        au compteur correspondant.
    </p>

</div>
</main>

<?php require_once "include/footer.inc.php"; ?>
