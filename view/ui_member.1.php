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
<script>
function request (method, url, callback) {
  const xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      try {
        callback(null, JSON.parse(this.responseText))
      } catch (err) {
        callback(null, this.responseText);
      }
    } else if (this.readyState === 4) {
      callback({ status: this.status, message: this.responseText });
    }
  };
  xhttp.open(method, url, true);
  xhttp.send();
}

function populateTable(tableBody, columns, data, extraConfig) {
  data.forEach((row) => {
    const tr = document.createElement('tr');
    
    if (extraConfig) {
      extraConfig(tr, row);
    }

    columns.forEach((column) => {
      const td = document.createElement('td');
      td.innerText = row[column];
      tr.appendChild(td);
    });

    tableBody.appendChild(tr);
  });
}

function displayMember (member) {
  console.log(member);
  const isActive = member.account.isActive;
  const deactivation = member.account.deactivationDate.toLocaleDateString();
  
  if (!isActive) {
    document.getElementById('deactivationDate').innerText = deactivation;
    document.getElementById('deactivationBanner').style.display = 'block';
  } else {
    const soldCopies = member.account.getSoldCopies();

    if (soldCopies.length) {
      document.getElementById('soldQty').innerText = soldCopies.length;
      document.getElementById('soldAmount').innerText = soldCopies.reduce(function (total, copy) {
        return total + copy.price;
      }, 0);    
      document.getElementById('soldBanner').style.display = 'block';
    }

    const renewButton = document.createElement('button');
    renewButton.id = 'renewButton';
    renewButton.innerText = 'Renouveler mon compte';
    renewButton.addEventListener('click', () => {
      request('GET', `http://localhost/blu/api/src/server/index.php/member/${memberNo}/renew`, (err) => {
        if (!err) {
          renewButton.innerHTML = 'Compte renouvelé';
          renewButton.setAttribute('disabled', 'disabled');
          renewButton.setAttribute('class', 'desactive');
        }
      });
    });
    
    document.getElementById('actions').appendChild(renewButton);
  }

  document.getElementById('name').innerText = `Bonjour ${member.name}`;
  document.getElementById('registration').innerText = member.account.registration.toLocaleDateString();
  document.getElementById('lastActivity').innerText = member.account.lastActivity.toLocaleDateString();
  document.getElementById('deactivation').innerText = deactivation;
  document.getElementById('contactInfo').innerText = member.contactInfo;

  if (member.account.itemFeed.length) {
    const tableBody = document.getElementById('itemFeedBody');
    const columns = ['title', 'inStock'];
    populateTable(tableBody, columns, member.account.itemFeed, (tr, row) => {
      tr.setAttribute('data-item', row.id);
      tr.addEventListener('click', openItem);

      if (row.inStock) {
        tr.setAttribute('class', 'enstock');
      }
    });

    document.getElementById('itemFeed').style.display = 'block';
  }

  const addedCopies = member.account.getAddedCopies();
  console.log(addedCopies);
  if (addedCopies.length) {
    const total = addedCopies.reduce((acc, copy) => acc + copy.price, 0);
    const tableBody = document.getElementById('addedBody');
    const columns = ['title', 'dateAdded', 'price'];
    populateTable(tableBody, columns, addedCopies, (tr, row) => {
      tr.setAttribute('data-item', row.item.id);
      tr.addEventListener('click', openItem);

      if (row.item.isOutdated) {
        tr.setAttribute('class', 'outdated');
      }
    });

    document.getElementById('addedStat').innerText = `${addedCopies.length} articles, ${total} $`;
    document.getElementById('added').style.display = 'block';
  }
}

request('GET', `http://localhost/blu/api/src/server/index.php/member/${memberNo}`, function (err, res) {
  if (res) {
    displayMember(new Member(res));
  }
});
</script>
<div id="deactivationBanner" style="display: none; background-color: #F4F1DC; padding: 15px; margin-top: 30px;">
  Votre compte est désactivé depuis le <span id="deactivationDate"><span>. Pour obtenir plus d'information <a href="wtfaq.php">cliquez ici</a>.
