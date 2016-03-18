<?php
require_once "../class/member.php";
require_once "../class/telephone.php";

$member = new Membre();
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

$member->setNo($_POST['memberNo']);
$member->setNoCivic($_POST['nocivic']);
$member->setRue($rue = str_replace("'", "''", $_POST['rue']));
$member->setApp(str_replace("'", "''", $_POST['app']));
$member->setCodePostal(str_replace(" ", "", $_POST['codepostal']));
$member->setVille($villeId);
$member->setTelephone($telephones);
$member->setCourriel($_POST['courriel']);

echo updateMembre($member);
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

function updateMembre($member) {
  $query = "UPDATE membre
            SET courriel='" . $member->getCourriel() ."',
                no_civic='" . $member->getNoCivic() . "',
                rue='" . $member->getRue() . "',
                app='" . $member->getApp() . "',
                code_postal='" . $member->getCodePostal() . "',
                id_ville='" . $member->getVille() . "'
            WHERE no='" . $member->getNo() . "'";

  include "../#/connection.php";
  mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());

  foreach($member->getTelephone() as $telephone) {
    setTelephone($telephone, $member->getNo());
  }
  
  mysqli_close($connection);

  return true;
}

function setTelephone($telephone, $memberNo) {
  $query = "";

  if($telephone->getId() != "" && $telephone->getId() != 0 && $telephone->getNumero() == "")
    $query = "DELETE FROM telephone WHERE id='" . $telephone->getId() . "'";
  elseif($telephone->getId() == 0 || $telephone->getId() == "")
    $query = "INSERT INTO telephone(no_membre, numero, note) VALUES ('$memberNo', '" . $telephone->getNumero() . "', '" . $telephone->getNote() . "')";
  else
    $query = "UPDATE telephone SET numero='" . $telephone->getNumero() . "', note='" . $telephone->getNote() . "' WHERE id='" . $telephone->getId() . "'";

  include "../#/connection.php";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query' " . mysqli_error());
}

?>
