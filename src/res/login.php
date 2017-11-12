<?php
include "../#/connection.php";

if (isset($_POST['email']) && isset($_POST['memberNo']) &&
    (strlen($_POST['memberNo']) == 7 || strlen($_POST['memberNo']) == 9)) {
  $query = "SELECT no, email FROM member WHERE no LIKE ? AND email = ?;";

  $connection = getConnection();
  $statement = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($statement,'ss', $password, $username);

  $password = '%' . strip_tags($_POST['memberNo']);
  $username = strip_tags($_POST['email']);

  mysqli_stmt_execute($statement);
  mysqli_stmt_bind_result($statement, $no, $email);
  mysqli_stmt_fetch($statement);

  mysqli_stmt_close($statement);
  mysqli_close($connection);

  if ($no) {
    session_start ();

		$_SESSION['email'] = $email;
		$_SESSION['memberNo'] = $no;
		$_SESSION['expire'] = null;

		if (!isset($_POST['connection'])) {
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
