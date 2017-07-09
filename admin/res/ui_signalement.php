<h1>Rapport d'erreurs</h1>
<?php
$errors = getErrors();

if (count($errors) > 0) { ?>
  <table class='tablesorter'>
    <thead>
      <tr>
        <th>Article</th>
        <th>Description</th>
        <th>Membre</th>
        <th>Date</th>
        <th>Supprimer</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($errors as $error) { ?>
        <tr data-article="<?php $error['id']; ?>">
          <td><?php echo $error['title']; ?></td>
          <td><?php echo $error['description']; ?></td>
          <td><?php echo $error['memberFirstName'] . " " . $error['memberLastName']; ?></td>
          <td><?php echo date('Y/m/d', strtotime($error['date'])); ?></td>
          <td>
            <a href="res/delete_signalement.php?id_signalement=<?php echo $error['id']; ?>">
              <span class='oi' data-glyph='trash'></span>
            </a>
          </td>
        </tr>
      <?php  } ?>
    </tbody>
  </table>
<?php } else { ?>
  <p>Il n'y a aucune erreur de signalée.</p>
<?php } ?>


<?php
function getErrors() {
  include '../#/connection.php';
  $query = "SELECT error.*,
                   item.name AS title,
                   member.first_name AS memberFirstName,
                   member.last_name AS memberLastName
              FROM error
              INNER JOIN item
                ON error.item=item.id
              INNER JOIN member
                ON error.member=member.no
              ORDER BY title ASC,
                       date DESC";
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");
  mysqli_close($connection);
  return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>
