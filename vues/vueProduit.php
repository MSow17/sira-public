<!-- Bouton pour afficher/masquer les catégories -->
<div class="show-categories-toggle">
  <button id="toggleCategories">Filtrer</button>
</div>

<!-- Section des catégories (masquée par défaut) -->
<section class="categories-section hidden-after-out" id="categoriesSection">
  <h2>Catégories</h2>
  <div class="categories-container">
    <?php foreach ($categories as $category): ?>
      <a href="#"
         class="category-card"
         data-category-id="<?= htmlspecialchars($category['id']) ?>"
         data-category-name="<?= htmlspecialchars($category['nom']) ?>">
        <?php if (!empty($category['image'])): ?>
          <img src="<?= htmlspecialchars($category['image']) ?>" alt="Image de <?= htmlspecialchars($category['nom']) ?>" loading="lazy" class="category-image">
        <?php else: ?>
          <div class="category-image placeholder">Image manquante</div>
        <?php endif; ?>
        <h3 class="category-name"><?= htmlspecialchars($category['nom']) ?></h3>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- Section des produits -->
<section class="products-section fade-slide-in" id="productsSection">
  <h2 id="productsTitle">Tous les produits</h2>

  <?php if (!empty($products)): ?>
    <div class="products-container" id="productsContainer">
      <?php foreach ($products as $index => $product): ?>
        <div class="product-card" data-category-id="<?= htmlspecialchars($product['categorie_id']) ?>">
          <a href="index.php?page=details&id=<?= urlencode($product['id']) ?>" class="product-link">
            <article class="product">
              <div class="product-images">
                <div class="swiper product-swiper-<?= $index ?>">
                  <div class="swiper-wrapper">
                    <?php if (!empty($product['imageP'])): ?>
                      <div class="swiper-slide">
                        <img class="carousel-image" loading="lazy"
                             src="<?= htmlspecialchars($product['imageP']) ?>"
                             alt="Image principale de <?= htmlspecialchars($product['nameP'] ?? '') ?>">
                      </div>
                    <?php endif; ?>
                    <?php if (!empty($product['images'])): ?>
                      <?php foreach ($product['images'] as $image): ?>
                        <div class="swiper-slide">
                          <img class="carousel-image" loading="lazy"
                               src="<?= htmlspecialchars($image) ?>"
                               alt="Image supplémentaire de <?= htmlspecialchars($product['nameP'] ?? '') ?>">
                        </div>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </div>
                  <div class="swiper-pagination"></div>
                  <div class="swiper-button-next"></div>
                  <div class="swiper-button-prev"></div>
                </div>
              </div>
              <div class="product-details">
                <h3 class="product-name"><?= htmlspecialchars($product['nameP'] ?? '') ?></h3>
                <p class="stock-indicator" data-stock="<?= (int) $product['stock'] ?>">
                  Disponible : <?= (int) $product['stock'] ?>
                </p>
                <p class="price"><?= number_format((float) $product['prix'], 0, ',', ' ') ?> FCFA</p>
              </div>
            </article>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p>Aucun produit trouvé.</p>
  <?php endif; ?>
</section>
