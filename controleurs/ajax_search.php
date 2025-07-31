<?php
require_once '../inc/config-bd.php'; 
$modelePath = '../modele/modele.php';
if (file_exists($modelePath)) {
    require_once $modelePath;
} else {
    die("Fichier modèle introuvable : $modelePath");
}


$pdo = getConnexionBD();
$query = trim($_GET['q'] ?? '');
if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

// Charger les synonymes
$synonymsFile = '/var/www/html/SIRA/utils/synonyms.txt';
$synonyms = [];
if (file_exists($synonymsFile)) {
    $synonyms = file($synonymsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

$terms = [$query];
foreach ($synonyms as $line) {
    $words = array_map('trim', explode(',', $line));
    if (in_array($query, $words)) {
        $terms = array_merge($terms, $words);
    }
}
$terms = array_unique($terms);

// Récupérer toutes les catégories pour comparaison
$allCategories = getAllCategory($pdo);

// Trouver les ids des catégories correspondant aux termes
$categoryIds = [];
foreach ($terms as $term) {
    foreach ($allCategories as $cat) {
        if (stripos($cat['nom'], $term) !== false) {
            $categoryIds[] = $cat['id'];
        }
    }
}
$categoryIds = array_unique($categoryIds);

// Construire la requête SQL
$sql = "SELECT p.*, c.nom AS categorie_nom
        FROM produit p
        LEFT JOIN categories c ON p.categorie_id = c.id";

$clauses = [];
$params = [];

foreach ($terms as $term) {
    $clauses[] = "LOWER(p.nameP) LIKE ?";
    $params[] = '%' . strtolower($term) . '%';
}

if (!empty($categoryIds)) {
    $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
    $clauses[] = "p.categorie_id IN ($placeholders)";
    $params = array_merge($params, $categoryIds);
}

if (!empty($clauses)) {
    $sql .= " WHERE " . implode(' OR ', $clauses);
}

$sql .= " ORDER BY p.nameP LIMIT 20";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($results);
