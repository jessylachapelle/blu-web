<?php
if (isset($_POST['courriel']) && isset($_POST['nodossier']) && (strlen($_POST['nodossier']) == 7 || strlen($_POST['nodossier']) == 9)) {
  $nodossier = $_POST['nodossier'];
  $query = "SELECT no, courriel FROM membre WHERE no LIKE '%$nodossier'";

  include "../#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());

  while($row = mysqli_fetch_assoc($result)) {
    if($_POST['courriel'] == $row['courriel']) {
      session_start ();

  		$_SESSION['courriel'] = $row['courriel'];
  		$_SESSION['nodossier'] = $row['no'];
  		$_SESSION['expire'] = null;

  		if(!isset($_POST['connexion'])) {
    		$_SESSION['requiredt'] = time();
        $_SESSION['expire'] = $_SESSION['requiredt'] + (1800);
  		}
      return redirect(200);
    }
  }
}
return redirect(401);

function redirect($code) {
  if($code == 200) {
    header("Location: ../index.php");
  } else {
    header("Location: ../index.php?error=$code");
  }
}
?>
