<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Arguments for and against | ProofWeb</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<link type="text/css" rel="stylesheet" media="all" href="../styles.css" />
	<script type="text/javascript" src="libraries/core.js"></script>
	<script type="text/javascript" src="libraries/ajax.js"></script>
	<script type="text/javascript" src="libraries/toglibrary.js"></script>
	<script type="text/javascript" src="libraries/formdata2querystring.js"></script>
	<script type="text/javascript">
	</script>
	<?php
		require '../../functions/functions.php';
		$id = mysql_real_escape_string($_GET['id']);
	?>
</head>
<body>
<!-- get html for arg by calling view.php & insert here - can we grap the paramaeters of this page using JavaScript rather than PHP? (is there any reason to bother besides as a learning exercise, given that seeing a new argument can acceptably require a page refresh? I've got PHP parsing power to burn -->
	<div id="main">
		<h1>Arguments for and against...</h1>
		<h1 id="for-and-against-prop">
			"<?php print db_fetch("SELECT text FROM propositions WHERE p = $id"); 
			?>"
		</h1>
		<p><em>Remember to disable your cache!</em></p>
		<h2>Named arguments</h2>
		<?php
		$implicators = db_query("SELECT implicators FROM propositions WHERE p = $id");
		$converse_id = get_converse($id);
		$converse_implicators = db_query("SELECT implicators FROM propositions WHERE p = $converse_id");
		$arguments_for = array();
		while ($_implicator = mysql_fetch_row($implicators)) {
			$implicator = $_implicator[0];
			if ($argument_for_to_add = db_fetch("SELECT id, name FROM arguments WHERE subargs LIKE '%$implicator'", CAN_BE_EMPTY)) {
				$arguments_for[] = $argument_for_to_add;
			}
		}
		if (!$arguments_for[0]) { ?>
			<p>No arguments for</p>
		<?php } else { ?>
			<p><strong>For:</strong>
			<?php foreach ($arguments_for as $argument_for) { ?>
				<br />
				<a href="view.php?type=argument&id=<?php print $argument_for[0]; ?>"><?php print $argument_for[1]; ?></a>
			<?php } ?>
			</p>
		<?php }
		?>
		<h2>All implicators</h2>
		need to add, test 229 to see if need to add SEP 1.3(?) arg from hume, tho need to test phraisng of it
		<?php
		?>
	</div>
</body>
</html>
