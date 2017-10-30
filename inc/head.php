<meta charset="utf-8" />
<title>Banque de livres usagés</title>
<link rel="shortcut icon" href="img/favicon.ico">
<link rel="icon" href="img/favicon.ico">
<meta name="author" content="Jessy Lachapelle" />
<meta name="robots" content="index, follow" />
<link rel="stylesheet" href="css/open-iconic.css"/>
<script src="lib/jquery.js"></script>
<script src="lib/tablesorter.js"></script>
<?php
require_once 'lib/mobileDetect.php';
$device = new Mobile_Detect;

if ($device->isMobile()) {
  include  'inc/mhead.html';
}
?>

<?php if ($device->isTablet()) { ?>
  <link rel="stylesheet/less" href="css/mobile.less" media="(orientation : portrait)" />
  <link rel="stylesheet/less" href="css/desktop.less" media="(orientation : landscape)" />
<?php } else if ($device->isMobile()) { ?>
  <link rel="stylesheet/less" href="css/mobile.less"/>
<?php } else { ?>
  <link rel="stylesheet/less" href="css/desktop.less" />
<?php } ?>

<script src="lib/less.js"></script>
<script defer="defer" src="js/model.js"></script>
<script defer="defer" src="js/script.js"></script>
