<?php
if ($_POST['title'] != '' && $_POST['message'] != '') {
  $param = '';
  $id = $_POST['id'];
  $title = addslashes($_POST['title']);
  $message = nl2br(addslashes($_POST['message']));
  $start = $_POST['startDate'] == '' ? date('Y-m-d', time()) : date('Y-m-d', strtotime($_POST['startDate']));
  $end = $_POST['endDate'] == '' ? null : $end = date('Y-m-d', strtotime($_POST['endDate']));;

  if ($id == 0) {
		$query = "INSERT INTO news (title, message, start_date, end_date)
					    VALUES('$title', '$message', '$start', '$end');";
    $param = 'insert';
  } else {
    $query = "UPDATE news
                SET title='$title',
                    message='$message',
                    start_date='$start',
                    end_date='$end'
                WHERE id=$id;";
    $param = 'update';
  }

  include '../../#/connection.php';
  mysqli_query($connection, $query) or die("Query failed: '$query'");
  mysqli_close($connection);
  return redirect($param, 'true');
}

return redirect('error', 403);

function redirect($param, $value) {
  header("Location: ../nouvelles.php?$param=$value");
}
?>
