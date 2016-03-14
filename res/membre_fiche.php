<?php
  require_once 'class/membre.php';
  require_once 'class/exemplaire.php';
  require_once 'class/telephone.php';

  $membre = getMembre($_SESSION['nodossier']);
  $membre->setTelephone(getTelephone($membre->getNo()));
  $suivi = getArticleSuivi($membre->getNo());
  $aVendre = getExemplairesAVendre($membre->getNo());
  $vendu = getExemplairesVendu($membre->getNo());
  $argentRemis = getExemplaireArgentRemis($membre->getNo());
?>

<div id='overlay'>
  <form id='coord-form' method='post'>
    <input id='nomembre' name='nomembre' type="hidden" />
    <div>
      <input id='nocivic' name='nocivic' type='text' placeholder='No civic' value='<?php echo $membre->getNoCivic() ?>' required='' />
      <input id='rue' name='rue' type='text' placeholder='Rue' required='' />
      <input id='app' name='app' type='text' placeholder='App.' />
    </div>
    <div>
      <input id='codepostal' name='codepostal' type='text' placeholder='Code Postal' required='' />
      <input id='ville' name='ville' type='text' placeholder='Ville' required='' />
      <select id='province' name='province'>
        <option value='AB'>Alberta</option>
        <option value="BC">Colombie-Britannique</option>
        <option value="PE">Île-du-Prince-Édouard</option>
        <option value="MB">Manitoba</option>
        <option value="NB">Nouveau-Brunswick</option>
        <option value="NU">Nunavut</option>
        <option value='ON'>Ontario</option>
        <option value='QC' selected=''>Québec</option>
        <option value="SK">Saskatchewan</option>
        <option value="NL">Terre-Neuve-et-Labrador</option>
        <option value="NT">Territoires du Nord-Ouest</option>
        <option value="YT">Yukon</option>
      </select>
    </div>
    <div>
      <input id='idtel1' name="idtel1" type="hidden" />
      <input id='telephone1' name='telephone1' type='tel' placeholder='Numéro de téléphone' required='' />
      <input id='note1' name='note1' type='text' placeholder='Note' />
    </div>
    <div>
      <input id='idtel2' name="idtel2" type="hidden" />
      <input id='telephone2' name='telephone2' type='tel' placeholder='Numéro de téléphone' />
      <input id='note2' name='note2' type='text' placeholder='Note' />
    </div>
    <input id='courriel' name='courriel' type='email' placeholder='Courriel' required='' />
    <div>
      <button>Enregistrer</button>
      <button formaction="" onclick='closeOverlay()'>Annuler</button>
    </div>
  </form>
</div>

<script>
  var membre = '<?php echo json_encode((array) $membre); ?>'
  var noMembre = '<?php echo $membre->getNo() ?>';
  var noCivic = '<?php echo $membre->getNoCivic() ?>';
  var rue = '<?php echo $membre->getRue() ?>';
  var app = '<?php echo $membre->getApp() ?>';
  var codePostal = '<?php echo $membre->getCodePostal() ?>';
  var ville = '<?php echo $membre->getVille() ?>';
  var province = '<?php echo $membre->getProvince() ?>';
  var courriel = '<?php echo $membre->getCourriel() ?>';
  var idTel1 = '';
  var tel1 = '';
  var note1 = '';
  var idTel2 = '';
  var tel2 = '';
  var note2 = '';
</script>

<?php
  if($membre->getTelephone() != null) {
    $noTel = 1;

    foreach($membre->getTelephone() as $telephone) {
      $id = $telephone->getId();
      $numero = $telephone->getNumero();
      $note = $telephone->getNote();

      echo "<script>
              idTel$noTel = $id;
              tel$noTel = '$numero';
              note$noTel = '$note';
            </script>";
      $noTel++;
    }
  }
?>


<?php // ÉTAT DE COMPTE ?>
<h1>Bonjour <?php echo $membre->getPrenom() . " " . $membre->getNom(); ?>
  <a href='res/logout.php'>
    <span class='oi' data-glyph='account-logout'></span>
  </a>
</h1>
<section class='inline'>
    <p><b>État du compte :</b></p>
    <table id='infocompte'>
      <tr>
        <td>Date d'inscription :</td>
        <td><?php echo $membre->getInscription(); ?></td>
      </tr>
      <tr>
        <td>Date de dernière activité :</td>
        <td><?php echo $membre->getDerniereActivite(); ?></td>
      </tr>
      <tr>
        <td>Date de désactivation :</td>
        <td><?php echo $membre->getDateDesactivation(); ?></td>
      </tr>
    </table>

