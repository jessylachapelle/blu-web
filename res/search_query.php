<?php
if(isset($_POST['search-data'])) {
  $searchData = $_POST['search-data'];

  $query = "SELECT article.id, article.nom AS name
            FROM article
            INNER JOIN propriete_article
              ON article.id=propriete_article.id_article
            INNER JOIN propriete_valeur
              ON propriete_article.id_propriete_valeur=propriete_valeur.id
            WHERE nom LIKE '%$searchData%' OR valeur LIKE '%$searchData%'";

  include "../#/connection.php";
  $result = mysqli_query($connection, $query) or die ("Query failed: '$query' " . mysqli_error($connection));
  $articles = [];

  while($row = mysqli_fetch_assoc($result)) {
    $articles[$row['id']] = addProprieties($row);
  }

  mysqli_close($connection);
  echo json_encode($articles);
}

function addProprieties($res) {
  $id = $res['id'];
  $query = "SELECT
            (SELECT valeur FROM propriete_valeur
            INNER JOIN propriete_article
              ON propriete_valeur.id=propriete_article.id_propriete_valeur
            WHERE id_article=$id AND id_propriete=9) AS editor,
            (SELECT valeur FROM propriete_valeur
            INNER JOIN propriete_article
              ON propriete_valeur.id=propriete_article.id_propriete_valeur
            WHERE id_article=$id AND id_propriete=2) AS author_1,
            (SELECT valeur FROM propriete_valeur
            INNER JOIN propriete_article
              ON propriete_valeur.id=propriete_article.id_propriete_valeur
            WHERE id_article=$id AND id_propriete=3) AS author_2,
            (SELECT valeur FROM propriete_valeur
            INNER JOIN propriete_article
              ON propriete_valeur.id=propriete_article.id_propriete_valeur
            WHERE id_article=$id AND id_propriete=4) AS author_3,
            (SELECT valeur FROM propriete_valeur
            INNER JOIN propriete_article
              ON propriete_valeur.id=propriete_article.id_propriete_valeur
            WHERE id_article=$id AND id_propriete=5) AS author_4,
            (SELECT valeur FROM propriete_valeur
            INNER JOIN propriete_article
              ON propriete_valeur.id=propriete_article.id_propriete_valeur
            WHERE id_article=$id AND id_propriete=6) AS author_5";

  include "../#/connection.php";
  $result = mysqli_query($connection, $query) or die ("Query failed: '$query' " . mysqli_error($connection));
  $row = mysqli_fetch_assoc($result);
  mysqli_close($connection);

  $author = "";

  for($i = 1; $i <= 5; $i++) {
    if($row['author_' . $i] != null) {
      $author .= $row['author_' . $i] . "; ";
    }
  }

  return [
    "id" => $res['id'],
    "name" => $res['name'],
    "author" => $author,
    "editor" => $row['editor']
  ];
}
?>