</div>
<div id="soldBanner" style="display: none; background-color: #F4F1DC; padding: 15px; margin-top: 30px;">
  Vous avez vendu <span id="soldQty"></span> livre(s) pour un montant de <span id="soldAmount"></span> $.
  Pour récupérer votre argent rendez-vous à la BLU lors d'une journée de remise d'argent ou communiquer avec la BLU par courriel.
</div>
<h1>
  <span id="name"></span>
  <a href='res/logout.php'>
    <span class='oi' data-glyph='account-logout'></span>
  </a>
</h1>
<div id="actions">
  <button><a href="files/formulaire.pdf" target="_blank" style="text-decoration:none;color:#FFF;">Vendre des Livres</a></button>
</div>
<section class='inline'>
  <p><b>État du compte :</b></p>
  <table id='infocompte'>
    <tr>
      <td>Date d'inscription :</td>
      <td id="registration"></td>
    </tr>
    <tr>
      <td>Date de dernière activité :</td>
      <td id="lastActivity"></td>
    </tr>
    <tr>
      <td>Date de désactivation :</td>
      <td id="deactivation"></td>
    </tr>
  </table>
</section>
<section class='inline'>
  <p><b>Coordonnées :</b></p>
  <p id="contactInfo"></p>
  <button onclick='miseAJourCompte()'>Mettre à jour</button>
</section>
<section id="itemFeed" style="display: none">
  <h2>Articles suivis</h2>
  <div class='table-wrapper'>
    <table>
      <thead>
        <tr>
          <th>Titre</th>
          <th>En stock</th>
        </tr>
      </thead>
      <tbody id="itemFeedBody">
      </tbody>
    </table>
  </div>
</section>
<section id="added" style="display:none;">
  <h2>À vendre (<span id="addedStat"></span>)</h2>
  <div class='table-wrapper'>
    <table>
      <thead>
        <tr>
          <th>Titre</th>
          <th>Date d’ajout</th>
          <th>Prix</th>
        </tr>
      </thead>
      <tbody id="addedBody">
      </tbody>
    </table>
  </div>
</section>

<?php
// ARTICLES VENDUS
$htmlStr = '';
$nbArticle = 0;
$montant = 0;
$htmlTableStr = "";

foreach($sold as $e) {
  $nbArticle++;
  $montant += $e->getPrice();
  $htmlTableStr .= "<tr data-article='" . $e->getArticle() . "' onclick='openItem'>
                      <td>" . $e->getTitle() . "</td>
                      <td>" . $e->getDateAdded() . "</td>
                      <td>" . $e->getDateSold() . "</td>
                      <td>" . $e->getPrice() . " $</td>
                    </tr>";
}

if ($nbArticle > 0 && $member->isActive()) {
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

  $htmlTableStr .= "<tr data-article='" . $e->getArticle() . "' onclick='openItem'>
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

  include '#/connection.php';
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");
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

  include '#/connection.php';
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");

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
  $copies = getCopies($memberNo, 'ADD');

  foreach($copies as $copy) {
    $copy->setDateAdded(getTransactionDate($copy->getId(), 'ADD'));
  }

  return $copies;
}

function getCopiesSold($memberNo) {
  $copies = getCopies($memberNo, 'SELL');

  foreach($copies as $copy) {
    $copy->setDateAdded(getTransactionDate($copy->getId(), 'ADD'));
    $copy->setDateSold(getTransactionDate($copy->getId(), 'SELL'));
  }

  return $copies;
}

