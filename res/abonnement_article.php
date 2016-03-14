<?php
if (isset($_POST['f'])) {
  switch ($_POST['f']) {
    case "abonnement":
      echo abonnement($_POST['noMembre'], $_POST['idArticle']);
      break;
    case "desabonnement":
      echo desabonnement($_POST['noMembre'], $_POST['idArticle']);
      break;
  }
}

function abonnement($noMembre, $noArticle) {
  $query = "INSERT INTO article_suivi(no_membre, no_article) VALUES ($noMembre, $noArticle);";

  include "../#/connection.php";
  $result = mysqli_query($connection, $query) or die ("Query failed: '"  . $query . "' " . mysqli_error($connection));
  mysqli_close($connection);
  return true;
}

function desabonnement($noMembre, $noArticle) {
  $query = "DELETE FROM article_suivi WHERE no_membre=$noMembre AND no_article=$noArticle;";

  include "../#/connection.php";
  $result = mysqli_query($connection, $query) or die ("Query failed: '"  . $query . "' " . mysqli_error($connection));
  mysqli_close($connection);
  return true;
}
?>
