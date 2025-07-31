<h2 class="category-admin-title">Gestion des catégories</h2>

<!-- Formulaire d'ajout -->
<form method="POST" enctype="multipart/form-data" class="category-form">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
  <input type="hidden" name="action" value="add">

  <label for="name">Nom de la catégorie :</label>
  <input type="text" id="name" name="nom" required>

  <label for="image">Image (facultative) :</label>
  <input type="file" id="image" name="image" accept="image/*">

  <button type="submit" class="category-submit-button">➕ Ajouter</button>
</form>

<?php if (!empty($categories)): ?>
  <table class="category-table">
    <thead>
      <tr>
        <th >Nom</th>
        <th>Image</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($categories as $categorie): ?>
        <tr>
          <!-- Colonne Nom + Formulaire update -->
          <td>
            <form method="POST" action="index.php?page=categories" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 0.5em;">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="id" value="<?= htmlspecialchars($categorie['id']) ?>">
              <input type="text" name="nom" value="<?= htmlspecialchars($categorie['nom']) ?>" required style="width: 100%; padding: 6px; font-size: 1em;">
          </td>

          <!-- Colonne Image + input fichier -->
          <td>
            <?php if (!empty($categorie['image']) && file_exists($categorie['image'])): ?>
              <img src="<?= htmlspecialchars($categorie['image']) ?>" alt="Image catégorie" class="category-image" style="margin-bottom: 5px;">
            <?php else: ?>
              <span class="category-no-image">Pas d'image</span>
            <?php endif; ?>
            <input type="file" name="image" accept="image/*" style="display: block;">
          </td>

          <!-- Colonne Actions -->
          <td class="category-action-cell">
            <button type="submit" class="btn btn-primary" style="padding: 6px 12px; font-size: 1em; cursor: pointer;">💾 Mettre à jour</button>
            </form>

            <form method="POST" action="index.php?page=categories" onsubmit="return confirm('Confirmer la suppression ?');" style="margin-top: 0.5em;">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="categorie_id" value="<?= htmlspecialchars($categorie['id']) ?>">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
              <button type="submit" class="category-delete-button">🗑️ Supprimer</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?>
  <p class="category-empty-message">Aucune catégorie trouvée.</p>
<?php endif; ?>

<!-- Bouton retour -->
<div class="category-actions-top">
  <a href="index.php?page=admin" class="btn btn-secondary">← Retour au tableau de bord</a>
</div>
