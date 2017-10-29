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
    <?php include "inc/header.html"; ?>
    <main>
      <?php if (isset($_GET['article'])) { ?>
        <script defer="defer" src="./js/item.js"></script>
        <div style='margin-top: 20px;'>
          <h1 id="title"></h1>
          <table id="infocompte">
            <tr>
              <td class='boldtitre'>Auteur.e.s :</td>
              <td id="author"></td>
            </tr>
            <tr>
              <td class='boldtitre'>Éditeur :</td>
              <td id="editor"></td>
            </tr>
            <tr>
              <td class='boldtitre'>Édition :</td>
              <td id="edition"></td>
            </tr>
            <tr>
              <td class='boldtitre'>Année de parution :</td>
              <td id="publication"></td>
            </tr>
            <tr>
              <td class='boldtitre'>Code EAN13 :</td>
              <td id="ean13"></td>
            </tr>
          </table>
          <p id="quantity"></p>
        </div>
      <?php } else { ?>
        <h1>Erreur 404 - Page non trouvée</h1>
        <p>
          Il semblerait que cette page n'existe pas. Si vous pensez qu'il s'agit d'une erreur de notre part, merci de nous le signaler en nous envoyons un courriel en décrivant le problème. Merci d'avance.
        </p>
      <?php } ?>
      ?>
    </main>
    <?php include "inc/footer.php"; ?>
  </body>
</html>
