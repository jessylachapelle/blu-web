<?php
$name = isset($_GET['name']) ? $_GET['name'] : '';
$email = isset($_GET['email']) ? $_GET['email'] : '';
$subject = isset($_GET['subject']) ? $_GET['subject'] : '';
$message = isset($_GET['message']) ? $_GET['message'] : '';
?>
<!DOCTYPE html>
<html>
	<head>
		<?php include "inc/head.php"; ?>
	</head>
	<body>
		<?php include "inc/header.html"; ?>
		<main>
			<h1>Contactez-nous</h1>
			<?php if (isset($_GET['sent']) && $_GET['sent'] === 'true') { ?>
				<p>Merci d'avoir pris.e le temps de nous écrire. Votre message a été envoyé et sera traité dans les plus brefs délais.</p>
			<?php return; } ?>
			<?php if (isset($_GET['sent']) && $_GET['sent'] === 'false') { ?>
				<p style=" background-color: #F4F1DC; padding: 15px; margin-top: 30px;">
					Une erreur s'est produite lors de l'envoie de courriel. Si cela persiste, veuillez nous en aviser au local de l'AÉCS ou par courriel (blu@aecs.info).
				</p>
			<?php } ?>
			<p>Pour obtenir des informations sur les services de la Banque de livres usagés, vous pouvez vous présenter en personne au local de l’association étudiante ou nous envoyer un email :</p>
			<form action="res/sendmail.php" method="post" id="contact-form">
				<div class="fieldWrapper">
					<label for="name">Nom<span class="required">*</span></label>
					<input id="name" name ="name" type="text" required="" value="<?php echo $name; ?>" />
				</div>
				<div class="fieldWrapper">
					<label for="email">Adresse courriel<span class="required">*</span></label>
					<input id="email" name="email" type="email" required="" value="<?php echo $email; ?>" />
				</div>
				<div class="fieldWrapper">
					<label for="subject">Sujet<span class="required">*</span></label>
					<input id="subject" name ="subject" type="text" required="" value="<?php echo $subject; ?>" />
				</div>
				<label for="message">Message<span class="required">*</span></label>
				<textarea id="message" name="message"  rows="10" cols="50" required=""><?php echo $message; ?></textarea>

				<button type="submit">Envoyer</button>
			</form>
		</main>
		<?php include "inc/footer.php"; ?>
	</body>
</html>
