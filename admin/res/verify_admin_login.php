<?php
if (isset($_POST['user']) && isset($_POST['password'])) {
  $user = $_POST['user'];
  $password = $_POST['password'];
  $query = "SELECT username FROM employee
            WHERE username='$user'
            AND password='$password'
            AND admin=1
            AND active=1;";

  include '../../#/connection.php';
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");

  while($row = mysqli_fetch_assoc($result)) {
    session_start ();

    $_SESSION['user'] = $row['user'];
    $_SESSION['expire'] = null;

    if(!isset($_POST['connection'])) {
      $_SESSION['requiredt'] = time();
      $_SESSION['expire'] = $_SESSION['requiredt'] + (1800);
    }

    return redirect(200, $_POST['url']);
  }
}

return redirect(401, $_POST['url']);

function redirect($code, $POST_URL) {
  $url = explode('?', $POST_URL)[0];

  if($code == 200) {
    header("Location: $url");
  } else {
    header("Location: $url?error=$code");
  }
}
?>
