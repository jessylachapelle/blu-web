<?php
  require_once 'class/member.php';
  require_once 'class/copy.php';
  require_once 'class/telephone.php';

  $member = getMembre($_SESSION['memberNo']);
  $member->setTelephone(getTelephone($member->getNo()));
  $itemFeed = getItemFeed($member->getNo());
  $inStock = getCopiesInStock($member->getNo());
  $sold = getCopiesSold($member->getNo());
  $paid = getCopiesPaid($member->getNo());
?>

<div id='overlay'>
  <form id='coord-form' method='post'>
    <input id='memberNo' name='memberNo' type="hidden" />
    <div>
      <input id='address' name='address' type='text' placeholder='Adresse' required='required' />
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
  var member = '<?php echo json_encode((array) $member); ?>'
  var memberNo = '<?php echo $member->getNo() ?>';
  var address = '<?php echo $member->getAddress() ?>';
  var codePostal = '<?php echo $member->getCodePostal() ?>';
  var ville = '<?php echo $member->getVille() ?>';
  var province = '<?php echo $member->getProvince() ?>';
  var courriel = '<?php echo $member->getCourriel() ?>';
  var idTel1 = '';
  var tel1 = '';
  var note1 = '';
  var idTel2 = '';
  var tel2 = '';
  var note2 = '';
</script>

<?php
  if ($member->getTelephone() != null) {
    $noTel = 1;

    foreach ($member->getTelephone() as $phone) {
      $id = $phone->getId();
      $number = $phone->getNumero();
      $note = $phone->getNote();

      echo "<script>
              idTel$noTel = $id;
              tel$noTel = '$number';
              note$noTel = '$note';
            </script>";
      $noTel++;
    }
  }
?>


<?php // ÉTAT DE COMPTE ?>
<h1>Bonjour <?php echo $member->getPrenom() . " " . $member->getNom(); ?>
  <a href='res/logout.php'>
    <span class='oi' data-glyph='account-logout'></span>
  </a>
</h1>
<section class='inline'>
    <p><b>État du compte :</b></p>
    <table id='infocompte'>
      <tr>
        <td>Date d'inscription :</td>
        <td><?php echo $member->getInscription(); ?></td>
      </tr>
      <tr>
        <td>Date de dernière activité :</td>
        <td><?php echo $member->getDerniereActivite(); ?></td>
      </tr>
      <tr>
        <td>Date de désactivation :</td>
        <td><?php echo $member->getDateDesactivation(); ?></td>
      </tr>
    </table>

<?php
if (isset($_GET['renouvele']) && $_GET['renouvele'] == true) {
  echo "<button id='btnrenouv' class='desactive' disabled=''>Compte renouvelé</button>";
} else {
  echo "<form class='nostyle' action='res/renew_account.php'><button id='btnrenouv'>Renouveler le compte</button></form></section>";
}

// COORDONNÉES
$htmlStr = "<section class='inline'>
              <p><b>Coordonnées :</b></p>
              <p>" . $member->getAddress();

$htmlStr .= ",<br/>" . $member->getVille() . ", " . $member->getProvince() . ",<br/>" . $member->getCodePostal() . "<br/>";

if($member->getTelephone() != null) {
  foreach($member->getTelephone() as $telephone) {
    $htmlStr .= $telephone->getNumero();

    if($telephone->getNote() != null)
      $htmlStr .= " (" . $telephone->getNote() . ")";
    $htmlStr .= "<br/>";
  }
}

$htmlStr .= $member->getCourriel() . "</p>
            <button onclick='miseAJourCompte()'>Mettre à jour</button></section>";

// ARTICLE SUIVI
$nbArticle = 0;
$htmlTableStr = "";

