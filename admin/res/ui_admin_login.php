<h1>Compte admin</h1>
<?php
if(isset($_GET['error']) && $_GET['error'] == '401') {
  echo "<p id='error'>Erreur de connexion, veuillez vérifier votre nom d'utilisateur et votre mot de passe</p>";
}
?>
<form action="res/verify_admin_login.php" method="post">
  <input id="url" name="url" type="hidden" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />
  <label for="user">Utilisateur :</label>
  <input id="user" name="user" type="text" required="" />
  <label for="password">Mot de passe :</label>
  <input id="password" name="password" type="password" required="" />
  <div class="checkbox-container">
    <input id="connexion" name="connexion" type="checkbox" value="connexion" />
    <label for="connexion">Garder ma session active</label>
  </div>
  <button id="connexion">Connexion</button>
</form>
