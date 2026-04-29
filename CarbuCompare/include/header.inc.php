<?php

// Valeurs par defaut
if (!isset($page_title)) $page_title= 'CarbuCompare';
if (!isset($page_desc)) $page_desc ='Prix des carburants en France.';
if (!isset($page_courante)) $page_courante= '';

// Gestion du theme (cookie qui dure 30 jours)
if (isset($_GET['theme']) && in_array($_GET['theme'], ['jour', 'nuit'])) {
    setcookie('theme', $_GET['theme'], time() +30*24*3600, '/');
    $theme = $_GET['theme'];
} else {
    $theme = $_COOKIE['theme'] ?? 'jour';
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= clean($page_title) ?> — CarbuCompare</title>
    <meta name="description" content="<?= clean($page_desc) ?>"/>
    <link rel="stylesheet" href="css/style.css"/>
    <?php if ($theme === 'nuit') { ?>
        <link rel="stylesheet" href="css/nuit.css"/>
    <?php } ?>
    <link rel="icon" href="images/favicon.png"/>
</head>
<body>

<header>
    <div class="contenu entete-haut">
        <a class="marque" href="index.php">
            <img src="images/logo.png" alt=""/>
            <div>
                <p class="titre" role="heading" aria-level="1">Les prix des carburants</p>
                <p class="baseline">Prix des carburants en France metropolitaine</p>
            </div>
        </a>

        <div class="theme">
            <a href="?theme=jour" class="<?= $theme === 'jour' ? 'actif' : '' ?>">Jour</a>
            <a href="?theme=nuit" class="<?= $theme === 'nuit' ? 'actif' : '' ?>">Nuit</a>
        </div>
    </div>

    <nav>
        <div class="contenu">
            <ul>
                <li><a href="index.php" class="<?= $page_courante==='index' ? 'actif' : '' ?>">Accueil</a></li>
                <li><a href="carburants.php" class="<?= $page_courante==='carburants' ? 'actif' : '' ?>">Comparateur</a></li>
                <li><a href="stations.php" class="<?= $page_courante==='stations' ? 'actif' : '' ?>">A proximite</a></li>
                <li><a href="statistiques.php" class="<?= $page_courante==='stats'  ? 'actif' : '' ?>">Statistiques</a></li>
                <li><a href="tech.php" class="<?= $page_courante==='tech'  ? 'actif' : '' ?>">Tech</a></li>
                <li><a href="plan.php" class="<?= $page_courante==='plan' ? 'actif' : '' ?>">Plan du site</a></li>
            </ul>
        </div>
    </nav>
</header>