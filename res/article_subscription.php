<?php
if (isset($_POST['f'])) {
  switch ($_POST['f']) {
    case "subscribe":
      echo subscribe($_POST['memberNo'], $_POST['articleId']);
      break;
    case "unsubscribe":
      echo unsubscribe($_POST['memberNo'], $_POST['articleId']);
      break;
  }
}

function subscribe($memberNo, $articleId) {
  $query = "INSERT INTO item_feed(member, item) VALUES ($memberNo, $articleId);";

  include "../#/connection.php";
  $result = mysqli_query($connection, $query) or die ("Query failed: '$query' " . mysqli_error($connection));
  mysqli_close($connection);
  return true;
}

function unsubscribe($memberNo, $articleId) {
  $query = "DELETE FROM item_feed WHERE member=$memberNo AND item=$articleId;";

  include "../#/connection.php";
  $result = mysqli_query($connection, $query) or die ("Query failed: '$query' " . mysqli_error($connection));
  mysqli_close($connection);
  return true;
}
?>
