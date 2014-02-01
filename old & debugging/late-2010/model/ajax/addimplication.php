<?php header('Content-Type: text/xml'); ?>
<proveitResponse>
<?php
require '../functions.php';
if ($_GET['p1'] && $_GET['c']) {
	create_implication($_GET['c'],$_GET['p1'],$_GET['p2'],$_GET['p3'],$_GET['p4']);
	if (!$_GET['p2']) { // 1 premise version
		create_converse_implication($_GET['p1'],$_GET['c']);
	} else if (!$_GET['p3']) { // 2 premise versions
		create_converse_implication($_GET['p1'],$_GET['c'],$_GET['p2']);
		create_converse_implication($_GET['p2'],$_GET['c'],$_GET['p1']);
	} else if (!$_GET['p4']) { // 3 premise versions
		create_converse_implication($_GET['p1'],$_GET['c'],$_GET['p2'],$_GET['p3']);
		create_converse_implication($_GET['p2'],$_GET['c'],$_GET['p1'],$_GET['p3']);
		create_converse_implication($_GET['p3'],$_GET['c'],$_GET['p1'],$_GET['p2']);
	} else { // 4 premise versions
		create_converse_implication($_GET['p1'],$_GET['c'],$_GET['p2'],$_GET['p3'],$_GET['p4']);
		create_converse_implication($_GET['p2'],$_GET['c'],$_GET['p1'],$_GET['p3'],$_GET['p4']);
		create_converse_implication($_GET['p3'],$_GET['c'],$_GET['p1'],$_GET['p2'],$_GET['p4']);
		create_converse_implication($_GET['p4'],$_GET['c'],$_GET['p1'],$_GET['p2'],$_GET['p3'],$_GET['p4']);
	}
}
?>
</proveitResponse>