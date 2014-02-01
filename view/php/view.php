<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
	<title>
		<?php
		require '../../model/functions.php';
		$type = mysql_real_escape_string($_GET['type']);
		$id = mysql_real_escape_string($_GET['id']);
		if (is_numeric($id)) {
			if ($type == 'implication') {
				print "Implication $id | ProofWeb";
			} else if ($type == 'argument') {
				if ($_arg_name = mysql_query("SELECT name FROM arguments WHERE id = $id")) {
					$arg_name =  mysql_fetch_row($_arg_name);
					print $arg_name[0] . ' | ProofWeb';
				} else {
					print 'Invalid argument ID';
				}
			}
		}
		?>
	</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link type="text/css" rel="stylesheet" media="all" href="../styles.css" />

</head>
<body>
<div id="page">
	<?php
		if ($_GET['dev']) {
			$devMode = TRUE;
		}
		if ($id != 'all') {
			switch ($type) {
				case 'implication':
					show_implication($id);
					break;
				case 'argument':
					show_argument($id);
					break;
			}
		} else {
			switch ($type) {
				case 'implication':
					$maxid = mysql_fetch_row(mysql_query("SELECT MAX(id) FROM implications"));
					for ($i = 1; $i <= $maxid; $i++) {
						show_implication($i);
					}
					break;
				case 'argument':
					$maxid = mysql_fetch_row(mysql_query("SELECT MAX(id) FROM arguments"));
					for ($i = 1; $i <= $maxid; $i++) {
						show_argument($i);
					}
					break;
			}
		}
	?>
</div>
</body>
</html>