<?php header('Content-Type: text/xml'); ?>
<proveitResponse>
<?php
require '../functions.php';
if ($_GET['p1'] && $_GET['c']) {
	if ($probabilisation = $_GET['probabilistic']) {
		create_implication($_GET['c'],$probabilisation,$_GET['p1'],$_GET['p2'],$_GET['p3'],$_GET['p4']);
		$converse_prob = 100 - $probabilisation;
		create_implication(get_converse($_GET['c']),$converse_prob,$_GET['p1'],$_GET['p2'],$_GET['p3'],$_GET['p4']);
	} else {
		create_implication($_GET['c'],DEDUCTIVE,$_GET['p1'],$_GET['p2'],$_GET['p3'],$_GET['p4']);
		if (!$_GET['p2']) { // 1 premise version
			create_converse_implication($_GET['p1'],DEDUCTIVE,$_GET['c']);
		} else if (!$_GET['p3']) { // 2 premise versions
			create_converse_implication($_GET['p1'],DEDUCTIVE,$_GET['c'],$_GET['p2']);
			create_converse_implication($_GET['p2'],DEDUCTIVE,$_GET['c'],$_GET['p1']);
		} else if (!$_GET['p4']) { // 3 premise versions
			create_converse_implication($_GET['p1'],DEDUCTIVE,$_GET['c'],$_GET['p2'],$_GET['p3']);
			create_converse_implication($_GET['p2'],DEDUCTIVE,$_GET['c'],$_GET['p1'],$_GET['p3']);
			create_converse_implication($_GET['p3'],DEDUCTIVE,$_GET['c'],$_GET['p1'],$_GET['p2']);
		} else { // 4 premise versions
			create_converse_implication($_GET['p1'],DEDUCTIVE,$_GET['c'],$_GET['p2'],$_GET['p3'],$_GET['p4']);
			create_converse_implication($_GET['p2'],DEDUCTIVE,$_GET['c'],$_GET['p1'],$_GET['p3'],$_GET['p4']);
			create_converse_implication($_GET['p3'],DEDUCTIVE,$_GET['c'],$_GET['p1'],$_GET['p2'],$_GET['p4']);
			create_converse_implication($_GET['p4'],DEDUCTIVE,$_GET['c'],$_GET['p1'],$_GET['p2'],$_GET['p3'],$_GET['p4']);
		}
	}
}
?>
</proveitResponse>