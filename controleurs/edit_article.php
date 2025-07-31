<?php


if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
// fonctions: getProductById, updateProduct, getAllCategory, etc.

$id = isset($_GET['id']) ? intval($_GET['id']) : null;
if (!$id || $id <= 0) {
    exit("ID invalide.");
}

$product = getProductById($pdo, $id);
if (!$product) {
    exit("Produit introuvable.");
}

$categories = getAllCategory($pdo);
$existingImages = getAdditionalImagesByProductId($pdo, $id);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        exit("Token CSRF invalide.");
    }

    $name     = trim($_POST['nameP'] ?? '');
    $price    = trim($_POST['prix'] ?? '');
    $descP    = trim($_POST['descriptionP'] ?? '');
    $descT    = trim($_POST['descriptionT'] ?? '');
    $stock    = isset($_POST['stock']) ? max(0, intval($_POST['stock'])) : 0;
    $catId    = isset($_POST['categorie_id']) ? intval($_POST['categorie_id']) : null;

    if (!$name || !$price || !$catId) {
        exit("Champs requis manquants.");
    }

    $uploadDir = 'img/produit/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $imageP = $product['imageP'];

    // Upload image principale
    if (isset($_FILES['imageP']) && $_FILES['imageP']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['imageP']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowedExts)) {
            $newName = uniqid() . '.' . $ext;
            $dest = $uploadDir . $newName;
            if (move_uploaded_file($_FILES['imageP']['tmp_name'], $dest)) {
                if (!empty($product['imageP']) && file_exists($product['imageP'])) {
                    unlink($product['imageP']);
                }
                $imageP = $dest;
            }
        }
    }

    // Supprimer images secondaires
    $deletedImages = json_decode($_POST['deleted_images'] ?? '[]', true);
    foreach ($deletedImages as $imgToDelete) {
        if (file_exists($imgToDelete)) unlink($imgToDelete);
        $stmt = $pdo->prepare("DELETE FROM produit_images WHERE produit_id = ? AND image_url = ?");
        $stmt->execute([$id, $imgToDelete]);
    }

    // Mise à jour produit
    updateProduct($pdo, $id, $name, $price, $descP, $descT, $imageP, $stock, $catId);

    // Ajouter images secondaires
    if (!empty($_FILES['images_sup']['name'][0])) {
        $newImages = [];
        foreach ($_FILES['images_sup']['tmp_name'] as $i => $tmpName) {
            if ($_FILES['images_sup']['error'][$i] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['images_sup']['name'][$i], PATHINFO_EXTENSION));
                if (in_array($ext, $allowedExts)) {
                    $newName = uniqid() . '.' . $ext;
                    $dest = $uploadDir . $newName;
                    if (move_uploaded_file($tmpName, $dest)) {
                        $newImages[] = $dest;
                    }
                }
            }
        }
        if (!empty($newImages)) {
            addAdditionalImages($pdo, $id, $newImages);
        }
    }

    // ✅ Plus d'appel à Elasticsearch ici

    header("Location: index.php?page=edit_article&id=$id&success=1");
    exit;
}
