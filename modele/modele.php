<?php

// Connexion à la base de données, retourne un lien de connexion
function getConnexionBD() {
    try {
        $connexion = new PDO(DNS, UTILISATRICE, MOTDEPASSE);
        $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur de connexion: " . $e->getMessage();
        return null;
    }
    return $connexion;
}

// Déconnexion de la base de données
function deconnectBD($connexion) {
    $connexion = null;
}

function insert_line_breaks($text, $max_words = 10) {
    $words = explode(' ', $text);
    $output = '';
    for ($i = 0; $i < count($words); $i++) {
        $output .= $words[$i] . ' ';
        if (($i + 1) % $max_words == 0) {
            $output .= '<br>';
        }
    }
    return $output;
}

// Récupérer tous les produits (avec stock et catégorie)
function getProducts($connexion) {
    $stmt = $connexion->query("
        SELECT 
            p.id,
            p.nameP,
            p.prix,
            p.descriptionP,
            p.descriptionT,
            p.imageP,
            p.stock,
            p.categorie_id, -- ✅ ajouté ici
            c.nom AS categorie_nom
        FROM produit p
        LEFT JOIN categories c ON p.categorie_id = c.id
        ORDER BY p.nameP
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Récupérer les images d'un produit
function getProductImages($pdo, $productId) {
    try {
        $stmt = $pdo->prepare("SELECT image_url FROM produit_images WHERE produit_id = :id");
        $stmt->execute(['id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        echo "<pre>Erreur lors de la récupération des images : " . $e->getMessage() . "</pre>";
        return [];
    }
}

// Récupérer les détails d'un produit (avec stock)
function getProductDetails($productId) {
    $pdo = getConnexionBD();
    $stmt = $pdo->prepare("SELECT nameP, prix, imageP, stock FROM produit WHERE id = :id");
    $stmt->execute(['id' => $productId]);
    return $stmt->fetch();
}

// Ajouter un produit au panier
function addProductToCart($productId, $quantity) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
    return 0;
}

// Récupérer les éléments du panier
function getCartItems() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
}

// Ajouter des images supplémentaires
function addAdditionalImages($pdo, $productId, $images) {
    $stmt = $pdo->prepare("INSERT INTO produit_images (produit_id, image_url) VALUES (:product_id, :image_url)");
    foreach ($images as $img) {
        $stmt->execute([
            'product_id' => $productId,
            'image_url' => $img
        ]);
    }
}

// Mettre à jour un produit (avec stock)
function updateProduct($pdo, $id, $name, $price, $descriptionP, $descriptionT, $imageP, $stock, $categorie_id) {
    $sql = "UPDATE produit 
            SET nameP = :name, 
                prix = :price, 
                descriptionP = :descP, 
                descriptionT = :descT, 
                imageP = :imageP, 
                stock = :stock,
                categorie_id = :categorie_id
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':name'         => $name,
        ':price'        => $price,
        ':descP'        => $descriptionP,
        ':descT'        => $descriptionT,
        ':imageP'       => $imageP,
        ':stock'        => $stock,
        ':categorie_id' => $categorie_id,
        ':id'           => $id
    ]);
}


// Supprimer les images supplémentaires
function deleteAdditionalImages($pdo, $productId) {
    $stmt = $pdo->prepare("DELETE FROM produit_images WHERE produit_id = :id");
    $stmt->execute(['id' => $productId]);
}

// Récupérer un produit complet (avec stock)
function getProductById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM produit WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

// Récupérer les produits d'une catégorie (avec images)
function getProductsByCategory(PDO $pdo, int $categoryId): array {
    $stmt = $pdo->prepare("
        SELECT 
            p.*, 
            c.nom AS categorie_nom
        FROM produit p
        LEFT JOIN categories c ON p.categorie_id = c.id
        WHERE p.categorie_id = ?
    ");
    $stmt->execute([$categoryId]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as &$product) {
        $product['images'] = getProductImages($pdo, $product['id']);
    }
    unset($product);

    return $products;
}


// Récupérer les images supplémentaires d'un produit
function getAdditionalImagesByProductId($pdo, $productId) {
    $stmt = $pdo->prepare("SELECT * FROM produit_images WHERE produit_id = ?");
    $stmt->execute([$productId]);
    return $stmt->fetchAll();
}

// Ajouter un produit
function addProduct($pdo, $name, $prix, $descP, $descT, $imageP, $stock, $categorie_id) {
    $stmt = $pdo->prepare("
        INSERT INTO produit (nameP, prix, descriptionP, descriptionT, imageP, stock, categorie_id)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$name, $prix, $descP, $descT, $imageP, $stock, $categorie_id]);
    return $pdo->lastInsertId();
}

// Obtenir toutes les catégories
function getAllCategory(PDO $pdo): array {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY nom");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Vérifie si une catégorie existe déjà (nom unique)
function categoryExists(PDO $pdo, string $nom): bool {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE nom = ?");
    $stmt->execute([$nom]);
    return $stmt->fetchColumn() > 0;
}

// Ajouter une nouvelle catégorie (si non existante)
function addCategory(PDO $pdo, string $nom, ?string $image): bool {
    $stmt = $pdo->prepare("INSERT INTO categories (nom, image) VALUES (?, ?)");
    return $stmt->execute([$nom, $image]);
}

// Vérifie si la catégorie est utilisée
function isCategoryUsed(PDO $pdo, int $id): bool {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM produit WHERE categorie_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn() > 0;
}

// Supprimer une catégorie si non utilisée
function deleteCategory(PDO $pdo, int $id): bool {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    return $stmt->execute([$id]);
}

// Modifier le nom d'une catégorie
function updateCategory(PDO $pdo, int $id, string $nom, ?string $imagePath): bool {
    $sql = "UPDATE categories SET nom = :nom, image = :image WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':nom' => $nom,
        ':image' => $imagePath,
        ':id' => $id,
    ]);
}

// Récupérer une catégorie par ID
function getCategoryById(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    return $category ?: null;
}

// Fonction pratique pour afficher le nom de la catégorie à partir de son ID
function getCategoryNameById(array $categories, int $id): string {
    foreach ($categories as $category) {
        if ($category['id'] === $id) {
            return $category['nom'];
        }
    }
    return "Inconnue";
}

function updateProductStock(PDO $pdo, int $productId, int $newStock): bool {
    $stmt = $pdo->prepare("UPDATE produit SET stock = :stock WHERE id = :id");
    return $stmt->execute(['stock' => $newStock, 'id' => $productId]);
}

function searchProducts(PDO $pdo, string $mot, int $limit = 50): array {
    $mot = '%' . strtolower(trim($mot)) . '%';

    $sql = "
        SELECT p.*, c.nom AS categorie_nom
        FROM produit p
        LEFT JOIN categories c ON p.categorie_id = c.id
        WHERE LOWER(p.nameP) LIKE :mot
           OR LOWER(p.descriptionP) LIKE :mot
           OR LOWER(p.descriptionT) LIKE :mot
        ORDER BY p.nameP
        LIMIT :limit
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':mot', $mot, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

