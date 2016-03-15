<!DOCTYPE html>
<html>
	<head>
		<?php include "inc/head.php"; ?>
	</head>
	<body>
		<?php include "inc/header.php"; ?>
		<main id="nouvelle">
			<h1>Nouvelles de la BLU</h1>

			<?php
		  $query = "SELECT titre, message, debut
								FROM nouvelle
								WHERE CURRENT_DATE BETWEEN debut AND fin
								ORDER BY debut DESC, fin DESC, titre ASC";

			include "#/connection.php";
		  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());

			while($row = mysqli_fetch_array($result)) { ?>
				<h2><?php echo $row['titre']; ?></h2>
		    <p><?php echo $row['message']; ?></p>
		    <p class="pdate"><?php echo $row['debut']; ?></p>
		    <hr/>
			<?php } ?>
		</main>
		<?php include "inc/footer.php"; ?>
	</body>
</html>
