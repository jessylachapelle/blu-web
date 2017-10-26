<?php session_start (); ?>
<!DOCTYPE html>
<html>
  <head>
    <?php include "inc/head.php"; ?>
    <script>
      const memberNo = <?php echo isset($_SESSION['memberNo']) ? $_SESSION['memberNo'] : null ?>;
    </script>
  </head>
  <body>
    <?php include "inc/header.php"; ?>
    <main>
      <?php
      if (isset($_GET['article'])) {
        include 'view/ui_article.html';
      } else {
        include 'error/404.php';
      }
      ?>
    </main>
    <?php include "inc/footer.php"; ?>
  </body>
</html>
