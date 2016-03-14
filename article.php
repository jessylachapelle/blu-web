<?php session_start (); ?>
<!DOCTYPE html>
<html>
  <head>
    <?php include "inc/head.php"; ?>
    <title>Banque de livres usagés</title>
  </head>
  <body>
    <?php include "inc/header.php"; ?>
    <main>
      <?php
      if(isset($_GET['article'])) { 
        include 'res/article_fiche.php';
      }
      else {
        include 'res/404.php';
      }
      ?>
    </main>
    <?php include "inc/footer.php"; ?>
  </body>
</html>