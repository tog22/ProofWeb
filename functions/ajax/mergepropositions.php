<?php header('Content-Type: text/xml'); ?>
<proveitResponse>
<?php
require '../functions.php';
function props_merge ($merge, $into) {
	$implications = db_fetch("SELECT implications FROM propositions WHERE p = $merge", CAN_BE_EMPTY);
	$implicators = db_fetch("SELECT implicators FROM propositions WHERE p = $merge", CAN_BE_EMPTY);
	if ($implications) {
		$old_implications = db_fetch("SELECT implications FROM propositions WHERE p = $into", CAN_BE_EMPTY);
		if ($old_implications) {
			$merged_implications = $old_implications.','.$implications;
			db_query("UPDATE propositions SET implications = '$merged_implications' WHERE p = $into");
		} else {
			db_query("UPDATE propositions SET implications = '$implications' WHERE p = $into");
		}
		$implications_array = explode(',', $implications);
		foreach ($implications_array as $implication) {
			$implication_props = db_fetch("SELECT p1,p2,p3,p4 FROM implications WHERE id = $implication");
			for ($i = 0; $i <= 3; $i++) {
				if ($implication_props[$i] == $merge) {
					$j = $i + 1;
					db_query("UPDATE implications SET p$j = $into WHERE id = $implication");
				}
			}
		}
	}
	if ($implicators) {
		$old_implicators = db_fetch("SELECT implicators FROM propositions WHERE p = $into", CAN_BE_EMPTY);
		if ($old_implicators) {
			$merged_implicators = $old_implicators.','.$implicators;
			db_query("UPDATE propositions SET implicators = '$merged_implicators' WHERE p = $into");
		} else {
			db_query("UPDATE propositions SET implicators = '$implicators' WHERE p = $into");
		}
		$implicators_array = explode(',', $implicators);
		foreach ($implicators_array as $implicator) {
			$implicator_c = db_fetch("SELECT c FROM implications WHERE id = $implicator");
			if ($implicator_c == $merge) {
				db_query("UPDATE implications SET c = $into WHERE id = $implicator");
			}
		}
	}
}
$m1 = $_GET['merge'];
$i1 = $_GET['into'];
if (!is_numeric($m1) || !is_numeric($i1)) { ?>
	<message>Error: IDs aren't numeric</message>
<? } else if (!($m1 % 2) || !($i1 % 2)) { ?>
	<message>Error: IDs aren't odd</message>
<? } else {
	props_merge($m1, $i1);
	$m2 = $m1 + 1;
	props_merge($m2, $i1 + 1);
	db_query('DELETE FROM propositions WHERE p = '.$m1);
	db_query('DELETE FROM propositions WHERE p = '.$m2); ?>
	<message>Done</message>
<? }
?>
</proveitResponse>