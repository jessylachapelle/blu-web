<!DOCTYPE html>
<html>
	<head>
		<?php include "inc/head.php"; ?>
	</head>
	<body>
		<?php include "inc/header.php"; ?>
		<main>
			<h1>Contactez-nous</h1>
			<p>Pour obtenir des informations sur les services de la Banque de livres usagés, vous pouvez vous présenter en personne au local de l’association étudiante ou nous envoyer un email :</p>
			<form action="res/sendmail.php" method="post" id="contact-form">
				<div class="fieldWrapper">
					<label for="name">Nom<span class="required">*</span></label>
					<input id="name" name ="name" type="text" required="" />
				</div>
				<div class="fieldWrapper">
					<label for="email">Adresse courriel<span class="required">*</span></label>
					<input id="email" name="email" type="email" required="" />
				</div>
				<div class="fieldWrapper">
					<label for="subject">Sujet<span class="required">*</span></label>
					<input id="subject" name ="subject" type="text" required="" />
				</div>
				<label for="message">Message<span class="required">*</span></label>
				<textarea id="message" name="message"  rows="10" cols="50" required=""></textarea>

				<button type="submit">Envoyer</button>
			</form>
		</main>
		<?php include "inc/footer.php"; ?>
	</body>
</html>
