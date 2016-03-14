<!DOCTYPE html>
<html>
  <head>
    <?php include 'inc/head.php'; ?>
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
 	  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="../js/nouvelle.js"></script>
 	  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  </head>
  <body>
    <?php include 'inc/header.php'; ?>
    <main id="admin-nouvelle">
      <?php
      session_start();

      if (isset($_SESSION['user']) && ($_SESSION['expire'] == null || time() < $_SESSION['expire'])) {
        include 'res/ui_nouvelles.php';
      } else {
        include 'res/ui_admin_login.php';
      }
      ?>
    </main>
    <?php include 'inc/footer.php'; ?>
  </body>
</html>
