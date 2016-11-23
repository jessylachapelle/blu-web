<?php
if (isset($_POST['search-data'])) {
  $results = searchItems($_POST['search-data']);
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
            WHERE name LIKE '%$searchData%'
            OR id IN(SELECT item
                     FROM item_author
                     INNER JOIN author
                      ON item_author.author=author.id
                     WHERE first_name LIKE '%$searchData%'
                     OR last_name LIKE '%$searchData%')";

  include '../#/connection.php';
  $result = mysqli_query($connection, $query) or die ("Query failed: '$query'");
  $items = [];

  while($row = mysqli_fetch_assoc($result)) {
    $items[$row['id']] = [
      'id' => $row['id'],
      'name' => $row['name'],
      'author' => getAuthors($row['id']),
      'editor' => $row['editor'],
      'edition' => $row['edition'],
      'publication' => $row['publication'],
      'status' => $row['status']
    ];
  }

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
