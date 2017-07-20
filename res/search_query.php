<?php
if (isset($_POST['search-data'])) {
  $results = searchItems(strip_tags($_POST['search-data']));
  echo json_encode($results);
}

function searchItems($searchData) {
  $query = "SELECT id,
                   name,
                   edition,
                   publication,
                   editor,
                   status
            FROM item
            WHERE name LIKE ?
            OR id IN(SELECT item
                     FROM item_author
                     INNER JOIN author
                      ON item_author.author=author.id
                     WHERE first_name LIKE ?
                     OR last_name LIKE ?);";

  include '../#/connection.php';
  $statement = mysqli_prepare($connection, $query);
  mysqli_stmt_bind_param($statement,'sss', $param, $param, $param);
  
  $param = "%$searchData%";

  mysqli_stmt_execute($statement);
  mysqli_stmt_bind_result($statement, $id, $name, $edition, $publication, $editor, $status);

  $items = [];
  while (mysqli_stmt_fetch($statement)) {
    $items[$id] = [
      'id' => $id,
      'name' => $name,
      'author' => getAuthors($id),
      'editor' => $editor,
      'edition' => $edition,
      'publication' => $publication,
      'status' => $status
    ];
  }

  mysqli_stmt_close($statement);
  mysqli_close($connection);
  return $items;
}

function getAuthors($itemId) {
  $query = "SELECT first_name, last_name
            FROM author
            INNER JOIN item_author
              ON author.id=item_author.author
            WHERE item_author.item=$itemId;";

  include '../#/connection.php';
  $result = mysqli_query($connection, $query) or die ("Query failed: '$query'");

  $authors = "";
  while($row = mysqli_fetch_assoc($result)) {
    $firstName = $row['first_name'];
    $lastName = $row['last_name'];

    $authors .= $firstName != '' ? "$firstName $lastName; " : "$lastName; ";
  }

  return $authors;
}
?>
