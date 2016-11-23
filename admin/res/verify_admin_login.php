<?php
if (isset($_POST['user']) && isset($_POST['password'])) {
  $user = $_POST['user'];
  $password = $_POST['password'];
  $query = "SELECT user FROM login WHERE user='$user' AND password='$password' AND admin=1";

  include "../../#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());

  while($row = mysqli_fetch_assoc($result)) {
    session_start ();

    $_SESSION['user'] = $row['user'];
    $_SESSION['expire'] = null;

    if(!isset($_POST['connection'])) {
      $_SESSION['requiredt'] = time();
      $_SESSION['expire'] = $_SESSION['requiredt'] + (1800);
    }

    return redirect(200);
  }
}
return redirect(401);

function redirect($code) {
  $url = explode('?', $_POST['url'])[0];

  if($code == 200) {
    header("Location: $url");
  } else {
    header("Location: $url?error=$code");
  }
}
?>
