<?php
session_start ();

if(isset($_SESSION['memberNo'])) {
  $no = $_SESSION['memberNo'];
  $query = "UPDATE member
            SET last_activity=CURRENT_TIMESTAMP
            WHERE no=$no;";

  include "../#/connection.php";
  mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
  mysqli_close($connection);

  return header("Location: ../index.php?renewed=true");
}

return header("Location: ../index.php");
?>
