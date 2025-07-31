<?php
require('inc/routes.php');          // Définition des routes du site
require('inc/includes.php');        // Constantes globales : nom du site, slogan, etc.
require('inc/config-bd.php');       // Paramètres de connexion à la base de données
require('inc/config-stmp.php');     // Configuration pour l'envoi d'e-mails SMTP
require('vendor/autoload.php');     // Autoload de Composer (utile si tu utilises PHPMailer, etc.)
require('inc/session_config.php');  // Sécurité, configuration session
require('modele/modele.php');       // Fonctions du modèle
require('inc/elasticsearch.php'); 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = getConnexionBD();  // Connexion à la base de données      
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title><?= htmlspecialchars($nomSite) ?>.com</title>
    
    <!-- Feuilles de style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


    <link rel="stylesheet" href="<?= htmlspecialchars($styleCSS) ?>?v=<?= filemtime($styleCSS) ?>" type="text/css" media="all">


    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="script.js?v=<?= filemtime('script.js') ?>"></script>

</head>
<body>

<?php include($pathHeader); ?>

<div id="divCentral">
    <main>
        <?php
        // Valeurs par défaut
        $controleur = $controleurAccueil;
        $vue = $vueAccueil;

        if (isset($_GET['page'])) {
            $nomPage = $_GET['page'];

            if (isset($routes[$nomPage])) {
                // Vérification des droits d’accès
                $adminPages = ['admin', 'add_article', 'edit_article', 'delete_article'];
                if (in_array($nomPage, $adminPages) && !isset($_SESSION['user_id'])) {
                    header('Location: index.php?page=login');
                    exit;
                }

                // Affectation des bons fichiers
                $controleur = $routes[$nomPage]['controleurs'];
                $vue = $routes[$nomPage]['vues'];
            }
        }

        // Inclusion du contrôleur et de la vue correspondants
        include('controleurs/' . $controleur . '.php');
        include('vues/' . $vue . '.php');
        ?>
    </main>
</div>

<?php include($pathFooter); ?>


</body>
</html>
