<?php
function getMessages() {
  $query = "SELECT title, message, start_date FROM news
            WHERE CURRENT_DATE BETWEEN start_date AND end_date
            ORDER BY start_date DESC, end_date DESC, title ASC";

  include "#/connection.php";
  $messages = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
  mysqli_close($connection);

  return $messages;
}
?>
