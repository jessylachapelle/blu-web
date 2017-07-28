<?php
// if (isset($_POST['signal']) && isset($_POST['article']) && isset($_POST['memberNo'])) {
//   $error = $_POST['signal'];
//   $member = $_POST['memberNo'];
//   $item = $_POST['article'];

//   $query = "INSERT INTO error (description, member, item, date)
//             VALUES ('$error', $member, $item, CURRENT_TIMESTAMP)";

//   include '../#/connection.php';
//   mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
//   mysqli_close($connection);

//   return redirect($item, 200);
// }

// return redirect($_POST['article'], 403);

// function redirect($item, $code) {
//   if ($code == 200) {
//     header("Location: ../article.php?article=$item&signaled=true");
//   } else {
//     header("Location: ../article.php?article=$item&error=$code");
//   }
// }
?>
