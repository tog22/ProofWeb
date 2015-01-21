<?php header('Content-Type: text/xml'); ?>
<proveitResponse>
<?php
require '../functions.php';
$p = addslashes($_GET['p']);
$b = addslashes($_GET['b']);
$c = addslashes($_GET['c']);
set_attitude_ajax($p,$b,$c);
?>
</proveitResponse>