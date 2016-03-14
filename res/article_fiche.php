<?php
require_once 'class/article.php';
require_once 'class/exemplaire.php';

$idArticle = $_GET['article'];
$article = getArticle($idArticle);
$ex = getExemplaire($idArticle);

$titre = $article->getTitre();
$auteur = $article->getAuteur();
$editeur = $article->getEditeur();
$edition = $article->getEdition();
$annee = $article->getAnnee();
$code = $article->getCode();
$total = count($ex);
$prix = 0;
$moyenne = 0;
$now = time();

if (estConnecte()) {
  $noMembre = $_SESSION['nodossier'];
  echo "<div id='signalement'>
          <form method='post' action='res/signalement.php'>
            <input type='hidden' id='article' name='article' value='$idArticle' />
            <input type='hidden' id='membre' name='membre' value='$noMembre' />
            <textarea name='signaler' placeholder=\"Décriver l'erreur que vous avez trouvé\"></textarea>
            <button>Enregistrer</button>
            <button formaction='' onclick='closeSignal()'>Annuler</button>
          </form>
        </div>";
}


$htmlStr = "<div style='margin-top: 20px;'>
                <h1>$titre";

if (estConnecte()) {
  echo "<script>const noMembre = " . $_SESSION['nodossier'] . "</script>";
  if (estAbonne($_SESSION['nodossier'], $idArticle) != 0) {
    $htmlStr .= "<span class='oi' data-glyph='star' data-state='abonne' data-article='$idArticle' onclick='abonnement(this)'></span>";
  } else {
    $htmlStr .= "<span class='oi' data-glyph='star' data-state='desabonne' data-article='$idArticle' onclick='abonnement(this)'></span>";
  }
}

$htmlStr .= "</h1>
             <span class='boldtitre'>Auteur(s) : </span>$auteur</br>
             <span class='boldtitre'>Éditeur : </span>$editeur</br>
             <span class='boldtitre'>Édition : </span>$edition</br>
             <span class='boldtitre'>Année de parution : </span>$annee</br>
             <span class='boldtitre'>Code EAN13 : </span>$code</br>
             </div>";



if($total != 0){
  foreach ($ex as $e) {
  $prix = $prix+$e->getPrix();
  }
  $moyenne = $prix/$total;

  if($total == 1) {
    $htmlStr .= "<p>Nous possédons en stock ".$total." exemplaire de cet article ";
  }
  else {
    $htmlStr .= "<p>Nous possédons en stock ".$total." exemplaires de cet article ";
  }

  $htmlStr .= "et le prix moyen est de ".round($moyenne)." dollars.</p>";
}
else{
  $htmlStr .= "<p>Nous ne possédons pas d'exemplaire en stock pour cet article. Vous pouvez le suivre pour être informer d'un éventuel approvisionnement.</p>";
}

if (estConnecte()) {
$htmlStr .= "<button onclick='openSignal()'>Signaler une erreur</button>";
}

echo $htmlStr;
?>

<?php
function getArticle($id) {
  $article = new Article();
  $auteur = "";

  $query = "SELECT (SELECT valeur FROM propriete_valeur WHERE id_propriete=10 AND id IN (SELECT id_propriete_valeur FROM propriete_article WHERE id_article=$id)) AS titre, (SELECT valeur FROM propriete_valeur WHERE id_propriete=2 AND id IN (SELECT id_propriete_valeur FROM propriete_article WHERE id_article=$id)) AS auteur_1, (SELECT valeur FROM propriete_valeur WHERE id_propriete=3 AND id IN (SELECT id_propriete_valeur FROM propriete_article WHERE id_article=$id)) AS auteur_2, (SELECT valeur FROM propriete_valeur WHERE id_propriete=4 AND id IN (SELECT id_propriete_valeur FROM propriete_article WHERE id_article=$id)) AS auteur_3, (SELECT valeur FROM propriete_valeur WHERE id_propriete=5 AND id IN (SELECT id_propriete_valeur FROM propriete_article WHERE id_article=$id)) AS auteur_4, (SELECT valeur FROM propriete_valeur WHERE id_propriete=6 AND id IN (SELECT id_propriete_valeur FROM propriete_article WHERE id_article=$id)) AS auteur_5, (SELECT valeur FROM propriete_valeur WHERE id_propriete=9 AND id IN (SELECT id_propriete_valeur FROM propriete_article WHERE id_article=$id)) AS editeur, (SELECT valeur FROM propriete_valeur WHERE id_propriete=12 AND id IN (SELECT id_propriete_valeur FROM propriete_article WHERE id_article=$id)) AS edition, (SELECT valeur FROM propriete_valeur WHERE id_propriete=11 AND id IN (SELECT id_propriete_valeur FROM propriete_article WHERE id_article=$id)) AS annee, (SELECT valeur FROM propriete_valeur WHERE id_propriete=13 AND id IN (SELECT id_propriete_valeur FROM propriete_article WHERE id_article=$id)) AS code";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die ("Query failed: '"  . $query . "' " . mysqli_error());
  $row = mysqli_fetch_assoc($result);

  $article->setTitre($row['titre']);

  if(isset($row['auteur_1'])) {
    $auteur .= $row['auteur_1'];
  }
  if(isset($row['auteur_2'])) {
    $auteur .= ", " . $row['auteur_2'];
  }
  if (isset($row['auteur_3'])) {
    $auteur .= ", " . $row['auteur_3'];
  }
  if (isset($row['auteur_4'])) {
    $auteur .= ", " . $row['auteur_4'];
  }
  if (isset($row['auteur_5'])) {
    $auteur .= ", " . $row['auteur_5'];
  }

  $article->setAuteur($auteur);
  $article->setEditeur($row['editeur']);
  $article->setEdition($row['edition']);
  $article->setAnnee($row['annee']);
  $article->setCode($row['code']);

  mysqli_close($connection);
  return $article;
}

function getExemplaire($id) {
  $exemplaires = Array();

  $query = "SELECT exemplaire.prix,
                   exemplaire.id AS id
            FROM exemplaire
            INNER JOIN transaction
              ON transaction.id_exemplaire=exemplaire.id
            WHERE id_article = $id AND transaction.id_exemplaire NOT IN(SELECT transaction.id_exemplaire FROM transaction WHERE id_type=2 OR id_type=3)";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die ("Query failed: '"  . $query . "' " . mysqli_error());
  while($row = mysqli_fetch_assoc($result)){
    $exemplaire = new Exemplaire();

    $exemplaire->setPrix($row['prix']);
    $exemplaire->setId($row['id']);

    array_push($exemplaires, $exemplaire);
  }

  mysqli_close($connection);
  return $exemplaires;
}

function estAbonne($noMembre, $noArticle) {
  $query = "SELECT COUNT(*) AS count FROM article_suivi WHERE no_membre=$noMembre AND no_article=$noArticle;";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die ("Query failed: '"  . $query . "' " . mysqli_error());
  $row = mysqli_fetch_assoc($result);
  $count = $row['count'];

  mysqli_close($connection);
  return $count;
}

function estConnecte() {
  return isset($_SESSION['courriel']) && isset($_SESSION['nodossier']) && ($_SESSION['expire'] == null || time() < $_SESSION['expire']);
}

?>
