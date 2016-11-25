<?php
$id = $_GET['id_signalement'];
$query = "DELETE FROM error WHERE id=$id";

include '../../#/connection.php';
mysqli_query($connection, $query)or die ("Query failed: '$query'");
mysqli_close($connection);

header("Location: ../signalement.php");
?>
