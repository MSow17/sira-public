<?php
error_log('🛒 SESSION[cart] dans vuePanier : ' . print_r($_SESSION['cart'] ?? 'Non défini', true));
$cartItems = getCartItems();
error_log('🧾 Contenu de getCartItems() : ' . print_r($cartItems, true));
?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>


<section class="cart-section">
<?php $cartItems = getCartItems(); ?>
<?php if (!empty($cartItems)): ?>
    <form action="index.php?page=panier" method="post" class="cart-form">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Nom</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th>Supprimer</th> 
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                foreach ($cartItems as $itemId => $quantity): 
                    $product = getProductDetails($itemId); 
                    if ($product): 
                        $productName = htmlspecialchars($product['nameP']);
                        $productPrice = floatval($product['prix']);
                        $productImage = $product['imageP'];
                        $itemTotal = $productPrice * $quantity;
                        $total += $itemTotal;
                ?>
                    <tr class="cart-item-row" data-price="<?= $productPrice ?>">
                        <td class="cart-item-image">
                            <a href="index.php?page=details&id=<?= $itemId ?>">
                                <img src="<?= $productImage ?>" alt="<?= $productName ?>" class="cart-image">
                            </a>
                        </td>
                        <td class="cart-item-name"><?= $productName ?></td>
                        <td class="cart-item-price"><?= number_format($productPrice, 0, ',', ' ') ?> FCFA</td>
                        <td class="cart-item-quantity">
                        <input type="number"
                                name="quantities[<?= $itemId ?>]"
                                value="<?= intval($quantity) ?>"
                                min="1"
                                max="<?= $product['stock'] ?>"
                                class="cart-input"
                                required>
                        </td>
                        <td class="cart-item-total"><?= number_format($total, 0, ',', ' ') ?> FCFA</td>
                        <td class="cart-item-remove">
                            <button type="submit" name="remove_item" value="<?= $itemId ?>" class="remove-button" onclick="return confirm('Voulez-vous supprimer ce produit du panier ?')">Supprimer</button>
                        </td>
                    </tr>
                <?php endif; endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="cart-total-row">
                    <td colspan="4" class="cart-total-label">Total</td>
                    <td id="cartTotal" class="cart-total-value"><?= number_format($total, 0, ',', ' ') ?> FCFA</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </form>

    <form action="index.php?page=commande" method="post" class="order-form">
        <fieldset>
            <legend>Informations Personnelles</legend><br>

            <label for="firstName">Prénom * </label>
            <input type="text" id="firstName" name="firstName" required><br><br>

            <label for="name">Nom * </label>
            <input type="text" id="name" name="name" required><br><br>

            <label for="tel">Téléphone * </label>
            <input type="tel" id="tel" name="tel" required><br><br>

            <label for="email">Email </label>
            <input type="email" id="email" name="email"><br><br>
        </fieldset>
        <button type="submit" class="order-submit-button">Commandez</button>
    </form>
<?php else: ?>
    <p class="empty-cart-message">Aucun produit trouvé.</p>
<?php endif; ?>
</section>
