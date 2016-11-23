<!DOCTYPE html>
<html>
	<head>
		<?php include "inc/head.php"; ?>
		<title>Banque de livres usagés</title>
	</head>
	<body>
		<?php include "inc/header.php"; ?>
		<main>
			<form id="search-form" method="post">
				<input id="recherche" name="search" type="search"/>
				<div class="radio-group">
					<input id="titre" type="radio" name="filtre" value="titre" />
	        <label for="titre">Titre</label>

					<input id="auteur" type="radio" name="filtre" value="auteur" />
	        <label for="auteur">Auteur</label>

					<input id="editeur" type="radio" name="filtre" value="editeur" />
	        <label for="editeur">Éditeur</label>

					<input id="tout" type="radio" name="filtre" value="tout" checked="" />
	        <label for="tout">Tout</label>
				</div>
        <button id="btn-recherche" type="submit">Rechercher</button>
			</form>
			<section id="resultat"></section>
		</main>
		<?php include "inc/footer.php"; ?>
	</body>
</html>
