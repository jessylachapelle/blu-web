<!DOCTYPE html>
<html>
  <head>
    <?php include 'inc/head.php' ?>
  </head>
  <body>
    <?php include 'inc/header.php' ?>
    <main>
      <?php
      session_start();

      if (isset($_SESSION['user']) && ($_SESSION['expire'] == null || time() < $_SESSION['expire'])) {
        include 'res/ui_signalement.php';
      } else {
        include 'res/ui_admin_login.php';
      }
      ?>
    </main>
    <?php include 'inc/footer.php' ?>
  </body>
</html>
