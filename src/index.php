<?php session_start(); ?>
<!DOCTYPE html>
<html>
	<head>
		<?php include "inc/head.php"; ?>
	</head>
	<body>
		<?php include "inc/header.html"; ?>
		<main>
			<?php
      if (isset($_SESSION['email']) && isset($_SESSION['memberNo']) &&
			(!isset($_SESSION['expire']) || time() < $_SESSION['expire'])) {
			?>
				<script>const memberNo = <?php echo $_SESSION['memberNo'] ?>;</script>
			<?php include 'view/ui_member.html'; ?>
			<?php
			} else {
				session_unset();
				session_destroy();
				include 'view/ui_login.html';
			}
      ?>
		</main>
		<?php include "inc/footer.php"; ?>
	</body>
</html>