foreach($itemFeed AS $e) {
  $nbArticle++;

  if($e->getPrice() > 0)
    $htmlTableStr .= "<tr class='enstock' data-article='" . $e->getArticle() . "' onclick='openArticle(this)'>";
  else
    $htmlTableStr .= "<tr data-article='" . $e->getArticle() . "' onclick='openArticle(this)'>";

  $htmlTableStr .= "<td>" . $e->getTitle() . "</td>
                    <td>" . $e->getPrice() . "</td>
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
foreach($inStock as $e) {
  $nbArticle++;
  $montant += $e->getPrice();

  if (itemIsOutdated($e->getArticle())) {
    $htmlTableStr .= "<tr class='outdated' data-article='" . $e->getArticle() . "' onclick='openArticle(this)'>";
  } else {
    $htmlTableStr .= "<tr data-article='" . $e->getArticle() . "' onclick='openArticle(this)'>";
  }

  $htmlTableStr .= "<td>" . $e->getTitle() . "</td>
                    <td>" . $e->getDateAdded() . "</td>
                    <td>" . $e->getPrice() . " $</td>
                    </tr>";
}

if ($nbArticle > 0) {
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
}

// ARTICLES VENDUS
$nbArticle = 0;
$montant = 0;
$htmlTableStr = "";

foreach($sold as $e) {
  $nbArticle++;
  $montant += $e->getPrice();
  $htmlTableStr .= "<tr data-article='" . $e->getArticle() . "' onclick='openArticle(this)'>
                      <td>" . $e->getTitle() . "</td>
                      <td>" . $e->getDateAdded() . "</td>
                      <td>" . $e->getDateSold() . "</td>
                      <td>" . $e->getPrice() . " $</td>
                    </tr>";
}

if ($nbArticle > 0) {
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
}

$nbArticle = 0;
$montant = 0;
$htmlTableStr = "";

foreach($paid as $e) {
  $nbArticle++;
  $montant += $e->getPrice();

  $htmlTableStr .= "<tr data-article='" . $e->getArticle() . "' onclick='openArticle(this)'>
                      <td>" . $e->getTitle() . "</td>
                      <td>" . $e->getDateAdded() . "</td>
                      <td>" . $e->getDateSold() . "</td>
                      <td>" . $e->getDatePaid() . "</td>
                      <td>" . $e->getPrice() . " $</td>
                    </tr>";
}

if ($nbArticle > 0) {
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
}

echo $htmlStr;
?>





<?php
function getMembre($no) {
  $member = new Membre();

  $query = "SELECT member.*,
                   city.name AS city,
                   state.name AS state
            FROM member
            INNER JOIN city
              ON member.city=city.id
            INNER JOIN state
              ON city.state=state.code
            WHERE member.no=$no;";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
  $row = mysqli_fetch_assoc($result);
  mysqli_close($connection);

  $member->setNo($row['no']);
  $member->setPrenom($row['first_name']);
  $member->setNom($row['last_name']);
  $member->setInscription(date('Y-m-d', strtotime($row['registration'])));
  $member->setDerniereActivite(date('Y-m-d', strtotime($row['last_activity'])));
  $member->setAddress($row['address']);
  $member->setVille($row['city']);
  $member->setProvince($row['state']);
  $member->setCourriel($row['email']);

  if ($row['zip'] != null && $row['zip'] != '') {
    $member->setCodePostal(substr($row['zip'], 0, 3) . " " . substr($row['zip'], 3, 3));
  }

  return $member;
}

function getTelephone($memberNo) {
  $phones = [];

  $query = "SELECT id, number, note FROM phone WHERE member=$memberNo;";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());

  while($row = mysqli_fetch_assoc($result)) {
    $phone = new Telephone();

    $phone->setId($row['id']);
    $phone->setNumero(substr($row['number'], 0, 3) . " " . substr($row['number'], 3, 3) . "-" . substr($row['number'], 6, 4));
    $phone->setNote($row['note']);

    array_push($phones, $phone);
  }

  mysqli_close($connection);
  return $phones;
}

function getCopiesInStock($memberNo) {
  return getCopies($memberNo, 1);
}

function getCopiesSold($memberNo) {
  $copies = getCopies($memberNo, 2);

  foreach($copies as $copy)
    $copy->setDateAdded(getTransactionDate($copy->getId(), 1));
  return $copies;
}

