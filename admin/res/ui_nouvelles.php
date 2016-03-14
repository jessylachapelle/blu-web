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
    $requete = 'SELECT * FROM nouvelle ORDER BY id DESC;';
    $resultat = mysqli_query($connection, $requete)or die ("Query failed: '" . $requete . "' " . mysqli_error());;

      for ($i=0; $i<5; $i++)
      {
        if($donnees = mysqli_fetch_array($resultat))
        {
    ?>
    <tr id="nouvelle_<?php echo $donnees['id'] ?>">
      <td onclick="modifier_nouvelle(<?php echo $donnees['id']?>)"><?php echo $donnees['titre']; ?></td>
      <td onclick="modifier_nouvelle(<?php echo $donnees['id']?>)"><?php echo $donnees['message']; ?></td>
      <td onclick="modifier_nouvelle(<?php echo $donnees['id']?>)"><?php echo $donnees['debut']; ?></td>
      <td onclick="modifier_nouvelle(<?php echo $donnees['id']?>)"><?php if ($donnees['fin'] != "0000-00-00") echo $donnees['fin']; ?></td>
      <td><?php echo '<a href="res/delete_nouvelle.php?id_nouvelle=' . $donnees['id'] . '">'; ?><span class="oi" data-glyph="trash"></span></a></td>
    </tr>
    <?php
       }
      }
    ?>
  </tbody>
</table>
