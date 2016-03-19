<?php
session_start ();

if(isset($_SESSION['memberNo'])) {
  $query = "UPDATE membre
            SET derniere_activite=CURRENT_TIMESTAMP
            WHERE no=" . $_SESSION['memberNo'];

  include "../#/connection.php";
  mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
  mysqli_close($connection);

  return header("Location: ../index.php?renewed=true");
}

return header("Location: ../index.php");
?>
