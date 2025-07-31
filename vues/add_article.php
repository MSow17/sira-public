<?php
// Générer un token CSRF unique s’il n’existe pas encore
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<h2 class="form-title">Ajouter un article</h2>

<form method="POST" enctype="multipart/form-data" class="product-form">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

    <label for="nameP">Nom :</label>
    <input type="text" id="nameP" name="nameP" required>

    <label for="prix">Prix (FCFA) :</label>
    <input type="number" id="prix" name="prix" step="0.01" required>

    <label for="stock">Stock disponible :</label>
    <input type="number" id="stock" name="stock" min="0" required>

    <label for="categorie_id">Catégorie :</label>
    <select id="categorie_id" name="categorie_id" required>
        <option value="">-- Sélectionnez une catégorie --</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
        <?php endforeach; ?>
    </select>


    <label for="descriptionP">Description :</label>
    <textarea id="descriptionP" name="descriptionP" rows="4" required></textarea>

    <label for="descriptionT">Description technique :</label>
    <textarea id="descriptionT" name="descriptionT" rows="4" required></textarea>

    <label for="imageP">Image principale :</label>
    <input type="file" id="imageP" name="imageP" accept="image/*" required>

    <label for="images_sup">Images supplémentaires :</label>
    <input type="file" id="images_sup" name="images_sup[]" multiple accept="image/*">

    <button type="submit" class="submit-btn">Ajouter</button>
</form>

<!-- Bouton retour -->
<div class="category-actions-top">
  <a href="index.php?page=admin" class="btn btn-secondary">← Retour au tableau de bord</a>
</div>