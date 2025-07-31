<!-- Header avec Bootstrap -->
<nav class="navbar navbar-expand-lg navbar-light bg-light px-3 rounded-bottom shadow-sm">
  <a class="navbar-brand d-flex align-items-center" href="index.php?page=accueil">
    <img src="img/logo/file3.png" alt="Logo" height="50" class="me-2">
  </a>

  <!-- Bouton responsive -->
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
    <span class="navbar-toggler-icon"></span>
  </button>

  <!-- Menu + recherche -->
  <div class="collapse navbar-collapse" id="navMenu">
    <ul class="navbar-nav ms-auto mb-2 mb-lg-0 me-3">
      <li class="nav-item">
        <a class="nav-link" href="index.php?page=accueil">Accueil</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="index.php?page=produit">Produit</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="index.php?page=panier">Panier</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="index.php?page=contact">Contact</a>
      </li>

      <?php if (isset($_SESSION['user_id'])) : ?>
        <li class="nav-item">
          <a class="nav-link" href="index.php?page=admin">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php?page=logout">Déconnexion</a>
        </li>
      <?php endif; ?>
    </ul>

    <!-- Barre de recherche -->
    <form class="search-wrapper d-flex position-relative" action="index.php" method="get" autocomplete="off">
      <input type="hidden" name="page" value="rechercher">
      <input 
        class="form-control me-2" 
        type="search" 
        id="live-search-input" 
        name="m" 
        placeholder="Recherche" 
        aria-label="Search"
      >
      <div id="search-suggestions" class="search-suggestions dropdown-menu"></div>
      <button class="btn btn-outline-primary" type="submit">🔍</button>
    </form>  

  </div>
</nav>