<?php
if(isset($_GET['renouvele']) && $_GET['renouvele'] == true)
  echo "<button id='btnrenouv' class='desactive' disabled=''>Compte renouvelé</button>";
else
  echo "<form class='nostyle' action='res/renouvele.php'><button id='btnrenouv'>Renouveler le compte</button></form></section>";

// COORDONNÉES
$htmlStr = "<section class='inline'>
              <p><b>Coordonnées :</b></p>
              <p>" . $membre->getNoCivic() . ", " . $membre->getRue();

if($membre->getApp() != null)
  $htmlStr .= " app. " . $membre->getApp();

$htmlStr .= ",<br/>" . $membre->getVille() . ", " . $membre->getProvince() . ",<br/>" . $membre->getCodePostal() . "<br/>";

if($membre->getTelephone() != null) {
  foreach($membre->getTelephone() as $telephone) {
    $htmlStr .= $telephone->getNumero();

    if($telephone->getNote() != null)
      $htmlStr .= " (" . $telephone->getNote() . ")";
    $htmlStr .= "<br/>";
  }
}

$htmlStr .= $membre->getCourriel() . "</p>
            <button onclick='miseAJourCompte()'>Mettre à jour</button></section>";

// ARTICLE SUIVI
$nbArticle = 0;
$htmlTableStr = "";

foreach($suivi AS $e) {
  $nbArticle++;

  if($e->getPrix() > 0)
    $htmlTableStr .= "<tr class='enstock' data-article='" . $e->getArticle() . "' onclick='ouvrirArticle(this)'>";
  else
    $htmlTableStr .= "<tr data-article='" . $e->getArticle() . "' onclick='ouvrirArticle(this)'>";

  $htmlTableStr .= "<td>" . $e->getTitre() . "</td>
                    <td>" . $e->getPrix() . "</td>
                    </tr></div>";
}

if($nbArticle > 0)
  $htmlStr .= "<section>
                <h2>Artciles suivis</h2>
                <div class='table-wrapper'>
                  <table>
                    <thead>
                      <tr>
                        <th>Titre</th>
                        <th>En stock</th>
                      </tr>
                    </thead>
                    <tbody>
                      $htmlTableStr
                    </tbody>
                  </table>
                </div>
              </section>";

// ARTICLES À VENDRE
$nbArticle = 0;
$montant = 0;
$htmlTableStr = "";
foreach($aVendre as $e) {
  $nbArticle++;
  $montant += $e->getPrix();

  if(articleEstDesuet($e->getArticle()))
    $htmlTableStr .= "<tr class='desuet' data-article='" . $e->getArticle() . "' onclick='ouvrirArticle(this)'>";
  else
    $htmlTableStr .= "<tr data-article='" . $e->getArticle() . "' onclick='ouvrirArticle(this)'>";

  $htmlTableStr .= "<td>" . $e->getTitre() . "</td>
                    <td>" . $e->getDateAjout() . "</td>
                    <td>" . $e->getPrix() . " $</td>
                    </tr>";
}

if($nbArticle > 0)
  $htmlStr .= "<section>
                <h2>À vendre [$nbArticle articles, $montant $]</h2>
                <div class='table-wrapper'>
                  <table>
                    <thead>
                      <tr>
                        <th>Titre</th>
                        <th>Date d’ajout</th>
                        <th>Prix</th>
                      </tr>
                    </thead>
                    <tbody>
                      $htmlTableStr
                    </tbody>
                  </table>
                </div>
              </section>";

// ARTICLES VENDUS
$nbArticle = 0;
$montant = 0;
$htmlTableStr = "";

foreach($vendu as $e) {
  $nbArticle++;
  $montant += $e->getPrix();
  $htmlTableStr .= "<tr data-article='" . $e->getArticle() . "' onclick='ouvrirArticle(this)'>
                      <td>" . $e->getTitre() . "</td>
                      <td>" . $e->getDateAjout() . "</td>
                      <td>" . $e->getDateVente() . "</td>
                      <td>" . $e->getPrix() . " $</td>
                    </tr>";
}

