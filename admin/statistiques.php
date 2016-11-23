<!DOCTYPE html>
<html>
  <head>
    <?php include 'inc/head.php' ?>
    <script src="../js/Chart.min.js"></script>
    <script src="../js/stats.js" defer="defer"></script>
    <style>
      div {
        width: 250px;
      }

      #transactionDate, #transactionIntervale, #choixStat {
        display: none;
      }
    </style>
  </head>
  <body>
    <?php include 'inc/header.php' ?>
    <main>
      <?php
      session_start();

      if(isset($_SESSION['user']) && ($_SESSION['expire'] == null || time() < $_SESSION['expire'])) {
        include 'res/ui_statistiques.php';
      } else {
       include 'res/ui_admin_login.php';
      }
      ?>
    </main>
    <?php include 'inc/footer.php' ?>
  </body>
</html>


<?php
function selecteurEdition($titre, $label, $selectedIndex) {
  $index = 0;
  echo "<label for='$titre'>$label</label>
        <select id='$titre' class='selectEdition' name='$titre'>";

  for($annee = Date('Y'); $annee > 2009; $annee--) {
    if($index++ == $selectedIndex)
      echo "<option value='H" . substr($annee, 2, 2) . "' selected>Hiver $annee</option>";
    else
      echo "<option value='H" . substr($annee, 2, 2) . "'>Hiver $annee</option>";

    if($index++ == $selectedIndex)
      echo "<option value='A" . substr(($annee - 1), 2, 2) . "' selected>Automne " . ($annee - 1) . "</option>";
    else
      echo "<option value='A" . substr(($annee - 1), 2, 2) . "'>Automne " . ($annee - 1) . "</option>";
  }

  echo "</select>";
}
?>
