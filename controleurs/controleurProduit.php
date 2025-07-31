<?php

// Récupérer toutes les catégories
$categories = getAllCategory($pdo);

// Récupérer l’ID de la catégorie sélectionnée via GET (ou 0 par défaut)
$categoryId = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Initialisation de la liste des produits
$products = [];

if ($categoryId > 0) {
    // Produits de la catégorie sélectionnée
    $products = getProductsByCategory($pdo, $categoryId);
} else {
    // Tous les produits
    $products = getProducts($pdo);
}

// Enrichir chaque produit avec ses images supplémentaires
foreach ($products as &$product) {
    $product['images'] = getProductImages($pdo, $product['id']);

    // ⚠️ S'assurer que chaque produit contient bien l'ID de catégorie
    if (!isset($product['categorie_id'])) {
        // Optionnel : récupération manuelle si la jointure n’est pas dans le SELECT
        $product['categorie_id'] = getCategoryIdByProductId($pdo, $product['id']);
    }
}
unset($product); // Bonnes pratiques : libérer la référence