if($nbArticle > 0)
  $htmlStr .= "<section>
                <h2>Vendu [$nbArticle articles, $montant $]</h2>
                <div class='table-wrapper'>
                  <table>
                    <thead>
                      <tr>
                        <th>Titre</th>
                        <th>Date d’ajout</th>
                        <th>Date de vente</th>
                        <th>Prix</th>
                      </tr>
                    </thead>
                    <tbody>
                      $htmlTableStr
                    </tbody>
                  </table>
                </div>
              </section>";


$nbArticle = 0;
$montant = 0;
$htmlTableStr = "";

foreach($argentRemis as $e) {
  $nbArticle++;
  $montant += $e->getPrix();

  $htmlTableStr .= "<tr data-article='" . $e->getArticle() . "' onclick='ouvrirArticle(this)'>
                      <td>" . $e->getTitre() . "</td>
                      <td>" . $e->getDateAjout() . "</td>
                      <td>" . $e->getDateVente() . "</td>
                      <td>" . $e->getDateArgentRemis() . "</td>
                      <td>" . $e->getPrix() . " $</td>
                    </tr>";
}

if($nbArticle > 0)
  $htmlStr .= "<section>
                <h2>Argent remis [$nbArticle articles, $montant $]</h2>
                <div class='table-wrapper'>
                  <table>
                    <thead>
                      <tr>
                        <th>Titre</th>
                        <th>Date d’ajout</th>
                        <th>Date de vente</th>
                        <th>Date de remise d'argent</th>
                        <th>Prix</th>
                      </tr>
                    </thead>
                    <tbody>
                      $htmlTableStr
                    </tbody>
                  </table>
                </div>
              </section>";


echo $htmlStr;
?>





<?php
function getMembre($nodossier) {
  $membre = new Membre();

  $query = "SELECT membre.*, ville.nom AS ville, province.nom AS province FROM membre
            INNER JOIN ville ON membre.id_ville=ville.id
            INNER JOIN province ON ville.code_province=province.code
            WHERE membre.no=$nodossier";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die ("Query failed: '"  . $query . "' " . mysqli_error());
  $row = mysqli_fetch_assoc($result);

  $membre->setNo($row['no']);
  $membre->setPrenom($row['prenom']);
  $membre->setNom($row['nom']);
  $membre->setInscription(date('Y-m-d', strtotime($row['inscription'])));
  $membre->setDerniereActivite(date('Y-m-d', strtotime($row['derniere_activite'])));
  $membre->setNoCivic($row['no_civic']);
  $membre->setRue($row['rue']);
  $membre->setApp($row['app']);
  $membre->setVille($row['ville']);
  $membre->setProvince($row['province']);
  $membre->setCodePostal($row['code_postal']);
  $membre->setCourriel($row['courriel']);

  $membre->setCodePostal(substr($membre->getCodePostal(), 0, 3) . " " . substr($membre->getCodePostal(), 3, 3));

  mysqli_close($connection);
  return $membre;
}

function getTelephone($noMembre) {
  $telephones = array();

  $query = "SELECT id, numero, note FROM telephone WHERE no_membre=$noMembre";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die ("Query failed: '"  . $query . "' " . mysqli_error());

  while($row = mysqli_fetch_assoc($result)) {
    $telephone = new Telephone();

    $telephone->setId($row['id']);
    $telephone->setNumero($row['numero']);
    $telephone->setNote($row['note']);

    $telephone->setNumero(substr($telephone->getNumero(), 0, 3) . " " . substr($telephone->getNumero(), 3, 3) . "-" . substr($telephone->getNumero(), 6, 4));

    array_push($telephones, $telephone);
  }

  mysqli_close($connection);
  return $telephones;
}

function getExemplairesAVendre($noMembre) {
  return getExemplaires($noMembre, 1);
}

function getExemplairesVendu($noMembre) {
  $exemplaires = getExemplaires($noMembre, 2);

  foreach($exemplaires as $e)
    $e->setDateAjout(getDateTransaction($e->getId(), 1));
  return $exemplaires;
}

function getExemplaireArgentRemis($noMembre) {
  $exemplaires = getExemplaires($noMembre, 4);

  foreach($exemplaires as $e) {
    $e->setDateAjout(getDateTransaction($e->getId(), 1));
    $e->setDateVente(getDateTransaction($e->getId(), 2));
  }

  return $exemplaires;
}

