<?php session_start (); ?>
<!DOCTYPE html>
<html>
  <head>
    <?php include "inc/head.php"; ?>
  </head>
  <body>
    <?php include "inc/header.php"; ?>
    <main>
      <?php
      if (isset($_GET['article'])) {
        include 'view/ui_article.php';
      } else {
        include 'error/404.php';
      }
      ?>
    </main>
    <?php include "inc/footer.php"; ?>
  </body>
</html>
