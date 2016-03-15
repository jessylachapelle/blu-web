<?php session_start(); ?>
<!DOCTYPE html>
<html>
	<head>
		<?php include "inc/head.php"; ?>
	</head>
	<body>
		<?php include "inc/header.php"; ?>
		<main>
			<?php
      if (isset($_SESSION['courriel']) && isset($_SESSION['nodossier']) &&
					($_SESSION['expire'] == null || time() < $_SESSION['expire'])) {
				include 'view/ui_member.php';
			} else {
				include 'view/ui_login.php';
			}
      ?>
		</main>
		<?php include "inc/footer.php"; ?>
	</body>
</html>
