<?php
$query = "DELETE FROM nouvelle WHERE id =" . $_GET['id_nouvelle'];

include "../../#/connection.php";
mysqli_query($connection, $query)or die ("Query failed: '" . $query . "' " . mysqli_error());
mysqli_close($connection);

header("Location: ../nouvelles.php");
?>
