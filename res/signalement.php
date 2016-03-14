<?php
if (isset($_POST['signaler']) && isset($_POST['article']) && isset($_POST['membre'])) {
  $erreur = $_POST["signaler"];
  $no_membre = $_POST['membre'];
  $id_article = $_POST["article"];
  $date = date("Y-m-d H:i:s");

  $query = "INSERT INTO erreur (description, no_membre, id_article, date)
            VALUES ('$erreur', $no_membre, $id_article, '$date')";

  include "../#/connection.php";
  mysqli_query($connection, $query) or die ("Query failed: '"  . $query . "' " . mysqli_error());
  mysqli_close($connection);

  return redirect($id_article, 200);
}

return redirect($_POST['article'], 403);

function redirect($article, $code) {
  if ($code == 200) {
    header("Location: ../article.php?article=$article&signaler=true");
  } else {
    header("Location: ../article.php?article=$article&erreur=$code");
  }
}
?>
