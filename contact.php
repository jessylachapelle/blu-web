<!DOCTYPE html>
<html>
	<head>
		<?php include "inc/head.php"; ?>
	</head>
	<body>
		<?php include "inc/header.php"; ?>
		<main>
			<h1>Contactez-nous</h1>
			<p>Pour obtenir des informations sur les services de la Banque de livres usagés, vous pouvez vous présenter en personne au local de l’association étudiante ou nous envoyer un courriel :</p>
			<form action="res/sendmail.php" method="post" id="frmContact">
				<label for="nom">Nom<span class="required">*</span> :</label><br />
				<input id="nom" name ="nom" type="text" required="" /><br />
				<label for="courriel">Adresse courriel<span class="required">*</span> :</label><br />
				<input id="courriel" name="courriel" type="email" required="" /><br />
				<label for="sujet">Sujet<span class="required">*</span> :</label><br />
				<input id="sujet" name ="nom" type="text" required="" /><br />
				<label for="message">Message<span class="required">*</span> :</label>
				<textarea id="message" name="message"  rows="10" cols="50" required=""></textarea><br/>
				<button type="submit">Envoyer</button>
			</form>
		</main>
		<?php include "inc/footer.php"; ?>
	</body>
</html>
