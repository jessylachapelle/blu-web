<?php
if ($_POST['titre'] != '' && $_POST['message'] != '') {
  $param = '';
  $id = $_POST['id_nouvelle'];
  $title = addslashes($_POST['titre']);
  $message = nl2br(addslashes($_POST['message']));
  $start = ($_POST['debut'] == '' ? date('Y-m-d', time()) : date('Y-m-d', strtotime($_POST['debut']));
  $end = $_POST['fin'] == '' ? null : $end = date('Y-m-d', strtotime($_POST['fin']));;

  if ($id == 0) {
		$query = "INSERT INTO new (title, message, start_date, end_date)
					    VALUES('$title', '$message', '$start', '$end');";
    $param = 'insert';
  } else {
    $query = "UPDATE new
                SET title='$title',
                    message='$message',
                    start_date='$start',
                    end_date='$end'
                WHERE id=$id;";
    $param = 'update';
  }

  include '../../#/connection.php';
  mysqli_query($connection, $query)or die ("Query failed: '$query'");
  mysqli_close($connection);
  return redirect($param, 'true');
}

return redirect('error', 403);

function redirect($param, $value) {
  header("Location: ../nouvelles.php?$param=$value");
}
?>
