<h1 id="actionTitre">Ajouter une nouvelle</h1>
<p><b id="actionTexte">Vous pouvez ajouter une nouvelle au site à l'aide de ce formulaire.</b></p>

<form action="res/poste_nouvelle.php" method="post" class="nostyle">
  <input type="hidden" name="id_nouvelle" id="id_nouvelle" value="0">
  <label for="titre">Titre :</label>
  <input type="textbox" name="titre" id="titre" />
  <label for="message">Message :</label><br/>
  <textarea cols="50"; rows="10"; name="message" id="message"></textarea><br/>
  <label for="debut">Date de début :</label>
  <input type="date" name="debut" id="debut" />
  <label for="fin">Date de fin :</label>
  <input type="date" name="fin" id="fin" />
  <button onclick="resetText()" type="reset">Annuler</button>
  <button>Enregistrer</button>
</form>
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

    <?php
    include "../#/connection.php";
    $query = 'SELECT * FROM nouvelle ORDER BY id DESC;';
    $result = mysqli_query($connection, $query)or die ("Query failed: '" . $query . "' " . mysqli_error());;

      for ($i=0; $i<5; $i++)
      {
        if($row = mysqli_fetch_array($result))
        {
    ?>
    <tr id="nouvelle_<?php echo $row['id'] ?>">
      <td onclick="modifier_nouvelle(<?php echo $row['id']?>)"><?php echo $row['titre']; ?></td>
      <td onclick="modifier_nouvelle(<?php echo $row['id']?>)"><?php echo $row['message']; ?></td>
      <td onclick="modifier_nouvelle(<?php echo $row['id']?>)"><?php echo $row['debut']; ?></td>
      <td onclick="modifier_nouvelle(<?php echo $row['id']?>)"><?php if ($row['fin'] != "0000-00-00") echo $row['fin']; ?></td>
      <td><?php echo '<a href="res/delete_nouvelle.php?id_nouvelle=' . $row['id'] . '">'; ?><span class="oi" data-glyph="trash"></span></a></td>
    </tr>
    <?php
       }
      }
    ?>
  </tbody>
</table>