function getCopiesPaid($memberNo) {
  $copies = getCopies($memberNo, 4);

  foreach($copies as $copy) {
    $copy->setDateAdded(getTransactionDate($copy->getId(), 1));
    $copy->setDateSold(getTransactionDate($copy->getId(), 2));
  }

  return $copies;
}

function getCopies($memberNo, $transactionType) {
  $copies = [];

  $query = "SELECT copy.id,
                   item.id AS item_id,
                   item.name AS item_name,
                   transaction.date,
                   copy.price
            FROM transaction
            INNER JOIN copy
              ON transaction.copy=copy.id
            INNER JOIN item
              ON copy.item=item.id
            WHERE transaction.member=$memberNo";

  if ($transactionType == 2 || $transactionType == 3) {
    $query .= " AND (type=2 OR type=3)";
    $query .= " AND copy NOT IN(SELECT copy FROM transaction WHERE member=$memberNo AND type=4)";
  } else {
    $query .= " AND type=$transactionType";
  }

  if ($transactionType == 1) {
    $query .= " AND copy NOT IN(SELECT copy FROM transaction WHERE member=$memberNo AND (type=2 OR type=3))";
  }

  $query .= " ORDER BY item_name";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());

  while($row = mysqli_fetch_assoc($result)) {
    $copy = new Exemplaire();

    $copy->setId($row['id']);
    $copy->setArticle($row['item_id']);
    $copy->setTitle($row['item_name']);
    $copy->setDate(date( 'Y-m-d', strtotime($row['date'])), $transactionType);
    $copy->setPrice($row['price']);

    array_push($copies, $copy);
  }

  mysqli_close($connection);
  return $copies;
}

function getItemFeed($memberNo) {
  $items = [];

  $query = "SELECT item.id, item.name
            FROM item_feed
            INNER JOIN item
              ON item_feed.item=item.id
            WHERE member=$memberNo;";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());

  while($row = mysqli_fetch_assoc($result)) {
    $copy = new Exemplaire();

    $copy->setArticle($row['id']);
    $copy->setTitle($row['name']);
    $copy>setPrice(getAmountInStock($copy->getArticle()));

    array_push($items, $copy);
  }

  return $items;
}

function getTotalInventory($itemId) {
  $query = "SELECT COUNT(*) AS count
            FROM transaction
            INNER JOIN copy
              ON transaction.copy=copy.id
            INNER JOIN item
              ON copy.item=item.id
            WHERE item.id=$itemId
            AND transaction.type=1";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
  $row = mysqli_fetch_assoc($result);
  mysqli_close($connection);

  return $row['count'];
}

function gotAmountSold($itemId) {
  $query = "SELECT COUNT(*) AS count
            FROM transaction
            INNER JOIN copy
              ON transaction.copy=copy.id
            INNER JOIN item
              ON copy.item=item.id
            WHERE item.id=$itemId
            AND (transaction.type=2 OR transaction.type=3)";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
  $row = mysqli_fetch_assoc($result);
  mysqli_close($connection);

  return $row['count'];
}

function getAmountInStock($idArticle) {
  return (getTotalInventory($idArticle) - gotAmountSold($idArticle));
}

function getTransactionDate($copyId, $transactionType) {
  $query = "";

  if ($transactionType == 2 || $transactionType == 3) {
    $query = "SELECT date FROM transaction WHERE copy=$copyId AND (type=2 OR type=3)";
  } else {
    $query = "SELECT date FROM transaction WHERE copy=$copyId AND type=$transactionType";
  }

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
  $row = mysqli_fetch_assoc($result);
  mysqli_close($connection);

  return date( 'Y-m-d', strtotime($row['date']));
}

function itemIsOutdated($itemId) {
  $query = "SELECT COUNT(id) AS count
            FROM item WHERE id=$itemId
            AND status=(SELECT id FROM status WHERE code='VALID');";

  include "#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
  $row = mysqli_fetch_assoc($result);
  mysqli_close($connection);

  return $row['count'] == 0;
}
?>
