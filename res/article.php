<?php
require_once 'res/article.php';

function getArticle($itemId) {
  $item = new Article();

  $query = "SELECT id, name, publication, edition, editor, `ean13` FROM item WHERE id = ?;";

  include '#/connection.php';
  $statement = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($statement,'i', $itemId);
  
  mysqli_stmt_execute($statement);
  mysqli_stmt_bind_result($statement, $id, $name, $publication, $edition, $editor, $ean13);
  mysqli_stmt_fetch($statement);

  $item->setId($id);
  $item->setTitle($name);
  $item->setEditor($editor);
  $item->setEdition($edition);
  $item->setYear($publication);
  $item->setCode($ean13);

  mysqli_stmt_close($statement);

  $query = "SELECT first_name, last_name
            FROM item_author
            INNER JOIN author
              ON item_author.author=author.id
            WHERE item=$id;";
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

function getArticleStats($itemId) {
  $stats = [];
  $query = "SELECT SUM(copy.price) AS amount,
                   COUNT(copy.id) AS quantity
            FROM copy
            WHERE copy.item=$itemId
              AND copy.id NOT IN(SELECT copy.id
                                 FROM copy
                                 INNER JOIN transaction
                                  ON copy.id=transaction.copy
                                 INNER JOIN transaction_type
                                   ON transaction.type=transaction_type.id
                                 WHERE copy.item=$itemId
                                 AND transaction_type.code LIKE 'SELL%')";

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
