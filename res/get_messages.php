<?php
function getMessages() {
  $query = "SELECT titre AS title,
                   message AS text,
                   debut AS startDate
            FROM nouvelle
            WHERE CURRENT_DATE BETWEEN debut AND fin
            ORDER BY debut DESC, fin DESC, titre ASC";

  include "#/connection.php";
  $messages = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
  mysqli_close($connection);
  
  return $messages;
}
?>
