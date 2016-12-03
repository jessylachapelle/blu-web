<?php
require_once 'res/article.php';

function getArticle($itemId) {
  $item = new Article();

  $query = "SELECT name, publication, edition, editor, `ean13`
            FROM item WHERE id=$itemId;";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");
  $row = mysqli_fetch_assoc($result);

  $item->setId($itemId);
  $item->setTitle($row['name']);
  $item->setEditor($row['editor']);
  $item->setEdition($row['edition']);
  $item->setYear($row['publication']);
  $item->setCode($row['ean13']);

  $query = "SELECT first_name, last_name
            FROM item_author
            INNER JOIN author
              ON item_author.author=author.id
            WHERE item=$itemId;";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");

  $author = "";
  while($row = mysqli_fetch_assoc($result)) {
    $firstName = $row['first_name'];
    $lastName = $row['last_name'];
    $author = $firstName != null ? "$firstName $lastName; " : "$lastName; ";
  }

  $item->setAuthor($author);
  mysqli_close($connection);
  return $item;
}

function getArticleStats($articleId) {
  $stats = [];
  $query = "SELECT SUM(copy.price) AS amount,
                   COUNT(copy.id) AS quantity
            FROM copy
            INNER JOIN transaction
              ON transaction.copy=copy.id
            WHERE copy.item=$articleId
              AND transaction.copy NOT IN(SELECT transaction.copy
                                          FROM transaction
                                          WHERE type IN (SELECT id
                                                         FROM transaction_type
                                                         WHERE code LIKE 'SELL%')";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");
  $row = mysqli_fetch_assoc($result);

  if ($row['quantity'] > 0) {
    $stats['quantity'] = $row['quantity'];
    $stats['amount'] = $row['amount'];
    $stats['average'] = round($row['amount'] / $row['quantity']);
  } else {
    $stats['quantity'] = 0;
  }

  mysqli_close($connection);
  return $stats;
}

function isSubscribed($memberNo, $itemId) {
  $query = "SELECT COUNT(item) AS count
            FROM item_feed
            WHERE member=$memberNo
            AND item=$itemId;";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");
  $row = mysqli_fetch_assoc($result);
  $count = $row['count'];

  mysqli_close($connection);
  return $count != 0;
}

function isConnected() {
  return isset($_SESSION['email']) && isset($_SESSION['memberNo']) &&
          ($_SESSION['expire'] == null || time() < $_SESSION['expire']);
}

function getState() {
  return isSubscribed($_SESSION['memberNo'], $_GET['article']) ? 'subscribed' : 'unsubscribed';
}
?>
