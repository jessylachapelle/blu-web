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
function selecteurEdition($title, $label, $selectedIndex) {
  $index = 0;
  echo "<label for='$title'>$label</label>
        <select id='$title' class='selectEdition' name='$title'>";

  for ($year = Date('Y'); $year > 2009; $year--) {
    if ($index++ == $selectedIndex) {
      echo "<option value='H" . substr($year, 2, 2) . "' selected>Hiver $year</option>";
    } else {
      echo "<option value='H" . substr($year, 2, 2) . "'>Hiver $year</option>";
    }

    if ($index++ == $selectedIndex) {
      echo "<option value='A" . substr(($year - 1), 2, 2) . "' selected>Automne " . ($year - 1) . "</option>";
    } else {
      echo "<option value='A" . substr(($year - 1), 2, 2) . "'>Automne " . ($year - 1) . "</option>";
    }
  }

  echo "</select>";
}
?>
