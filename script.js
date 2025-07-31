
document.addEventListener('DOMContentLoaded', function () {

    // --- Gestion des stocks ---
    document.querySelectorAll('.stock-indicator').forEach(stockEl => {
        const stock = parseInt(stockEl.dataset.stock);
        if (isNaN(stock)) return;

        if (stock <= 0) {
            stockEl.classList.add('stock-zero');
        } else {
            stockEl.classList.add('stock-dispo');
        }
    });

    // --- Initialisation des Swipers (produits) ---
    function initAllSwipers() {
        document.querySelectorAll('.swiper').forEach(swiperEl => {
            const slideCount = swiperEl.querySelectorAll('.swiper-slide').length;
            if (slideCount < 1) return;

            const pagination = swiperEl.querySelector('.swiper-pagination');
            const nextBtn = swiperEl.querySelector('.swiper-button-next');
            const prevBtn = swiperEl.querySelector('.swiper-button-prev');

            new Swiper(swiperEl, {
                loop: slideCount > 1,
                speed: 500,
                slidesPerView: 1,
                spaceBetween: 10,
                observer: true,
                observeParents: true,
                navigation: nextBtn && prevBtn ? {
                    nextEl: nextBtn,
                    prevEl: prevBtn,
                } : false,
                pagination: pagination ? {
                    el: pagination,
                    clickable: true,
                } : false,
                autoplay: slideCount > 1 ? {
                    delay: 4000,
                    disableOnInteraction: false,
                } : false,
            });
        });
    }

    initAllSwipers();

    // --- Suppression d'image (admin) ---
    const deletedImagesInput = document.getElementById('deleted_images');
    if (deletedImagesInput) {
        let deletedImages = [];

        document.querySelectorAll('.delete-image-btn').forEach(button => {
            button.addEventListener('click', function () {
                const imgUrl = this.getAttribute('data-img');
                if (!deletedImages.includes(imgUrl)) {
                    deletedImages.push(imgUrl);
                    deletedImagesInput.value = JSON.stringify(deletedImages);
                }

                const container = this.closest('.image-container');
                if (container) container.remove();
            });
        });
    }

    // --- Calculs panier (quantité, total) ---
    const quantityInputs = document.querySelectorAll('.cart-input');
    const totalCell = document.getElementById('cartTotal');

    function formatFCFA(value) {
        return value.toLocaleString('fr-FR', { maximumFractionDigits: 0 }) + ' FCFA';
    }

    function updateTotals() {
        let total = 0;
        document.querySelectorAll('.cart-item-row').forEach(row => {
            const price = parseFloat(row.dataset.price);
            const input = row.querySelector('.cart-input');
            const max = parseInt(input.getAttribute('max')) || Infinity;

            let quantity = parseInt(input.value);
            if (isNaN(quantity) || quantity < 1) quantity = 1;
            else if (quantity > max) quantity = max;

            input.value = quantity;

            const itemTotal = price * quantity;
            row.querySelector('.cart-item-total').textContent = formatFCFA(itemTotal);
            total += itemTotal;
        });

        if (totalCell) {
            totalCell.textContent = formatFCFA(total);
        }
    }

    quantityInputs.forEach(input => {
        input.addEventListener('input', updateTotals);
        input.addEventListener('change', () => {
            const form = input.closest('form');
            if (form) form.submit();
        });
    });

    // --- Animation délai fade-in ---
    document.querySelectorAll('.fade-in').forEach(el => {
        const delay = el.getAttribute('data-delay') || '0s';
        el.style.animationDelay = delay;
    });

    // --- Live search ---
    const input = document.getElementById('live-search-input');
    const suggestions = document.getElementById('search-suggestions');
    let controller;

    if (input && suggestions) {
        input.addEventListener('input', function () {
            const term = this.value.trim();
            if (term.length < 2) {
                suggestions.innerHTML = '';
                suggestions.style.display = 'none';
                return;
            }

            if (controller) controller.abort();
            controller = new AbortController();

            fetch(`controleurs/ajax_search.php?q=${encodeURIComponent(term)}`, {
                signal: controller.signal
            })
                .then(response => response.json())
                .then(data => {
                    suggestions.innerHTML = '';
                    if (!Array.isArray(data) || data.length === 0) {
                        suggestions.style.display = 'none';
                        return;
                    }

                    data.forEach(prod => {
                        const item = document.createElement('a');
                        item.classList.add('dropdown-item');
                        item.href = `index.php?page=details&id=${encodeURIComponent(prod.id)}`;
                        item.innerHTML = `
                            <strong>${escapeHtml(prod.nameP)}</strong><br>
                            <small>${escapeHtml(prod.categorie_nom ?? '')} - ${Number(prod.prix).toLocaleString()} FCFA</small>
                        `;
                        suggestions.appendChild(item);
                    });

                    suggestions.style.display = 'block';
                })
                .catch(err => {
                    if (err.name !== 'AbortError') {
                        console.error('Erreur AJAX recherche :', err);
                    }
                });
        });

        document.addEventListener('click', (e) => {
            if (!suggestions.contains(e.target) && e.target !== input) {
                suggestions.style.display = 'none';
            }
        });

        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }
    }

    // --- Filtrage des catégories ---
    const toggleBtn = document.getElementById('toggleCategories');
    const categoriesSection = document.getElementById('categoriesSection');
    const productsSection = document.getElementById('productsSection');
    const productsTitle = document.getElementById('productsTitle');
    const productCards = document.querySelectorAll('.product-card');
    let categoriesVisible = false;

    function showAllProducts() {
        productCards.forEach(card => card.style.display = 'block');
        productsTitle.textContent = 'Tous les produits';
    }

    function filterByCategory(catId, catName) {
        productCards.forEach(card => {
            const prodCatId = card.getAttribute('data-category-id');
            card.style.display = prodCatId === catId ? 'block' : 'none';
        });
        productsTitle.textContent = `Produits dans la catégorie : ${catName}`;
    }

    if (toggleBtn && categoriesSection && productsTitle) {
        toggleBtn.addEventListener('click', () => {
            categoriesVisible = !categoriesVisible;

            if (categoriesVisible) {
                categoriesSection.classList.remove('hidden-after-out');
                toggleBtn.textContent = 'Voir tous les produits';
            } else {
                categoriesSection.classList.add('hidden-after-out');
                showAllProducts();
                toggleBtn.textContent = 'Filtrer';
            }
        });

        document.querySelectorAll('.category-card').forEach(card => {
            card.addEventListener('click', e => {
                e.preventDefault();
                const catId = card.dataset.categoryId;
                const catName = card.dataset.categoryName || 'Non défini';
                filterByCategory(catId, catName);
                categoriesSection.classList.add('hidden-after-out');
                toggleBtn.textContent = 'Filtrer';
                categoriesVisible = false;
            });
        });
    }

});

