<?php
require_once "../class/membre.php";
require_once "../class/telephone.php";

$membre = new Membre();
$telephones = array();
$ville = str_replace("'", "''", $_POST['ville']);
$province = $_POST['province'];
$villeId = getIdVille($ville);

if($villeId == 0) {
  $villeId = insertVille($ville, $province);
}

$tel1 = new Telephone();
$tel1->setId($_POST['idtel1']);
$tel1->setNumero($_POST['telephone1']);
$tel1->setNote(str_replace("'", "''", $_POST['note1']));

array_push($telephones, $tel1);

if($_POST['telephone2'] != "") {
  $tel2 = new Telephone();
  $tel2->setId($_POST['idtel2']);
  $tel2->setNumero($_POST['telephone2']);
  $tel2->setNote(str_replace("'", "''", $_POST['note2']));

  array_push($telephones, $tel2);
}

$membre->setNo($_POST['nomembre']);
$membre->setNoCivic($_POST['nocivic']);
$membre->setRue($rue = str_replace("'", "''", $_POST['rue']));
$membre->setApp(str_replace("'", "''", $_POST['app']));
$membre->setCodePostal(str_replace(" ", "", $_POST['codepostal']));
$membre->setVille($villeId);
$membre->setTelephone($telephones);
$membre->setCourriel($_POST['courriel']);

echo updateMembre($membre);
?>




<?php
function getIdVille($ville) {
  $id = 0;
  $query = "SELECT id FROM ville WHERE nom='$ville' LIMIT 1";

  include "../#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());

  while($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
  }

  mysqli_close($connection);
  return $id;
}

function insertVille($ville, $province) {
  $query = "INSERT INTO ville(nom, code_province) VALUES ('$ville', '$province')";

  include "../#/connection.php";
  mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());

  $id = mysqli_insert_id($connection);

  mysqli_close($connection);
  echo $id;
}

function updateMembre($membre) {
  $query = "UPDATE membre
            SET courriel='" . $membre->getCourriel() ."',
                no_civic='" . $membre->getNoCivic() . "',
                rue='" . $membre->getRue() . "',
                app='" . $membre->getApp() . "',
                code_postal='" . $membre->getCodePostal() . "',
                id_ville='" . $membre->getVille() . "'
            WHERE no='" . $membre->getNo() . "'";

  include "../#/connection.php";
  mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());

  foreach($membre->getTelephone() as $telephone) {
    setTelephone($telephone, $membre->getNo());
  }
  
  mysqli_close($connection);

  return true;
}

function setTelephone($telephone, $noMembre) {
  $query = "";

  if($telephone->getId() != "" && $telephone->getId() != 0 && $telephone->getNumero() == "")
    $query = "DELETE FROM telephone WHERE id='" . $telephone->getId() . "'";
  elseif($telephone->getId() == 0 || $telephone->getId() == "")
    $query = "INSERT INTO telephone(no_membre, numero, note) VALUES ('$noMembre', '" . $telephone->getNumero() . "', '" . $telephone->getNote() . "')";
  else
    $query = "UPDATE telephone SET numero='" . $telephone->getNumero() . "', note='" . $telephone->getNote() . "' WHERE id='" . $telephone->getId() . "'";

  include "../#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
}

?>
