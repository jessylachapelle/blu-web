<?php
if (isset($_POST['email']) && isset($_POST['memberNo']) &&
    (strlen($_POST['memberNo']) == 7 || strlen($_POST['memberNo']) == 9)) {
  $memberNo = $_POST['memberNo'];
  $email = $_POST['email'];

  $query = "SELECT no, courriel AS email
            FROM membre
            WHERE no LIKE '%$memberNo'
            AND courriel='$email'";

  include "../#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
  $row = mysqli_fetch_assoc($result);

  if($row['no']) {
    session_start ();

		$_SESSION['email'] = $row['email'];
		$_SESSION['memberNo'] = $row['no'];
		$_SESSION['expire'] = null;

		if(!isset($_POST['connection'])) {
  		$_SESSION['start'] = time();
      $_SESSION['expire'] = $_SESSION['start'] + 1800;
		}
    return redirect(200);
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
