<?php
$id = $_GET['id_nouvelle'];
$query = "DELETE FROM news WHERE id=$id";

include '../../#/connection.php';
mysqli_query($connection, $query)or die ("Query failed: '$query'");
mysqli_close($connection);

header("Location: ../nouvelles.php");
?>
