<?php $text = json_decode(file_get_contents('json/wtfaq.json'), true); ?>
<!DOCTYPE html>
<html>
	<head>
		<?php include "inc/head.php"; ?>
	</head>
	<body>
		<?php include "inc/header.php"; ?>
		<main>
			<h1><?php echo $text['title']; ?></h1>

			<?php foreach ($text['qa'] as $qa) { ?>
				<h3><?php echo $qa['question']; ?></h3>
				<p><?php echo $qa['answer']; ?></p>
			<?php } ?>

			<br/>
			<p><?php echo $text['note']; ?></p>
		</main>
		<?php include "inc/footer.php"; ?>
	</body>
</html>
