<!DOCTYPE html>
<html>
	<head>
		<?php include "inc/head.php"; ?>
		<script src="js/search.js" defer></script>
	</head>
	<body>
		<?php include "inc/header.php"; ?>
		<main>
			<form id="search-form" method="post">
				<input id="recherche" name="search" type="search"/>
        <button id="btn-recherche" type="submit">Rechercher</button>
			</form>
			<section id="resultat"></section>
		</main>
		<?php include "inc/footer.php"; ?>
	</body>
</html>
