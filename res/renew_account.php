<?php
function renewAccount() {
  session_start ();

  if (!isset($_SESSION['memberNo'])) {
    return [ 'code' => 403, 'message' => 'not_logged_in' ];
  }

  $no = $_SESSION['memberNo'];
  $query = "UPDATE member
            SET last_activity=CURRENT_TIMESTAMP
            WHERE no=$no;";

  include '../#/connection.php';
  mysqli_query($connection, $query) or die("Query failed: '$query'");
  mysqli_close($connection);

  return [ 'code' => 200, 'message' => 'success' ];
}

echo json_encode(renewAccount());
?>
