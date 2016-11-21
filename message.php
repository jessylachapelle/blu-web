<?php require_once 'res/get_messages.php' ?>
<!DOCTYPE html>
<html>
	<head>
		<?php include "inc/head.php"; ?>
	</head>
	<body>
		<?php include "inc/header.php"; ?>
		<main id="nouvelle">
			<h1>Nouvelles de la BLU</h1>

			<?php $messages = getMessages();
			foreach ($messages as $message) { ?>
				<h2><?php echo $message['title']; ?></h2>
		    <p><?php echo $message['text']; ?></p>
		    <p class="pdate"><?php echo $message['startDate']; ?></p>
		    <hr/>
			<?php } ?>
		</main>
		<?php include "inc/footer.php"; ?>
	</body>
</html>
