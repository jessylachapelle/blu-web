<h1>Compte membre</h1>
<p>Pour consulter la page sommaire de votre compte, et ainsi savoir si nous vous devons de l’argent, veuillez vous identifier à l’aide de votre adresse courriel et de votre numéro de dossier.</p>
<?php if(isset($_GET['error']) && $_GET['error'] == '401') { ?>
  <p id='error'>Erreur de connexion, veuillez vérifier votre adresse courriel et votre numéro de dossier</p>
<? } ?>
<form action="res/login.php" method="post">
	<label for="email">Adresse courriel<span class="required">*</span> :</label>
	<input id="email" name="email" type="text" required="" />

	<label for="memberNo">Numéro de dossier<span class="required">*</span> :</label>
	<input id="memberNo" name="memberNo" type="password" required="" />
  <div class="checkbox-container">
    <input id="connection"
           name="connection"
           type="checkbox"
           value="connection"
           <?php if($device->isMobile()) { echo "checked='checked'"; ?> />
    <label for="connection">Garder ma session active</label>
  </div>
  <button id="connection">Connexion</button>
</form>
