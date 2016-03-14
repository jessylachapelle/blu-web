<?php
include "../../#/connection.php";
$requete = "DELETE FROM erreur WHERE id =" . $_GET['id_signalement'];
$resultat = mysqli_query($connection, $requete)or die ("Query failed: '" . $requete . "' " . mysqli_error());

header("Location: ../signalement.php");
?>
