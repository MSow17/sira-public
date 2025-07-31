<h2 class="search-title">Résultats pour : <em><?= htmlspecialchars($motCle ?? '') ?></em></h2>

<?php if (!empty($produits)): ?>
    <section class="search-results-grid">
        <?php foreach ($produits as $p): ?>
            <a href="index.php?page=details&id=<?= urlencode($p['id']) ?>" class="search-card">
                <div class="card-image-wrapper">
                    <img src="<?= htmlspecialchars($p['imageP']) ?>" alt="Image de <?= htmlspecialchars($p['nameP']) ?>">
                </div>
                <div class="card-info">
                    <h3 class="card-title"><?= htmlspecialchars($p['nameP']) ?></h3>
                    <p class="card-price"><?= number_format($p['prix'], 0, ',', ' ') ?> FCFA</p>
                </div>
            </a>
        <?php endforeach; ?>
    </section>
<?php else: ?>
    <p class="no-result-message">Aucun produit ne correspond à votre recherche.</p>
<?php endif; ?>
