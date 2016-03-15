<h1>Compte membre</h1>
<p>Pour consulter la page sommaire de votre compte, et ainsi savoir si nous vous devons de l’argent, veuillez vous identifier à l’aide de votre adresse courriel et de votre numéro de dossier.</p>
<?php
if(isset($_GET['error']) && $_GET['error'] == '401') {
    echo "<p id='error'>Erreur de connexion, veuillez vérifier votre adresse courriel et votre numéro de dossier</p>";
}?>
<form action="res/login.php" method="post">
	<label for="courriel">Adresse courriel :</label>
	<input id="courriel" name="courriel" type="text" required="" />
	<label for="nodossier">Numéro de dossier* :</label>
	<input id="nodossier" name="nodossier" type="password" required="" />
  <div class="checkbox-container">
    <?php
    if($device->isMobile()) {
      echo '<input id="connexion" name="connexion" type="checkbox" value="connexion" checked="checked" />';
    } else {
      echo '<input id="connexion" name="connexion" type="checkbox" value="connexion" />';
    }
    ?>
    <label for="connexion">Garder ma session active</label>
  </div>
  <button id="connexion">Connexion</button>
</form>
