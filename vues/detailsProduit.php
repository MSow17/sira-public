<section class="details-product-page">
  <div class="details-product-images">
    <div class="swiper details-product-swiper">
      <div class="swiper-wrapper">
        <?php foreach ($allImages as $img): ?>
          <div class="swiper-slide">
            <img src="<?= htmlspecialchars($img) ?>" alt="Image de <?= htmlspecialchars($produit['nameP']) ?>" class="carousel-image">
          </div>
        <?php endforeach; ?>
      </div>
      <div class="swiper-pagination"></div>
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
    </div>
  </div>

  <div class="details-product-details">
    <h2><?= htmlspecialchars($produit['nameP']) ?></h2>
    <p class="details-price"><?= number_format($produit['prix'], 0, ',', ' ') ?> FCFA</p>

    <h3>Description</h3>
    <p><?= nl2br(htmlspecialchars($produit['descriptionP'])) ?></p>

    <?php if (!empty($produit['descriptionT'])): ?>
      <h3>Description technique</h3>
      <ul class="details-description-list">
        <?php foreach (explode("\n", $produit['descriptionT']) as $line): ?>
          <?php if (trim($line) !== ''): ?>
            <li><?= htmlspecialchars(trim($line)) ?></li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <form action="index.php?page=panier" method="post" class="details-cart-form">
      <input type="hidden" name="product_id" value="<?= htmlspecialchars($produit['id']) ?>">
      <input type="number" name="quantity" value="1" min="1" max="<?= htmlspecialchars($produit['stock']) ?>" class="details-quantity-input">
      <button type="submit" class="details-add-to-cart-button">Ajouter au panier</button>
    </form>
  </div>
</section>
