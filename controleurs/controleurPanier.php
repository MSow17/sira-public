<?php
error_log("Contrôleur panier chargé !");

// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialiser le panier s’il n’existe pas
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}



// Gestion des actions POST (ajout, mise à jour, suppression)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST détecté !");
    error_log(print_r($_POST, true));
    // 👉 Supprimer un article
    if (isset($_POST['remove_item'])) {
        $itemId = intval($_POST['remove_item']);
        unset($_SESSION['cart'][$itemId]);

        header("Location: index.php?page=panier");
        exit;
    }

    // 👉 Mise à jour des quantités
    if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $itemId => $quantity) {
            $itemId = intval($itemId);
            $quantity = max(1, intval($quantity)); // min = 1

            $product = getProductDetails($itemId);
            if ($product) {
                $stock = (int) $product['stock'];
                $_SESSION['cart'][$itemId] = min($quantity, $stock);
            }
        }

        header("Location: index.php?page=panier");
        exit;
    }

    // 👉 Ajouter un article depuis formulaire produit
    if (isset($_POST['product_id'])) {
        $productId = intval($_POST['product_id']);
        $quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;

        if (!isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] = 0;
        }

        $_SESSION['cart'][$productId] += $quantity;

        header("Location: index.php?page=panier");
        error_log("Produit ajouté : ID=" . $productId . ", Qté=" . $quantity);
        exit;
    }
}

