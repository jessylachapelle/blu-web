<?php
if ($_POST['titre'] != "" && $_POST['message'] != "") {
  $id = $_POST['id_nouvelle'];
  $titre = addslashes($_POST['titre']);
  $message = nl2br(addslashes($_POST['message']));

  if ($_POST['debut'] == "") {
    $debut = date("Y-m-d", time());
  } else {
    $debut = date("Y-m-d", strtotime($_POST['debut']));
	}

  if ($_POST['fin'] == "") {
		$fin = null;
	} else {
    $fin = date("Y-m-d", strtotime($_POST['fin']));
  }

  if ($_POST['id_nouvelle'] == 0) {
		$query = "INSERT INTO nouvelle (titre, message, debut, fin)
					      VALUES('$titre', '$message', '$debut', '$fin');";
    $param = "insert";
  } else {
    $query = "UPDATE nouvelle
                SET titre='$titre',
                    message='$message',
                    debut='$debut',
                    fin='$fin'
                WHERE id=$id;";
    $param = "update";
  }

  include "../../#/connection.php";
  mysqli_query($connection, $query)or die ("Query failed: '" . $query . "' " . mysqli_error());
  mysqli_close($connection);
  return redirect($param, 'true');
}
return redirect('error', 403);


function redirect($param, $value) {
  header("Location: ../nouvelles.php?$param=$value");
}
?>
