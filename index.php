<?php session_start (); ?>
<!DOCTYPE html>
<html>
	<head>
		<?php include "inc/head.php"; ?>
		<title>Banque de livres usagés</title>
	</head>
	<body>
		<?php include "inc/header.php"; ?>
		<main>
			<?php
  		$now = time();

      if(isset($_SESSION['courriel']) && isset($_SESSION['nodossier']) && ($_SESSION['expire'] == null || $now < $_SESSION['expire']))
        include 'res/membre_fiche.php';
      else
        include 'res/membre_login.php';
      ?>
		</main>
		<?php include "inc/footer.php"; ?>
	</body>
</html>
