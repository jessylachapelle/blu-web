<?php
session_start ();

if(isset($_SESSION['memberNo'])) {
  renouveleCompte($_SESSION['memberNo']);
  header("Location: ../index.php?renouvele=true");
} else {
  header("Location: ../index.php");
}
?>


<?php
function renouveleCompte($memberNo) {
  $date = date("Y-m-d");
  $query = "UPDATE membre SET derniere_activite='$date' WHERE no=$memberNo";
  
  include "../#/connection.php";
  mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
  mysqli_close($connection);
} 
?>