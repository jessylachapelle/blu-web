<?php
$requete = "DELETE FROM nouvelle WHERE id =" . $_GET['id_nouvelle'];

include "../../#/connection.php";
mysqli_query($connection, $requete)or die ("Query failed: '" . $requete . "' " . mysqli_error());
mysqli_close($connection);

header("Location: ../nouvelles.php");
?>
