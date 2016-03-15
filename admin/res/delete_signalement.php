<?php
include "../../#/connection.php";
$query = "DELETE FROM erreur WHERE id =" . $_GET['id_signalement'];
$result = mysqli_query($connection, $query)or die ("Query failed: '" . $query . "' " . mysqli_error());

header("Location: ../signalement.php");
?>
