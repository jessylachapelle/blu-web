<?php
$subscription = [
  'subscribed' => function($memberNo, $itemId) {
    $query = "INSERT INTO item_feed(member, item) VALUES ($memberNo, $itemId);";

    include "../#/connection.php";
    mysqli_query($connection, $query) or die ("Query failed: '$query'");
    mysqli_close($connection);
    return true;
  },

  'unsubscribe' => function($memberNo, $itemId) {
    $query = "DELETE FROM item_feed WHERE member=$memberNo AND item=$itemId;";

    include "../#/connection.php";
    mysqli_query($connection, $query) or die ("Query failed: '$query'");
    mysqli_close($connection);
    return true;
  }
];

if (isset($_POST['f'])) {
  echo $subscription[$_POST['f']]($_POST['memberNo'], $_POST['itemId']);
}
?>
