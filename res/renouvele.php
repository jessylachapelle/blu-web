<?php
session_start ();

if(isset($_SESSION['nodossier'])) {
  renouveleCompte($_SESSION['nodossier']);
  header("Location: ../index.php?renouvele=true");
} else {
  header("Location: ../index.php");
}
?>


<?php
function renouveleCompte($noMembre) {
  $date = date("Y-m-d");
  $query = "UPDATE membre SET derniere_activite='$date' WHERE no=$noMembre";
  
  include "../#/connection.php";
  mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
  mysqli_close($connection);
} 
?>