function getExemplaires($noMembre, $typeTransaction) {
  $exemplaires = Array();

  $query = "SELECT exemplaire.id AS id,
                   article.id AS article,
                   article.nom,
                   transaction.date,
                   exemplaire.prix
            FROM transaction
            INNER JOIN exemplaire
              ON transaction.id_exemplaire=exemplaire.id
            INNER JOIN article
              ON exemplaire.id_article=article.id
            WHERE no_membre=$noMembre";

  if($typeTransaction == 2 || $typeTransaction == 3)
    $query .= " AND (id_type=2 OR id_type=3)";
  else
    $query .= " AND id_type=$typeTransaction";

  if($typeTransaction == 1)
    $query .= " AND id_exemplaire NOT IN(SELECT id_exemplaire FROM transaction WHERE no_membre=$noMembre AND (id_type=2 OR id_type=3))";
  elseif($typeTransaction == 2 || $typeTransaction == 3)
    $query .= " AND id_exemplaire NOT IN(SELECT id_exemplaire FROM transaction WHERE no_membre=$noMembre AND id_type=4)";

  $query .= " ORDER BY nom";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die ("Query failed: '"  . $query . "' " . mysqli_error());

  while($row = mysqli_fetch_assoc($result)) {
    $exemplaire = new Exemplaire();

    $exemplaire->setId($row['id']);
    $exemplaire->setArticle($row['article']);
    $exemplaire->setTitre($row['nom']);
    $exemplaire->setDate(date( 'Y-m-d', strtotime($row['date'])), $typeTransaction);
    $exemplaire->setPrix($row['prix']);

    array_push($exemplaires, $exemplaire);
  }

  mysqli_close($connection);
  return $exemplaires;
}

function getArticleSuivi($noMembre) {
  $articles = array();

  $query = "SELECT id, nom FROM article WHERE id IN (SELECT no_article FROM article_suivi WHERE no_membre=$noMembre)";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die ("Query failed: '"  . $query . "' " . mysqli_error());

  while($row = mysqli_fetch_assoc($result)) {
    $e = new Exemplaire();

    $e->setArticle($row['id']);
    $e->setTitre($row['nom']);
    $e->setPrix(getNombreEnVente($e->getArticle()));

    array_push($articles, $e);
  }

  return $articles;
}

function getNombreMisEnVente($idArticle) {
  $query = "SELECT COUNT(*) AS nombre FROM transaction
            INNER JOIN exemplaire ON transaction.id_exemplaire=exemplaire.id
            INNER JOIN article ON exemplaire.id_article=article.id
            WHERE article.id=$idArticle
            AND transaction.id_type=1";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die ("Query failed: '"  . $query . "' " . mysqli_error());
  $row = mysqli_fetch_assoc($result);

  return $row['nombre'];
}

function getNombreVendu($idArticle) {
  $query = "SELECT COUNT(*) AS nombre FROM transaction
            INNER JOIN exemplaire ON transaction.id_exemplaire=exemplaire.id
            INNER JOIN article ON exemplaire.id_article=article.id
            WHERE article.id=$idArticle
            AND (transaction.id_type=2 OR transaction.id_type=3)";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die ("Query failed: '"  . $query . "' " . mysqli_error());
  $row = mysqli_fetch_assoc($result);

  return $row['nombre'];
}

function getNombreEnVente($idArticle) {
  return (getNombreMisEnVente($idArticle) - getNombreVendu($idArticle));
}

function getDateTransaction($idExemplaire, $typeTransaction) {
  if($typeTransaction == 2 || $typeTransaction == 3)
    $query = "SELECT date FROM transaction WHERE id_exemplaire=$idExemplaire AND (id_type=2 OR id_type=3)";
  else
    $query = "SELECT date FROM transaction WHERE id_exemplaire=$idExemplaire AND id_type=$typeTransaction";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die ("Query failed: '"  . $query . "' " . mysqli_error());
  $row = mysqli_fetch_assoc($result);

  return date( 'Y-m-d', strtotime($row['date']));
}

function articleEstDesuet($idArticle) {
  $query = "SELECT COUNT(*) AS nombre
          FROM propriete_valeur
          INNER JOIN propriete_article
            ON propriete_valeur.id=propriete_article.id_propriete_valeur
          WHERE propriete_article.id_article=$idArticle
            AND propriete_valeur.id_propriete=15";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die ("Query failed: '"  . $query . "' " . mysqli_error());
  $row = mysqli_fetch_assoc($result);

  return $row['nombre'] != 0;
}

?>