function getCopiesPaid($memberNo) {
  $copies = getCopies($memberNo, 'PAY');

  foreach($copies as $copy) {
    $copy->setDateAdded(getTransactionDate($copy->getId(), 'ADD'));
    $copy->setDateSold(getTransactionDate($copy->getId(), 'SELL'));
    $copy->setDatePaid(getTransactionDate($copy->getId(), 'PAY'));
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

  if (preg_match("/SELL/i", $transactionType)) {
    $query .= " AND type IN (SELECT id FROM transaction_type WHERE code LIKE 'SELL%')";
    $query .= " AND copy NOT IN(SELECT copy FROM transaction WHERE member=$memberNo AND type IN (SELECT id FROM transaction_type WHERE code='PAY' or code='DONATE'))";
  } else {
    $query .= " AND type=(SELECT id FROM transaction_type WHERE code='$transactionType')";
  }

  if ($transactionType == 'ADD') {
    $query .= " AND copy NOT IN(SELECT copy FROM transaction WHERE member=$memberNo AND type IN (SELECT id FROM transaction_type WHERE code LIKE 'SELL%'))";
  }

  $query .= ' ORDER BY item_name;';

  include '#/connection.php';
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");

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

  include '#/connection.php';
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");

  while($row = mysqli_fetch_assoc($result)) {
    array_push($items, [
      'id' => $row['id'],
      'title' => $row['name'],
      'inStock' => getAmountInStock($row['id'])
    ]);
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
            AND transaction.type=(SELECT id FROM transaction_type WHERE code='VALID');";

  include '#/connection.php';
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");
  $row = mysqli_fetch_assoc($result);
  mysqli_close($connection);

  return $row['count'];
}

function getAmountSold($itemId) {
  $query = "SELECT COUNT(*) AS count
            FROM transaction
            INNER JOIN copy
              ON transaction.copy=copy.id
            INNER JOIN item
              ON copy.item=item.id
            WHERE item.id=$itemId
            AND transaction.type IN (SELECT id
                                     FROM transaction_type
                                     WHERE code
                                     LIKE 'SELL%');";

  include '#/connection.php';
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");
  $row = mysqli_fetch_assoc($result);
  mysqli_close($connection);

  return $row['count'];
}

function getAmountInStock($idArticle) {
  $query = "SELECT
              (SELECT COUNT(DISTINCT(copy.id))
              FROM copy
              INNER JOIN transaction
                ON copy.id = transaction.copy
              WHERE item = $idArticle) - 
              (SELECT COUNT(DISTINCT(copy.id))
              FROM copy
              INNER JOIN transaction
                ON copy.id = transaction.copy
              WHERE item = $idArticle
              AND transaction.type IN (SELECT transaction_type.id
                                      FROM transaction_type
                                      WHERE transaction_type.code
                                      IN ('SELL', 'SELL_PARENT', 'AJUST_INVENTORY')))
            AS quantity;";

  include '#/connection.php';
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");
  $row = mysqli_fetch_assoc($result);

  mysqli_close($connection);
  return $row['quantity'];
}

function getTransactionDate($copyId, $transactionType) {
  $query;

  if (preg_match("/SELL/i", $transactionType)) {
    $query = "SELECT date FROM transaction WHERE copy=$copyId AND type IN (SELECT id FROM transaction_type WHERE code LIKE 'SELL%');";
  } else {
    $query = "SELECT date FROM transaction WHERE copy=$copyId AND type=(SELECT id FROM transaction_type WHERE code='$transactionType')";
  }

  include '#/connection.php';
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");
  $row = mysqli_fetch_assoc($result);
  mysqli_close($connection);

  return date( 'Y-m-d', strtotime($row['date']));
}

function itemIsOutdated($itemId) {
  $query = "SELECT COUNT(id) AS count
            FROM item WHERE id=$itemId
            AND status=(SELECT id FROM status WHERE code='VALID');";

  include '#/connection.php';
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");
  $row = mysqli_fetch_assoc($result);
  mysqli_close($connection);

  return $row['count'] == 0;
}
?>
