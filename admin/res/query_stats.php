<?php
if(isset($_POST['f'])) {
  switch ($_POST['f']) {
    case "transactionDate":
      echo json_encode(statsParDate($_POST['data']));
      break;
    case "transactionIntervale":
      echo json_encode(statsParEdition($_POST['data']));
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

function statsParDate($date) {
  return [
    "misEnVente" => statistiquesTransaction($date, 1),
    "vente" => statistiquesTransaction($date, 2),
    "venteParentEtudiant" => statistiquesTransaction($date, 3),
    "economieParentEtudiant" => economieParentEtudiant($date),
    "argentRemis" => statistiquesTransaction($date, 4)
  ];
}

function statsParEdition($edition) {
  $annee = "20" . substr($edition, 1, 2);

  if(substr($edition, 0, 1) == "A")
    return statsParDate(["$annee-08-01", "$annee-12-31"]);
  return statsParDate(["$annee-01-01", "$annee-07-31"]);
}

function statistiquesTransaction($date, $typeTransaction) {
  $query = "SELECT COUNT(*) AS quantite,
                   SUM(exemplaire.prix) AS montant
            FROM transaction
            INNER JOIN exemplaire
              ON transaction.id_exemplaire=exemplaire.id
            WHERE id_type=$typeTransaction ";

  if(is_array($date)) {
    $query .= "AND date>='$date[0]' AND date<='$date[1]';";
  } else {
    $query .= "AND date='$date';";
  }

  include "../../#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
  $row = mysqli_fetch_assoc($result);

  $stats = [
    "quantite" => $row['quantite'],
    "montant" => $row['montant']
  ];

  mysqli_close($connection);
  return $stats;
}

function economieParentEtudiant($date) {
  $query = "SELECT exemplaire.prix
            FROM transaction
            INNER JOIN exemplaire
              ON transaction.id_exemplaire=exemplaire.id
            WHERE id_type=3 ";

  if(is_array($date)) {
    $query .= "AND date>='$date[0]' AND date<='$date[1]';";
  } else {
    $query .= "AND date='$date';";
  }

  include "../../#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());

  $economie = 0;

  while($row = mysqli_fetch_assoc($result)) {
    $economie += ceil($row['prix']/2);
  }

  mysqli_close($connection);
  return $economie;
}

function argentTotalARemettre($compteActif) {
  $membres = getMembreAvecRemise($compteActif);
  $total = 0;

  foreach ($membres as &$membre) {
    $montant = ArgentRemettreParMembre($membre['no']);
    $membre['montant'] = $montant;

    if($membre['montant'] == 0) {
      unset($membres[$membre['no']]);
    } else {
     $total += $membre['montant'];
    }
  }

  $membres['total'] = $total;
  return $membres;
}

function getMembreAvecRemise($compteActif) {
  $date = (Date('Y') - 1) . "-" . Date('m') . "-" . Date('d');
  $symb = '<=';

  if($compteActif)
    $symb = '>=';

  $query = "SELECT no, prenom, nom
            FROM membre
            INNER JOIN transaction
              ON membre.no=transaction.no_membre
            WHERE derniere_activite$symb'$date'
            ORDER BY nom, prenom, no;";

  include "../../#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());

  $membres = [];

  while($row = mysqli_fetch_assoc($result)) {
    $membre = [
      'no' => $row['no'],
      'nom' => utf8_encode($row['nom']),
      'prenom' => utf8_encode($row['prenom']),
      'montant' => 0
    ];

    $membres[$row['no']] = $membre;
  }

  mysqli_close($connection);
  return $membres;
}

function ArgentRemettreParMembre($noMembre) {
  $query = "SELECT SUM(exemplaire.prix) AS montant
            FROM transaction
            INNER JOIN exemplaire
              ON transaction.id_exemplaire=exemplaire.id
            WHERE no_membre=$noMembre
              AND (transaction.id_type=2
                  OR transaction.id_type=3)
              AND id_exemplaire NOT IN(SELECT id_exemplaire
                                       FROM transaction
                                       WHERE no_membre=$noMembre
                                       AND id_type=4);";

   include "../../#/connection.php";
   $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
   $row = mysqli_fetch_assoc($result);

   $montant = $row['montant'];
   mysqli_close($connection);

   if ($montant == null)
     $montant = 0;
   return $montant;
}

function getActifPassifBLU() {
  return [
    "actif" => compteBLU(true),
    "passif" => compteBLU(false)
  ];
}

function compteBLU($actif) {
  $date = (Date('Y') - 1) . "-" . Date('m') . "-" . Date('d');
  $typeTransaction = "WHERE transaction.id_type=1 AND exemplaire.id NOT IN(SELECT id_exemplaire FROM transaction WHERE id_type=2 OR id_type=3)";

  if($actif) {
    $typeTransaction = "WHERE (transaction.id_type=2 OR transaction.id_type=3) AND exemplaire.id NOT IN(SELECT id_exemplaire FROM transaction WHERE id_type=4)";
  }

  $query = "SELECT SUM(exemplaire.prix) AS montant,
                   COUNT(exemplaire.prix) AS quantite
            FROM exemplaire
            INNER JOIN transaction
              ON exemplaire.id=transaction.id_exemplaire
            INNER JOIN membre
              ON transaction.no_membre=membre.no
            $typeTransaction
            AND transaction.no_membre IN(SELECT no FROM membre WHERE derniere_activite>='$date');";

  include "../../#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
  $row = mysqli_fetch_assoc($result);

  $data = [
    "montant" => $row['montant'],
    "quantite" => $row['quantite']
  ];

  mysqli_close($connection);
  return $data;
}

function livresValidesNonVendus() {
  $query = "SELECT article.*, pv.valeur AS categorie
            FROM article
            INNER JOIN propriete_article pa
              ON article.id=pa.id_article
            INNER JOIN propriete_valeur pv
              ON pa.id_propriete_valeur=pv.id
            WHERE article.id
              NOT IN(SELECT DISTINCT(exemplaire.id_article)
                     FROM exemplaire
                     INNER JOIN transaction
                      ON exemplaire.id = transaction.id_exemplaire
                     WHERE (transaction.id_type=2 OR transaction.id_type=3)
                     AND transaction.date > (DATE_SUB(CURDATE(), INTERVAL 2 YEAR)))
            AND article.id
              NOT IN (SELECT pa.id_article
                      FROM propriete_article pa
                      INNER JOIN propriete_valeur pv
                        ON pa.id_propriete_valeur=pv.id
                      WHERE pv.id_propriete=15
                      AND pv.valeur IS NOT NULL)
            AND pv.id_propriete=8
            AND pv.id != 5092";

    include "../../#/connection.php";
    $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());

    $articles = [];

    while($row = mysqli_fetch_assoc($result)) {
      $article = [
        'id' => $row['id'],
        'title' => $row['nom'],
        'category' => $row['categorie']
      ];

      $articles[$row['id']] = $article;
    }

    mysqli_close($connection);
    return $articles;
}
?>
