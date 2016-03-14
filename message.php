<!DOCTYPE html>
<html>
	<head>
		<?php include "inc/head.php"; ?>
		<title>Banque de livres usagés</title>
	</head>
	<body>
		<?php include "inc/header.php"; ?>
		<main id="nouvelle">
			<h1>Nouvelles de la BLU</h1>
			
	<?php
	  $connection = mysqli_connect('localhost', 'root', '', 'blu') or die ('Could not connect to server');
	  mysqli_set_charset($connection, "utf8");
	  $requete = 'SELECT * FROM nouvelle ORDER BY id DESC;';
	  $resultat = mysqli_query($connection, $requete)or die ("Query failed: '" . $requete . "' " . mysqli_error());;
	  
	    for ($i=0; $i<5; $i++) 
	    {
	      if($donnees = mysqli_fetch_array($resultat))
	      {
	?>
	    <h2><?php echo $donnees['titre']; ?></h2>
	    <p><?php echo $donnees['message']; ?></p>
	    <p class="pdate"><?php echo $donnees['debut']; ?></p>	
	    <hr/>
	<?php
	   }
	  }
	?>

		</main>
		<?php include "inc/footer.php"; ?>
	</body>
</html>