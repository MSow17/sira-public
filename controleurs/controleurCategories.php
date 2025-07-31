<?php

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$successMessage = '';
$errorMessage = '';

// Récupération CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Traitement POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errorMessage = "Token CSRF invalide.";
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            $nom = trim($_POST['nom'] ?? '');

            if ($nom === '') {
                $errorMessage = "Le nom de la catégorie est requis.";
            } else {
                $imagePath = null;
                $uploadDir = 'img/categories/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if (in_array($ext, $allowed)) {
                        $fileName = uniqid() . '.' . $ext;
                        $dest = $uploadDir . $fileName;

                        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                            $imagePath = $dest;
                        } else {
                            $errorMessage = "Erreur lors de l’upload de l’image.";
                        }
                    } else {
                        $errorMessage = "Extension de fichier non autorisée.";
                    }
                }

                if (empty($errorMessage)) {
                    if (addCategory($pdo, $nom, $imagePath)) {
                        $successMessage = "✅ Catégorie ajoutée.";
                    } else {
                        $errorMessage = "Erreur lors de l’ajout en base.";
                    }
                }
            }
        } elseif ($action === 'delete') {
            $catId = intval($_POST['categorie_id'] ?? 0);
            if ($catId > 0 && deleteCategory($pdo, $catId)) {
                $successMessage = "✅ Catégorie supprimée.";
            } else {
                $errorMessage = "Erreur lors de la suppression.";
            }
        } elseif ($action === 'update') {
            $categoryId = intval($_POST['id'] ?? 0);

            if ($categoryId <= 0 || !($category = getCategoryById($pdo, $categoryId))) {
                $errorMessage = "Catégorie introuvable.";
            } else {
                $newName = trim($_POST['nom'] ?? '');
                $imagePath = $category['image'];
                $uploadDir = 'img/categories/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Gestion upload image si fournie
                if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if (in_array($ext, $allowed)) {
                        $newFileName = uniqid() . '.' . $ext;
                        $dest = $uploadDir . $newFileName;

                        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                            // Suppression ancienne image si elle existe
                            if (!empty($imagePath) && file_exists($imagePath)) {
                                unlink($imagePath);
                            }
                            $imagePath = $dest;
                        } else {
                            $errorMessage = "Erreur lors de l’upload de l’image.";
                        }
                    } else {
                        $errorMessage = "Extension de fichier non autorisée.";
                    }
                }

                if (empty($errorMessage)) {
                    if (updateCategory($pdo, $categoryId, $newName, $imagePath)) {
                        header("Location: index.php?page=categories&success=1");
                        exit;
                    } else {
                        $errorMessage = "Échec de la mise à jour.";
                    }
                }
            }
        }
    }
}

// Récupération des catégories
$categories = getAllCategory($pdo);
