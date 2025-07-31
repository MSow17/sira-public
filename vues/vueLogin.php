<h2>Connexion</h2>

<form method="POST" class="login-form">
  <div class="form-group">
    <label for="username" class="form-label">Nom d'utilisateur :</label>
    <input type="text" name="username" id="username" class="form-input" required>
  </div>

  <div class="form-group">
    <label for="password" class="form-label">Mot de passe :</label>
    <input type="password" name="password" id="password" class="form-input" required>
  </div>

  <button type="submit" class="login-button">Se connecter</button>

  <?php if (isset($error)): ?>
    <p class="login-error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>
</form>
