<?php
if(isset($_POST['f'])) {
  switch ($_POST['f']) {
    case "transactionDate":
      echo json_encode(statsByDate($_POST['data']));
      break;
    case "transactionIntervale":
      echo json_encode(statsByEdition($_POST['data']));
      break;
    case "argentARemettre":
      echo json_encode(argentTotalARemettre($_POST['data']));
      break;
    case "blu":
      echo json_encode(getActifPassifBLU());
      break;
    case "livresValidesNonVendus":
      echo json_encode(livresValidesNonVendus());
      break;
  }
}

function statsByDate($date) {
  return [
    'misEnVente' => statistiquesTransaction($date, 'ADD'),
    'vente' => statistiquesTransaction($date, 'SELL'),
    'venteParentEtudiant' => statistiquesTransaction($date, 'SELL_PARENT'),
    'economieParentEtudiant' => economieParentEtudiant($date),
    'argentRemis' => statistiquesTransaction($date, 'PAY')
  ];
}

function statsByEdition($edition) {
  $year = '20' . substr($edition, 1, 2);
  $semester = substr($edition, 0, 1);
  $interval = $semester == 'A' ? ["$year-08-01", "$year-12-31"] : ["$year-01-01", "$year-07-31"];
  return statsByDate($interval);
}

function statistiquesTransaction($date, $transactionType) {
  $query = "SELECT COUNT(*) AS quantity,
                   SUM(copy.price) AS amount
            FROM transaction
            INNER JOIN copy
              ON transaction.copy=copy.id
            INNER JOIN transaction_type
              ON transaction.type=transaction_type.id
            WHERE transaction_type.code='$transactionType' ";
  $query .= is_array($date) ? "AND date>='$date[0]' AND date<='$date[1]';" : "AND date='$date';" ;

  include '../../#/connection.php';
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");
  $row = mysqli_fetch_assoc($result);
  mysqli_close($connection);

  $stats = [
    'quantite' => $row['quantity'],
    'montant' => $row['amount']
  ];

  return $stats;
}

function economieParentEtudiant($date) {
  $query = "SELECT copy.price
            FROM transaction
            INNER JOIN copy
              ON transaction.copy=copy.id
            INNER JOIN transaction_type
              ON transaction.type=transaction_type.id
            WHERE transaction_type.code='SELL_PARENT' ";
  $query .= is_array($date) ? "AND date>='$date[0]' AND date<='$date[1]';" : "AND date='$date';" ;

  include '../../#/connection.php';
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");

  $savings = 0;
  while($row = mysqli_fetch_assoc($result)) {
    $savings += ceil($row['price']/2);
  }

  mysqli_close($connection);
  return $savings;
}

function argentTotalARemettre($compteActif) {
  $members = getMembreAvecRemise($compteActif);
  $total = 0;

  foreach ($members as $member) {
    $amount = ArgentRemettreParMembre($member['no']);
    $member['montant'] = $amount;

    if ($member['montant'] == 0) {
      unset($members[$member['no']]);
    } else {
     $total += $member['montant'];
    }
  }

  $members['total'] = $total;
  return $members;
}

function getMembreAvecRemise($compteActif) {
  $date = (Date('Y') - 1) . "-" . Date('m') . "-" . Date('d');
  $symb = $compteActif ? '>=' : '<=';

  $query = "SELECT no, first_name, last_name
            FROM member
            INNER JOIN transaction
              ON member.no=transaction.member
            WHERE last_activity $symb '$date'
            ORDER BY nom, prenom, no;";

  include '../../#/connection.php';
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");

  $members = [];
  while($row = mysqli_fetch_assoc($result)) {
    $members[$row['no']] = [
      'no' => $row['no'],
      'nom' => utf8_encode($row['first_name']),
      'prenom' => utf8_encode($row['last_name']),
      'montant' => 0
    ];
  }

  mysqli_close($connection);
  return $members;
}

function ArgentRemettreParMembre($memberNo) {
  $query = "SELECT SUM(copy.prix) AS amount
            FROM transaction
            INNER JOIN copy
              ON transaction.copy=copy.id
            INNER JOIN transaction_type
              ON transaction.type=transaction_type.id
            WHERE no_membre=$memberNo
            AND transaction_type.code LIKE 'SELL%'
            AND copy NOT IN(SELECT copy
                            FROM transaction
                            INNER JOIN transaction_type
                              ON transaction.type=transaction_type.id
                            WHERE member=$memberNo
                            AND transaction_type.code IN ('PAY', 'DONATE'));";

   include '../../#/connection.php';
   $result = mysqli_query($connection, $query) or die("Query failed: '$query'");
   $row = mysqli_fetch_assoc($result);
   mysqli_close($connection);

   return $row['amount'] == null ? 0 : $row['amount'];
}

function getActifPassifBLU() {
  return [
    'actif' => compteBLU(true),
    'passif' => compteBLU(false)
  ];
}

function compteBLU($actif) {
  $date = (Date('Y') - 1) . "-" . Date('m') . "-" . Date('d');
  $typeTransaction = "WHERE transaction.type=(SELECT id FROM transaction_type WHERE code='ADD')
                      AND copy.id NOT IN(SELECT copy FROM transaction WHERE type IN(SELECT id FROM transaction_type WHERE code LIKE 'SELL%'))";

  if ($actif) {
    $typeTransaction = "WHERE transaction.type IN(SELECT id FROM transaction_type WHERE code LIKE 'SELL%')
                        AND copy.id NOT IN(SELECT copy FROM transaction WHERE type IN(SELECT id FROM transaction_type WHERE code='PAY'))";
  }

  $query = "SELECT SUM(copy.price) AS amount,
                   COUNT(copy.price) AS quantity
            FROM copy
            INNER JOIN transaction
              ON copy.id=transaction.copy
            INNER JOIN membre
              ON transaction.member=member.no
            $typeTransaction
            AND transaction.member IN(SELECT no FROM member WHERE last_activity>='$date');";

  include '../../#/connection.php';
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");
  $row = mysqli_fetch_assoc($result);

  $data = [
    'montant' => $row['amount'],
    'quantite' => $row['quantity']
  ];

  mysqli_close($connection);
  return $data;
}

function livresValidesNonVendus() {
  $query = "SELECT item.id, item.name, subject.name, category.name
            FROM item
            INNER JOIN subject
              ON item.subject=subject.id
            INNER JOIN category
              ON subject.category=category.id
            INNER JOIN status
              ON item.status=status.id
            WHERE status.code='VALID'
            AND article.id NOT IN(SELECT DISTINCT(copy.item)
                                  FROM copy
                                  INNER JOIN transaction
                                    ON copy.id=transaction.copy
                                  INNER JOIN transaction_type
                                    ON transaction.type=transaction_type.id
                                  WHERE transaction_type.code LIKE 'SELL%'
                                  AND transaction.date > (DATE_SUB(CURDATE(), INTERVAL 2 YEAR)));";

    include '../../#/connection.php';
    $result = mysqli_query($connection, $query) or die("Query failed: '$query'");

    $items = [];
    while($row = mysqli_fetch_assoc($result)) {
      $items[$row['id']] = [
        'id' => $row['id'],
        'title' => $row['name'],
        'subject' => $row['subject'],
        'category' => $row['category']
      ];
    }

    mysqli_close($connection);
    return $items;
}
?>
