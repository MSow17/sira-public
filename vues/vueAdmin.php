<h1 class="admin-title">Panneau d'administration</h1>

<p class="admin-welcome">Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?> !</p>

<div class="admin-actions">
    <a href="index.php?page=add_article" class="btn btn-primary">➕ Ajouter un article</a>
    <a href="index.php?page=categories" class="btn btn-secondary">📂 Gérer les catégories</a>
    <a href="index.php?page=logout" class="btn btn-danger logout-button">🚪 Déconnexion</a>
</div>

<h2 class="admin-subtitle">Liste des articles</h2>

<?php if (!empty($produits)): ?>
    <table class="admin-table">
        <thead>
            <tr>
                <!-- ID masqué à l'affichage mais utilisé -->
                <th class="admin-th" style="display:none;">ID</th>
                <th class="admin-th">Nom</th>
                <th class="admin-th">Catégorie</th>
                <th class="admin-th">Prix</th>
                <th class="admin-th">Stock</th>
                <th class="admin-th">Image</th>
                <th class="admin-th">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $produit): ?>
                <tr class="admin-tr">
                    <!-- ID masqué à l'affichage -->
                    <td style="display:none;"><?= htmlspecialchars($produit['id']) ?></td>

                    <td class="admin-td"><?= htmlspecialchars($produit['nameP']) ?></td>
                    <td class="admin-td"><?= htmlspecialchars($produit['categorie_nom'] ?? 'Non catégorisé') ?></td>
                    <td class="admin-td"><?= number_format($produit['prix'], 0, ',', ' ') ?> FCFA</td>
                    <td class="admin-td"><?= htmlspecialchars($produit['stock'] ?? '0') ?></td>
                    <td class="admin-td">
                        <?php if (!empty($produit['imageP'])): ?>
                            <img src="<?= htmlspecialchars($produit['imageP']) ?>" alt="Image" class="admin-image">
                        <?php else: ?>
                            <span class="admin-no-image">Pas d'image</span>
                        <?php endif; ?>
                    </td>
                    <td class="admin-td admin-actions-cell">
                        <a href="index.php?page=edit_article&id=<?= urlencode($produit['id']) ?>" class="admin-edit-link">✏️ Modifier</a>
                        <form method="POST" action="index.php?page=delete_article" class="admin-delete-form" onsubmit="return confirm('Confirmer la suppression de l\'article ID <?= $produit['id'] ?> ?');">
                            <input type="hidden" name="id" value="<?= $produit['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                            <button type="submit" class="admin-delete-button">🗑️ Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="admin-empty-message">Aucun article trouvé.</p>
<?php endif; ?>
