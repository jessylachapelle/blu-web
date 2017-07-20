<?php
require_once 'class/article.php';
require_once 'res/article.php';

$article = getArticle($_GET['article']);
$stats = getArticleStats($article->getId());

if (isConnected()) { ?>
  <script>
    const memberNo = <?php echo $_SESSION['memberNo'] ?>;
    document.getElementsByTagName('main')[0].removeChild(document.body.getElementsByTagName('script')[0]);
  </script>

  <?php
  // <div id='signalement'>
  //   <form method='post' action='res/signalement.php'>
  //     <input type='hidden' id='article' name='article' value="<?php echo $article->getId() >" />
  //     <input type='hidden' id='memberNo' name='memberNo' value="<?php echo $_SESSION['memberNo']; >" />
  //     <textarea name='signal' placeholder="Décriver l'erreur que vous avez trouvé"></textarea>
  //     <button>Enregistrer</button>
  //     <button formaction='' onclick='closeSignal()'>Annuler</button>
  //   </form>
  // </div>
  ?>
<?php } ?>

<div style='margin-top: 20px;'>
  <h1>
    <?php echo $article->getTitle();
    if (isConnected()) { ?>
      <span class='oi'
            data-glyph='star'
            data-item='<?php echo $article->getId() ?>'
            data-state='<?php echo getState(); ?>'
            onclick='subscribe(this)'>
      </span>
    <?php } ?>
  </h1>
  <table class="borderless">
    <tr>
      <td class='boldtitre'>Auteur.e.s :</td>
      <td><?php echo $article->getAuthor(); ?></td>
    </tr>
    <tr>
      <td class='boldtitre'>Éditeur :</td>
      <td><?php echo $article->getEditor(); ?></td>
    </tr>
    <tr>
      <td class='boldtitre'>Édition :</td>
      <td><?php echo $article->getEdition(); ?></td>
    </tr>
    <tr>
      <td class='boldtitre'>Année de parution :</td>
      <td><?php echo $article->getYear(); ?></td>
    </tr>
    <tr>
      <td class='boldtitre'>Code EAN13 :</td>
      <td><?php echo $article->getCode(); ?></td>
    </tr>
  </table>
</div>

<?php if ($stats['quantity'] > 0) { ?>
  <p>Nous possédons <?php echo $stats['quantity'] ?> exemplaire(s) en stock de cet article et le prix moyen de vente est de <?php echo $stats['average'] ?>$.</p>
<?php } else { ?>
  <p>Nous ne possédons pas d'exemplaire en stock pour cet article. Vous pouvez le suivre pour être informer d'un éventuel approvisionnement.</p>
<?php } ?>

<?php if (isConnected()) { ?>
   <?php
    // <button onclick='openSignal()'>Signaler une erreur</button>
  ?> 
<?php } ?>
