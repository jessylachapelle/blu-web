<?php
require_once '../class/member.php';
require_once '../class/telephone.php';

$member = new Membre();
$phones = [];
$city = str_replace("'", "''", $_POST['ville']);
$cityId = getCityId($city, $_POST['province']);

$tel1 = new Telephone();
$tel1->setId($_POST['idtel1']);
$tel1->setNumero($_POST['telephone1']);
$tel1->setNote(str_replace("'", "''", $_POST['note1']));

array_push($phones, $tel1);

if($_POST['telephone2'] != '') {
  $tel2 = new Telephone();
  $tel2->setId($_POST['idtel2']);
  $tel2->setNumero($_POST['telephone2']);
  $tel2->setNote(str_replace("'", "''", $_POST['note2']));

  array_push($telephones, $tel2);
}

$member->setNo($_POST['memberNo']);
$member->setAdress($_POST['address']);
$member->setCodePostal(str_replace(" ", "", $_POST['codepostal']));
$member->setVille($cityId);
$member->setTelephone($phones);
$member->setCourriel($_POST['courriel']);

echo updateMembre($member);
?>




<?php
function getCityId($city) {
  $id = 0;
  $query = "SELECT id FROM city WHERE name='$city' LIMIT 1";

  include '../#/connection.php';
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");

  while($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
  }

  mysqli_close($connection);
  return $id > 0 ? $id : insertCity($city, $state);
}

function insertCity($city, $state) {
  $query = "INSERT INTO city(name, state) VALUES ('$city', '$state')";

  include '../#/connection.php';
  mysqli_query($connection, $query) or die("Query failed: '$query'");

  $id = mysqli_insert_id($connection);

  mysqli_close($connection);
  return $id;
}

function updateMembre($member) {
  $no = $member->getNo()
  $email = $member->getCourriel();
  $address = $member->getAddress();
  $zip = $member->getCodePostal();
  $city = $member->getVille();

  $query = "UPDATE member
            SET email='$email',
                address='$address',
                zip='$zip',
                city='$city'
            WHERE no='$no';";

  include '../#/connection.php';
  mysqli_query($connection, $query) or die("Query failed: '$query'");
  mysqli_close($connection);

  foreach($member->getTelephone() as $telephone) {
    setTelephone($telephone, $no);
  }

  return true;
}

function setTelephone($phone, $memberNo) {
  $query = "";
  $id = $phone->getId();
  $number = $phone->getNumero();
  $note = $phone->getNote();

  if ($id != '' && $id != 0 && $number == '') {
    $query = "DELETE FROM phone WHERE id=$id;";
  } else if ($id == '' || $id == 0) {
    $query = "INSERT INTO phone(member, number, note)
              VALUES ($memberNo, '$number', '$note');";
  } else {
    $query = "UPDATE phone SET number='$number', note='$note' WHERE id='$id';";
  }

  include '../#/connection.php';
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");
}

?>
