<h1 id="actionTitre">Ajouter une nouvelle</h1>
<p><b id="actionTexte">Vous pouvez ajouter une nouvelle au site à l'aide de ce formulaire.</b></p>

<form action="res/poste_nouvelle.php" method="post" class="nostyle">
  <input type="hidden" name="id" id="id" value="0">
  <label for="title">Titre :</label>
  <input type="text" name="title" id="title" />
  <label for="message">Message :</label><br/>
  <textarea cols="50"; rows="10"; name="message" id="message"></textarea><br/>
  <label for="startDate">Date de début :</label>
  <input type="date" name="startDate" id="startDate" />
  <label for="endDate">Date de fin :</label>
  <input type="date" name="endDate" id="endDate" />
  <button onclick="resetText()" type="reset">Annuler</button>
  <button type="submit">Enregistrer</button>
</form>
<?php
$newsFeed = getNewsfeed();

if (count($newsFeed) > 0) { ?>
  <table  class='tablesorter'>
    <thead>
      <tr>
        <th>Titre</th>
        <th>Message</th>
        <th>Date début</th>
        <th>Date fin</th>
        <th>Delete</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($newsFeed as $news) {
        $id = $news['id'];
        $title = $news['title'];
        $message = $news['message'];
        $startDate = date('Y/m/d', strtotime($news['start_date']));
        $endDate = $news['end_date'] != "0000-00-00" ? date('Y/m/d', strtotime($news['end_date'])) : '';
      ?>
        <tr id="nouvelle_<?php echo $id; ?>">
          <td onclick="modifier_nouvelle(<?php echo $id; ?>)"><?php echo $title; ?></td>
          <td onclick="modifier_nouvelle(<?php echo $id; ?>)"><?php echo $message; ?></td>
          <td onclick="modifier_nouvelle(<?php echo $id; ?>)"><?php echo $startDate; ?></td>
          <td onclick="modifier_nouvelle(<?php echo $id; ?>)"><?php echo $endDate; ?></td>
          <td>
            <a href="res/delete_nouvelle.php?id_nouvelle=<?php echo $id; ?>">
              <span class="oi" data-glyph="trash"></span>
            </a>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
<?php } else { ?>
  <p>Vous n'avez aucune nouvelle de publiée</p>
<?php } ?>

<?php
function getNewsfeed() {
  $query = 'SELECT * FROM news ORDER BY start_date ASC, end_date ASC, title ASC;';
  include '../#/connection.php';
  $result = mysqli_query($connection, $query) or die("Query failed: '$query'");
  mysqli_close($connection);
  return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>
