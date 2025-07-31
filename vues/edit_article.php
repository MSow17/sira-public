<?php if (isset($_GET['success'])): ?>
  <p class="edit-success-message">✅ Produit modifié avec succès.</p>
<?php endif; ?>

<h2 class="edit-title">Modifier un produit</h2>

<form method="POST" enctype="multipart/form-data" action="index.php?page=edit_article&id=<?= $id ?>" class="edit-form">
  <input type="hidden" name="action" value="update">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

  <label for="nameP" class="form-label">Nom :</label>
  <input type="text" id="nameP" name="nameP" class="form-input" value="<?= htmlspecialchars($product['nameP']) ?>" required>
  <label for="categorie_id" class="form-label">Catégorie :</label>
  <select name="categorie_id" id="categorie_id" class="form-input" required>
    <option value="">-- Choisir une catégorie --</option>
    <?php foreach ($categories as $cat): ?>
      <option value="<?= $cat['id'] ?>" <?= ($product['categorie_id'] == $cat['id']) ? 'selected' : '' ?>>
        <?= htmlspecialchars($cat['nom']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <label for="prix" class="form-label">Prix (FCFA) :</label>
  <input type="number" id="prix" name="prix" class="form-input" value="<?= htmlspecialchars($product['prix']) ?>" step="0.01" required>

  <label for="stock" class="form-label">Stock disponible :</label>
  <input type="number" id="stock" name="stock" class="form-input" value="<?= htmlspecialchars($product['stock'] ?? 0) ?>" min="0" required>

  <label for="descriptionP" class="form-label">Description :</label>
  <textarea id="descriptionP" name="descriptionP" class="form-textarea" required><?= htmlspecialchars($product['descriptionP']) ?></textarea>

  <label for="descriptionT" class="form-label">Description technique :</label>
  <textarea id="descriptionT" name="descriptionT" class="form-textarea" required><?= htmlspecialchars($product['descriptionT']) ?></textarea>

  <h3 class="form-subtitle">Image principale actuelle :</h3>
  <?php if (!empty($product['imageP'])): ?>
    <img src="<?= htmlspecialchars($product['imageP']) ?>" class="main-image-preview">
  <?php endif; ?>
  <input type="file" name="imageP" id="imageP" class="form-file-input" accept="image/*">

  <h3 class="form-subtitle">Images supplémentaires actuelles :</h3>
  <?php if (!empty($existingImages)): ?>
    <div id="additional-images" class="additional-images-list">
      <?php foreach ($existingImages as $img): ?>
        <div class="image-container">
          <img src="<?= htmlspecialchars($img['image_url']) ?>" class="extra-image-preview">
          <button type="button" class="delete-image-btn" data-img="<?= htmlspecialchars($img['image_url']) ?>">X</button>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="no-extra-image">Aucune image supplémentaire.</p>
  <?php endif; ?>

  <input type="hidden" name="deleted_images" id="deleted_images" value="[]">

  <label for="images_sup" class="form-label">Ajouter des images supplémentaires :</label>
  <input type="file" id="images_sup" name="images_sup[]" class="form-file-input" multiple accept="image/*">

  <button type="submit" class="form-submit-btn">💾 Enregistrer</button>
</form>

<!-- Bouton retour -->
<div class="category-actions-top">
  <a href="index.php?page=admin" class="btn btn-secondary">← Retour au tableau de bord</a>
</div>
