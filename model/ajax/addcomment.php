<?php header('Content-Type: text/xml'); ?>
<proveitResponse>
<?php
require '../functions.php';
if (isset($_GET['proposition']) && isset($_GET['comment'])) {
	$proposition = "'".addslashes($_GET['proposition'])."'";
	$comment = "'".addslashes($_GET['comment'])."'";
	// Make the queries and report on their success
	if (!mysql_query("INSERT INTO prop_comments (p,official,text) VALUES ($proposition,1,$comment)")) { ?>
		<db-error>
			<query><?php print "INSERT INTO prop_comments (p,official,text) VALUES ($proposition,1,$comment)"; ?></query>
			<error>
				<? print mysql_error(); ?>
			</error>
		</db-error>
		<?php 
	} else { ?>
		<message>Comment added</message>
		<?
	}
} else { ?>
	<form-error>
		<error>
			Both proposition and comment need to be filled in
		</error>
	</form-error>
<?php } ?>
</proveitResponse>