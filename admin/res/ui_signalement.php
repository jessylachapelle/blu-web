<h1>Rapport d'erreurs</h1>
<?php
include "../#/connection.php";
$query = "SELECT erreur.*,
                   article.nom AS article_nom,
                   membre.prenom AS membre_prenom,
                   membre.nom AS membre_nom
            FROM erreur
            INNER JOIN article
              ON erreur.id_article=article.id
            INNER JOIN membre
              ON erreur.no_membre=membre.no
            ORDER BY article_nom ASC,
                     date DESC";
$result = mysqli_query($connection, $query)or die ("Query failed: '" . $query . "' " . mysqli_error());
$content = null;

while($row = mysqli_fetch_array($result)) {
  $content .= "<tr data-article='" . $row['id'] . "'>
                <td>" . $row['article_nom'] . "</td>
                <td>" . $row['description'] . "</td>
                <td>" . $row['membre_prenom'] . " " . $row['membre_nom'] . "</td>
                <td>" . date("d-m-Y", strtotime($row['date'])) . "</td>
                <td>
                  <a href='res/delete_signalement.php?id_signalement=" . $row['id'] . "'>
                    <span class='oi' data-glyph='trash'></span>
                  </a>
                </td>
              </tr>";
}

if ($content) {
  echo "<table class='tablesorter'>
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
            $content
          </tbody>
        </table>";
} else {
  echo "<p>Il n'y a aucune erreur de signalée.</p>";
}
?>
