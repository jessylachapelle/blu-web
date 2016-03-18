<?php
require_once 'res/article.php';

function getArticle($articleId) {
  $article = new Article();
  $author = "";

  $query = "SELECT propriete.nom AS propriety,
                   propriete_valeur.valeur AS value
            FROM propriete_article
            INNER JOIN propriete_valeur
              ON propriete_article.id_propriete_valeur=propriete_valeur.id
            INNER JOIN propriete
              ON propriete_valeur.id_propriete=propriete.id
            WHERE propriete_article.id_article=$articleId
              AND propriete.nom!='caisse';";


  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());

  $array = array();
  while($row = mysqli_fetch_assoc($result)) {
    $array[$row['propriety']] = $row['value'];
  }

  if(isset($array['auteur_1'])) {
    $author .= $array['auteur_1'];
  }
  if(isset($array['auteur_2'])) {
    $author .= ", " . $array['auteur_2'];
  }
  if (isset($array['auteur_3'])) {
    $author .= ", " . $array['auteur_3'];
  }
  if (isset($array['auteur_4'])) {
    $author .= ", " . $array['auteur_4'];
  }
  if (isset($array['auteur_5'])) {
    $author .= ", " . $array['auteur_5'];
  }

  $article->setId($articleId);
  $article->setAuthor($author);
  $article->setTitle($array['ouvrage_titre']);
  $article->setEditor($array['editeur_nom']);
  $article->setEdition($array['ouvrage_no_edition']);
  $article->setYear($array['ouvrage_parution']);
  $article->setCode($array['ouvrage_ean13']);

  return $article;
}

function getArticleStats($articleId) {
  $query = "SELECT SUM(exemplaire.prix) AS amount,
                   COUNT(exemplaire.id) AS quantity
            FROM exemplaire
            INNER JOIN transaction
              ON transaction.id_exemplaire=exemplaire.id
            WHERE id_article = $articleId
              AND transaction.id_exemplaire NOT IN(SELECT transaction.id_exemplaire
                                                   FROM transaction
                                                   WHERE id_type=2 OR id_type=3)";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
  $row = mysqli_fetch_assoc($result);

  if ($row['quantity'] > 0) {
    $stats = [
      "quantity" => $row['quantity'],
      "amount" => $row['amount'],
      "average" => round($row['amount'] / $row['quantity'])
    ];
  } else {
    $stats["quantity"] = 0;
  }

  mysqli_close($connection);
  return $stats;
}

function isSubscribed($memberNo, $articleId) {
  $query = "SELECT COUNT(*) AS count FROM article_suivi WHERE no_membre=$memberNo AND no_article=$articleId;";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
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
  if (isSubscribed($_SESSION['memberNo'], $_GET['article'])) {
    return 'subscribed';
  } return 'unsubscribed';
}
?>
