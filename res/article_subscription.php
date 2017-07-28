<?php
function handleSubscription($query, $memberNo, $itemId) {
  include '../#/connection.php';
  $statement = mysqli_prepare($connection, $query);

  mysqli_stmt_bind_param($statement,'ii', $memberNo, $itemId);
  mysqli_stmt_execute($statement);

  mysqli_stmt_close($statement);
  mysqli_close($connection);
  return true;
}

$subscription = [
  'subscribed' => function($memberNo, $itemId) {
    $query = "INSERT INTO item_feed(member, item) VALUES (?, ?);";
    return handleSubscription($query, $memberNo, $itemId);
  },

  'unsubscribe' => function($memberNo, $itemId) {
    $query = "DELETE FROM item_feed WHERE member = ? AND item = ?;";
    return handleSubscription($query, $memberNo, $itemId);
  }
];

if (isset($_POST['f']) && ($_POST['f'] === 'subscribed' || $_POST['f'] === 'unsubscribe')) {
  echo $subscription[$_POST['f']]($_POST['memberNo'], $_POST['itemId']);
}
?>
