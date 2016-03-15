<?php
require_once 'class/article.php';
require_once 'class/exemplaire.php';

$article = getArticle($_GET['article']);
$stats = getArticleStats($article->getId());

if (isConnected()) {
  echo "<script>const noMembre = " . $_SESSION['nodossier'] . "</script>"  ?>
  <div id='signalement'>
    <form method='post' action='res/signalement.php'>
      <input type='hidden' id='article' name='article' value="<?php echo $article->getId() ?>" />
      <input type='hidden' id='membre' name='membre' value="<?php echo $_SESSION['nodossier']; ?>" />
      <textarea name='signaler' placeholder="Décriver l'erreur que vous avez trouvé"></textarea>
      <button>Enregistrer</button>
      <button formaction='' onclick='closeSignal()'>Annuler</button>
    </form>
  </div>
<?php } ?>

<div style='margin-top: 20px;'>
  <h1>
    <?php echo $article->getTitre();
    if (isConnected()) { ?>
      <span class='oi'
            data-glyph='star'
            data-article='<?php echo $article->getId() ?>'
            data-state='<?php echo getState(); ?>'
            onclick='abonnement(this)'>
      </span>
    <?php } ?>
  </h1>
  <table class="borderless">
    <tr>
      <td class='boldtitre'>Auteur.e.s :</td>
      <td><?php echo $article->getAuteur(); ?></td>
    </tr>
    <tr>
      <td class='boldtitre'>Éditeur :</td>
      <td><?php echo $article->getEditeur(); ?></td>
    </tr>
    <tr>
      <td class='boldtitre'>Édition :</td>
      <td><?php echo $article->getEdition(); ?></td>
    </tr>
    <tr>
      <td class='boldtitre'>Année de parution :</td>
      <td><?php echo $article->getAnnee(); ?></td>
    </tr>
    <tr>
      <td class='boldtitre'>Code EAN13 :</td>
      <td><?php echo $article->getCode(); ?></td>
    </tr>
  </table>
</div>

<?php if ($stats['quantity'] > 0) { ?>
  <p>Nous possédons <?php echo $stats['quantity'] ?> exemplaire(s) en stock de cet article et le prix moyen de vente est de <?php echo $stats['average'] ?>$.</p>
<? } else { ?>
  <p>Nous ne possédons pas d'exemplaire en stock pour cet article. Vous pouvez le suivre pour être informer d'un éventuel approvisionnement.</p>
<? } ?>

<?php if (isConnected()) { ?>
  <button onclick='openSignal()'>Signaler une erreur</button>
<? } ?>


<?php
function getArticle($articleId) {
  $article = new Article();
  $auteur = "";

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
    $auteur .= $array['auteur_1'];
  }
  if(isset($array['auteur_2'])) {
    $auteur .= ", " . $array['auteur_2'];
  }
  if (isset($array['auteur_3'])) {
    $auteur .= ", " . $array['auteur_3'];
  }
  if (isset($array['auteur_4'])) {
    $auteur .= ", " . $array['auteur_4'];
  }
  if (isset($array['auteur_5'])) {
    $auteur .= ", " . $array['auteur_5'];
  }

  $article->setId($articleId);
  $article->setAuteur($auteur);
  $article->setTitre($array['ouvrage_titre']);
  $article->setEditeur($array['editeur_nom']);
  $article->setEdition($array['ouvrage_no_edition']);
  $article->setAnnee($array['ouvrage_parution']);
  $article->setCode($array['ouvrage_ean13']);

  return $article;
}

function getArticleStats($articleId) {
  $exemplaires = Array();

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

function isSubscribed($noMembre, $noArticle) {
  $query = "SELECT COUNT(*) AS count FROM article_suivi WHERE no_membre=$noMembre AND no_article=$noArticle;";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
  $row = mysqli_fetch_assoc($result);
  $count = $row['count'];

  mysqli_close($connection);
  return $count != 0;
}

function isConnected() {
  return isset($_SESSION['courriel']) && isset($_SESSION['nodossier']) && ($_SESSION['expire'] == null || time() < $_SESSION['expire']);
}

function getState() {
  if (isSubscribed($_SESSION['nodossier'], $_GET['article'])) {
    return 'abonne';
  } return 'desabonne';
}
?>
