<?php

	include "sql-login.php";

	$db = mysql_connect("localhost", $usern, $passw);
	mysql_select_db($dbname, $db);
	mysql_set_charset('utf8',$db); 
	$u = 1;
	// $id = 1; - I was creating new $id variables in lots of functions - is this global one needed?
	$devMode = FALSE;
	
	define('BELIEF', 0);
	define('COMMITMENT', 1);
	define('ACCEPT', 1);
	define('REJECT', 2);
	define('NEUTRAL', 'NULL'); // check whether this needs to be a number to prevent BUG with checking whether there's a db row set
	define('NOTSET', 5); // This means not to change what's in the database - it's not a value in the DB itself so needn't be NULL
	define('TEST', 4);
	// Values for propositions.definitional and propositions.logical
	define('TRUTH',1);
	define('FALSITY',-1);
	// $probabilistic in add_implication() 
	define('DEDUCTIVE','NULL');
	define('PROBABLY',80);
	define('PROBABLYNOT',20);
	define('NEITHER',50);
	// Argument for db_fetch 
	define('CAN_BE_EMPTY',TRUE);
	define('RETURN_ARRAY',TRUE);
	// Comments
	define('OFFICIAL',1);
	
/****************************************************************
>>>>>>>>>>>>>>>>>>>> DATABASE ABSTRACTION <<<<<<<<<<<<<<<<<<<<<<<
****************************************************************/

function db_query ($query, $line = NULL) {
	$result = mysql_query($query);
	if (!$result) {
		?>
		<db-error>
			<query>
				<? print "Failed at line $line: $query"; ?>
			</query>
			<error>
				<? print mysql_error(); ?>
			</error>
		</db-error>
		<?php
	} else {
		return $result;
	}
}

function db_fetch ($query, $canBeEmpty = FALSE, $forceArray = FALSE, $line = NULL) {
	$query_result = db_query($query, $line);
	if (!$query_result) { // no need to show errors otherwise as they should have been shown by db_query?
		return;
	}
	$result = mysql_fetch_row(db_query($query));
	if (!$result && !$canBeEmpty) {
		?>
		<db-error>
			<query>
				<? print "Failed: $query"; ?>
			</query>
			<error>
				mysql_query gave a return value, but mysql_fetch_row didn't. mysql_error if any: <? print mysql_error(); ?>
			</error>
		</db-error>
		<?php
	} else {
		if ($result[1] || $forceArray) {
			return $result;
		} else {
			return $result[0];
		}
	}
}

	
/****************************************************************
>>>>>>>>>>>>>>>>>>>>>  VIEWING DATABASE <<<<<<<<<<<<<<<<<<<<<<<<<
****************************************************************/

function print_proposition ($p, $num = FALSE, $CONCLUSION = FALSE, $fromNums = NULL) {
	global $devMode;
	
	if ($num) {
		if ($CONCLUSION) {
			print '<span class="therefore">&there4;</span>';
		} else {
			print '<span class="therefore">&emsp;&ensp;</span>';
		}
		print '<span class="number">'.$num.'</span>';
	}
	$_text = mysql_query("SELECT text FROM propositions WHERE p = $p");
	$text = mysql_fetch_row($_text);
	if ($text) {
		// Parse for glossary terms
		$matches = array();
		if (preg_match_all('/\[.*?\]/', $text[0], $matches)) {
			foreach ($matches[0] as $match) {
				$endofmatch = strlen($match) - 2;
				$match_inner = substr($match, 1, $endofmatch);
				//Need to split on | with explode(), etc.
			}
			
			//print $testtext;
		}
		$text[0] = preg_replace('/\[([^|]*)(?:|.*)\]/', '${1}', $text[0]);
		// TO CHANGE, MUCH LATER: If shown within an argument, don't wrap in abbr but show 'terms in this argument that may need explaining' below the *whole* argument - these terms would only appear once, so generate list to show while running through arg.
		// TO ADD: Eventually fetch def from database
		print '<span class="text">'.$text[0].'</span>';
	}
	if ($CONCLUSION) {
		sort($fromNums);
		$fromLast = array_pop($fromNums);
		if ($fromNums) {
			$fromText = implode(', ', $fromNums);
			$fromText .= " and $fromLast";
		} else {
			$fromText = $fromLast;
		}
		print '<span class="from">(from '.$fromText.')</span>';
	}
	if (!$_comments = db_fetch('SELECT official FROM prop_comments WHERE p = '.$p, CAN_BE_EMPTY, RETURN_ARRAY)) {
		$_comments = array();
	}
	if (!$__comments = db_fetch('SELECT official FROM prop_comments WHERE p = '.get_converse($p), CAN_BE_EMPTY, RETURN_ARRAY)) {
		$__comments = array();
	}
	$comments = array_merge($_comments, $__comments);
	if ($comments) {
		foreach ($comments as $comment) {
			if ($comment = OFFICIAL) {
				$HAS_OFFICIAL = TRUE;
			}
		}
		if ($HAS_OFFICIAL) {
			?>
			<a class="comments-official" href="/view/ajax/view.php?type=proposition&anchor=comments&id=<?php print $p; ?>">Comments icon</a>
			<?
		} else {
			?>
			<a class="comments-unofficial" href="/view/ajax/view.php?type=proposition&anchor=comments&id=<?php print $p; ?>">Comments icon</a>
			<?
		}
	}
	if ($devMode) { ?>
	<span class="id-info">
		<?php
		print $p;
		?>
	</span>
	<?php
	}
	print "</div>\n\n";
}

/****/
function print_buttons ($p, $b, $c) { // could add TEST as default for $a
	print '<div class="proposition">';
	print '<span class="buttons">';
	if ($b == ACCEPT) {
		print '<span class="tick-on button"><a href="/functions/php/set-attitude.php?p='.$p.'&amp;a=NULL" title="Accepted - click to undo"></a></span> ';
	} else if ($c == ACCEPT) {
		print '<span class="tick-c button"><a href="/functions/php/set-attitude.php?p='.$p.'&amp;a=1" title="Committed - click to accept"></a></span> ';
	} else {
		print '<span class="tick-off button"><a href="/functions/php/set-attitude.php?p='.$p.'&amp;a=1" title="Click to accept"></a></span> ';
	}
	if ($b == REJECT) {
		print '<span class="cross-on button"><a href="/functions/php/set-attitude.php?p='.$p.'&amp;a=NULL" title="Rejected - click to undo"></a></span> ';
	} else {
		print '<span class="cross-off button"><a href="/functions/php/set-attitude.php?p='.$p.'&amp;a=2" title="Click to reject"></a></span> ';
	}
	print '</span>';
}

/****/
function print_ajax_buttons ($p, $b, $c) { // could add TEST as default for $a
	$converse = $p + 1;
	print '<div class="proposition">';
	print '<span class="buttons">';
	if ($b == ACCEPT) {
		print '<span class="tick-on button"><a title="Take this back" class="'.$p.'" href="/functions/php/set-attitude.php?p='.$p.'&amp;a=NULL"></a></span> ';
	} else if ($c == ACCEPT) {
		print '<span class="tick-c button"><a title="Accept" class="'.$p.' committed" href="/functions/php/set-attitude.php?p='.$p.'&amp;a=1"></a></span> ';
	} else {
		print '<span class="tick-off button"><a title="Accept" class="'.$p.'" href="/functions/php/set-attitude.php?p='.$p.'&amp;a=1"></a></span> ';
	}
	if ($b == REJECT) {
		print '<span class="cross-on button"><a title="Take this back" class="'.$converse.'" href="/functions/php/set-attitude.php?p='.$p.'&amp;a=NULL"></a></span> ';
	} else {
		print '<span class="cross-off button"><a title="Reject" class="'.$converse.'" href="/functions/php/set-attitude.php?p='.$p.'&amp;a=2"></a></span> ';
	}
	print '</span>';
}

/*****/
function show_implication ($id) {
	global $u;
	global $devMode;
	
	if ($devMode) {
		print "<hr /><h3>Implication $id</h3>";
	}
	
	$maxid = mysql_fetch_row(mysql_query("SELECT MAX(id) FROM implications"));
	if (!($id > 0 && $id <= $maxid)) {
		print '<p>An invalid ID was sent</p>';
		return;
	}
	$argpnum = 1;
	$_ps = mysql_query("SELECT p1, p2, p3, p4, c FROM implications WHERE id = $id");
	if ($_ps) {
		$ps = mysql_fetch_row($_ps);
		$cid = array_pop($ps);
		$pnum = 1;
		foreach ($ps as $p) { // PROPOSITIONS, OTHER THAN CONCLUSION
			if ($p) {
				$PDONE = FALSE;
				for ($oldpnum = $argpnum - 1; $oldpnum >= 1; $oldpnum--) {
					if ($a[$oldpnum][p] == $p) {
						$fromtext .= $oldpnum.' ';
						$PDONE = TRUE;
					}
				}
				if (!$PDONE) {
					$fromtext .= $argpnum.' ';
					$a[$argpnum][p] = $p;
					$_pa = mysql_query("SELECT b, c FROM attitudes WHERE p = $p AND u = $u");
					$pa = mysql_fetch_row($_pa);
					if ($pa[0] != NULL && $pa[0] != "NULL") {
						$a[$argpnum][b] = $pa[0];
					} else {
						$a[$argpnum][b] = NULL;
					}
					if ($pa[1] != NULL && $pa[1] != "NULL") {
						$a[$argpnum][c] = $pa[1];
					} else {
						$a[$argpnum][c] = NULL;
					}
					// Print the proposition line 
					
					tons($a[$argpnum][p], $a[$argpnum][b], $a[$argpnum][c]);
					print_proposition($p, $argpnum);
					$argpnum++;
				}
			}
		}
		// THE CONCLUSION
		$a[$argpnum][p] = $cid;
		$_pa = mysql_query("SELECT b, c FROM attitudes WHERE p = $cid AND u=$u");
		$pa = mysql_fetch_row($_pa);
		if ($pa[0] != NULL && $pa[0] != "NULL") {
			$a[$argpnum][b] = $pa[0];
		} else {
			$a[$argpnum][b] = NULL;
		}
		if ($pa[1] != NULL && $pa[1] != "NULL") {
			$a[$argpnum][c] = $pa[1];
		} else {
			$a[$argpnum][c] = NULL;
		}
		// Print the conclusion line 
		print_buttons($a[$argpnum][p], $a[$argpnum][b], $a[$argpnum][c]);
		print_proposition($cid, $argpnum, TRUE, $fromtext);
		$argpnum++;
	}
}

/*****/

function show_argument ($id, $ajax = FALSE) {
	global $u;
	
	$maxid = mysql_fetch_row(mysql_query("SELECT MAX(id) FROM arguments"));
	if (!($id > 0 && $id <= $maxid)) {
		print '<p>An invalid argument ID was sent</p>';
		return;
	}
	$arg_data =  mysql_fetch_row(mysql_query("SELECT subargs, name, description,source,source_author FROM arguments WHERE id = $id"));
	?>
	<h1><?php print $arg_data[1]; ?></h1>
	
	<?php if ($arg_data[3] || $arg_data[4]) { ?>
		<div id="arg-source">
			<span class="inline-label">Source:</span>
			<?php
			print $arg_data[4];
			if ($arg_data[3] && $arg_data[4]) {
				print ', ';
			}
			print $arg_data[3];
			?>
		</div>
	<?php } ?>

	<?php if ($arg_data[2]) { ?>
		<div id="arg-info">
			<?php print $arg_data[2]; ?>
		</div>
	<?php } ?>

	<?php
	$is = explode(',', $arg_data[0]);
	$argpnum = 1;
	foreach ($is as $i) {
		$fromNums = array(); // Clear $fromNums for new sub-argument
		$_ps = mysql_query("SELECT p1, p2, p3, p4, c FROM implications WHERE id = $i");
		if ($_ps) {
			$ps = mysql_fetch_row($_ps);
			$cid = array_pop($ps);
			$pnum = 1;
			foreach ($ps as $p) { // PROPOSITIONS, OTHER THAN CONCLUSION
				if ($p) {
					$PDONE = FALSE;
					for ($oldpnum = $argpnum - 1; $oldpnum >= 1; $oldpnum--) {
						if ($a[$oldpnum][p] == $p) {
							$fromNums[] = $oldpnum;
							$PDONE = TRUE;
						}
					}
					if (!$PDONE) {
						$fromNums[] = $argpnum;
						$a[$argpnum][p] = $p;
						$_pa = mysql_query("SELECT b, c FROM attitudes WHERE p = $p AND u = $u");
						$pa = mysql_fetch_row($_pa);
						if ($pa[0] != NULL && $pa[0] != "NULL") {
							$a[$argpnum][b] = $pa[0];
						} else {
							$a[$argpnum][b] = NULL;
						}
						if ($pa[1] != NULL && $pa[1] != "NULL") {
							$a[$argpnum][c] = $pa[1];
						} else {
							$a[$argpnum][c] = NULL;
						}
						// Print the proposition line 
						if ($ajax) {
							print_ajax_buttons($a[$argpnum][p], $a[$argpnum][b], $a[$argpnum][c]);
						} else {
							print_buttons($a[$argpnum][p], $a[$argpnum][b], $a[$argpnum][c]);
						}
						print_proposition($p, $argpnum);
						$argpnum++;
					}
				}
			}
			// THE CONCLUSION
			$a[$argpnum][p] = $cid;
			$_pa = mysql_query("SELECT b, c FROM attitudes WHERE p = $cid AND u=$u");
			$pa = mysql_fetch_row($_pa);
			if ($pa[0] != NULL && $pa[0] != "NULL") {
				$a[$argpnum][b] = $pa[0];
			} else {
				$a[$argpnum][b] = NULL;
			}
			if ($pa[1] != NULL && $pa[1] != "NULL") {
				$a[$argpnum][c] = $pa[1];
			} else {
				$a[$argpnum][c] = NULL;
			}
			// Print the conclusion line 
			if ($ajax) {
				print_ajax_buttons($a[$argpnum][p], $a[$argpnum][b], $a[$argpnum][c]);
			} else {
				print_buttons($a[$argpnum][p], $a[$argpnum][b], $a[$argpnum][c]);
			}
			print_proposition($cid, $argpnum, TRUE, $fromNums);
			$argpnum++;
		}
	}
}

/****************************************************************
*****************************************************************
>>>>>>>>>>>>>>>>>>>>>  MAKING CHANGES <<<<<<<<<<<<<<<<<<<<<<<<<<<
*****************************************************************
****************************************************************/

function set_attitude_ajax ($p, $b, $c = NOTSET) {
	// Note for this function - I think I wrote NOTSET not to mean that the user's attitude should be NULL but that it shouldn't be changed but left as what it was before this function ran. E.g. we might be setting $b but not $c, or vice versa

	// Set attitude - would be nice to let them know, if it's just a commitment
	global $u;
	$converse = get_converse($p);
	if ($b == ACCEPT) { $oppositeb = REJECT; } 
	else if ($b == REJECT) { $oppositeb = ACCEPT; }
	else if ($b == NEUTRAL) { $oppositeb = NEUTRAL; }
	else if ($b == 'NULL') { $oppositeb = 'NULL'; }
	if ($c == ACCEPT) { $oppositec = REJECT; } 
	else if ($c == REJECT) { $oppositec = ACCEPT; }
	else if ($c == 'NULL') { $oppositec = 'NULL'; }
	
	// WRITE TO THE DATABASE
	
	$_oldattitude = mysql_query("SELECT b, c FROM attitudes WHERE p = $p AND u=$u");
	if ($ENTRYALREADY = mysql_num_rows($_oldattitude)) { // needs looking at to check what happens if we're setting neutrality
		$oldattitude = mysql_fetch_row($_oldattitude);
		// Check whether we're going back on a belief or commitment, and remember for later
		if (($oldattitude[BELIEF] == ACCEPT || $oldattitude[COMMITMENT] == ACCEPT) && (($b == REJECT || $b == 'NULL') || ($b == NOTSET && ($oldattitude[BELIEF] == REJECT || $oldattitude[BELIEF] == NULL))) && (($c == REJECT || $c == 'NULL') || ($c == NOTSET && ($oldattitude[COMMITMENT] == REJECT || $oldattitude[COMMITMENT] == NULL))) ) {
			$GOINGBACK = TRUE;
		}
		if ($b != NOTSET) {
			if (!$CHECK = mysql_query("UPDATE attitudes SET b = $b WHERE p = $p AND u=$u")) {
				?>
				<db-error>
					<query>
						<? print "Failed: UPDATE attitudes SET b = $b WHERE p = $p AND u=$u"; ?>
					</query>
					<error>
						<? print mysql_error(); ?>
					</error>
				</db-error>
				<?php
			}
			mysql_query("UPDATE attitudes SET b = $oppositeb WHERE p = $converse AND u=$u");
		}
		if ($c != NOTSET) {
			mysql_query("UPDATE attitudes SET c = $c WHERE p = $p AND u=$u");
			mysql_query("UPDATE attitudes SET c = $oppositec WHERE p = $converse AND u=$u");
		}
	} else {
		if ($b == NOTSET) { $newbentry = 'NULL'; $oppositeb = 'NULL'; } else { $newbentry = $b; }
		if ($c == NOTSET) { $newcentry = 'NULL'; $oppositec = 'NULL'; } else { $newcentry = $c; }
		mysql_query("INSERT INTO attitudes (u,p,b,c) VALUES ($u,$p,$newbentry,$newcentry)");
		mysql_query("INSERT INTO attitudes (u,p,b,c) VALUES ($u,$converse,$oppositeb,$oppositec)");
	}
	
	// RETURN NEW VALUES ?>
	
	<attitudeChange>
		<p><?php print $p; ?></p>
		<belief><?php print $b; ?></belief>
		<c><?php print $c; ?></c>
	</attitudeChange>
	<attitudeChange>
		<p><?php print $converse; ?></p>
		<belief><?php print $oppositeb; ?></belief>
		<c><?php print $oppositec; ?></c>
	</attitudeChange>
	
	<?php // SET ANY IMPLIED COMMITMENTS
	
	if ($b == ACCEPT || $c == ACCEPT) {
		$trueProp = $p;
	} else if ($b == REJECT || $c == REJECT) {
		$trueProp = $converse;
	}
	
	if ($trueProp) do {
		$_is = db_fetch("SELECT implications FROM propositions WHERE p = $trueProp", 414);
		if (!$_is[0]) break;
		$is = explode(',', $_is[0]);
		$anum = 1;
		// Go through each implication of this true proposition to check if it's now a commitment
		foreach ($is as $i) {
			// Load all the other premises in this implication into $as[X]
			$ps = db_fetch("SELECT p1, p2, p3, p4, c FROM implications WHERE id = $i", 421);
			$as[$anum]['c']['p'] = array_pop($ps);
			$pnum = 1;
			foreach ($ps as $premise) {
				if ($premise && $premise != $trueProp) {
					$as[$anum][$pnum]['p'] = $premise;
					$_pa = mysql_query("SELECT b, c FROM attitudes WHERE p = $premise AND u = $u");
					$pa = mysql_fetch_row($_pa);
					if ($pa[BELIEF] == ACCEPT || $pa[COMMITMENT] == ACCEPT) {
						$as[$anum][$pnum]['a'] = 1;
					} else {
						$as[$anum][$pnum]['a'] = 0;
					}
					$pnum++;
				}
			}
			// See if all the other premises, now in $as[X], are accepted or commited to
			$committed = TRUE;
			for ($i = $pnum - 1; $i > 0; $i--) {
				if ($as[$anum][$i][a] == FALSE) {
					$committed = FALSE;
					/***EXTRA FEATURE?:*** Maybe add message to pass on potential implication... 
					$cid = $as[$anum][c][p];
					$_text = mysql_query("SELECT text FROM propositions WHERE p = $cid");
					if ($_text) {
						$text = mysql_fetch_row($_text);
						print "<p>You would be committed to accepting: $text[0]</p>";
					} else {
						print "<p>You would be committed to accepting $cid</p>";
					}
					print "<p>If you accepted:</p>";
					$_text = mysql_query("SELECT text FROM propositions WHERE p =".$as[$anum][$i][p]);
					if ($_text) {
						$text = mysql_fetch_row($_text);
						print "<p>$text[0]</p>";
					} */
					// This hasn't covered case where (2?) extra acceptances would be nec - we shouldn't notify if it's too many?
					// Ideally add commitments though arguments - register these in an extra columns in either props or subarguments
				}
			}
			// If they are, commit user to the conclusion
			if ($committed) {
				$cid = $as[$anum][c][p];
				set_attitude_ajax($cid,NOTSET,ACCEPT);
			}
			$anum++;
		}
	} while (false);
	
	// UNSET ANY COMMITMENTS WE'VE UNDONE
	// If we're going back on a belief or commitment, check that we're still committed to everything it implied
	
	if ($GOINGBACK) {
		print '***********475'; //√
		$__is = mysql_query("SELECT implications FROM propositions WHERE p = $p");
		$_is = mysql_fetch_row($__is);
		$is = explode(',', $_is[0]);
		$anum = 1;
		foreach ($is as $i) {
			// Load all the other premises in this implication into $as[X]
			$_ps = mysql_query("SELECT p1, p2, p3, p4, c FROM implications WHERE id = $i");
			$ps = mysql_fetch_row($_ps);
			$as[$anum][c][p] = array_pop($ps);
			$cid = $as[$anum][c][p];
			$_committed = mysql_query("SELECT c FROM attitudes WHERE p = $cid AND u=$u");
			$committed = mysql_fetch_row($_committed);
			if ($committed[0] == ACCEPT) {
				$pnum = 1;
				foreach ($ps as $premise) {
					if ($premise && $premise != $p) {
						$as[$anum][$pnum]['p'] = $premise;
						$_pa = mysql_query("SELECT b, c FROM attitudes WHERE p = $premise AND u = $u");
						$pa = mysql_fetch_row($_pa);
						if ($pa[BELIEF] == ACCEPT || $pa[COMMITMENT] == ACCEPT) {
							$as[$anum][$pnum]['a'] = 1;
						} else {
							$as[$anum][$pnum]['a'] = 0;
						}
						$pnum++;
					}
				}
				// See if all the other premises, now in $as[X], are accepted or commited to
				$committed = TRUE;
				for ($i = $pnum - 1; $i > 0; $i--) {
					if ($as[$anum][$i][a] == FALSE) {
						$committed = FALSE;
						// No longer accepted arguments check fails:  $as[$anum][$i][p]." wasn't accepted / committed to anyway
					} else { 
						// No longer accepted arguments check not failed yet: $as[$anum][$i][p].' accepted / committed to
					}
				}
				// If they are, remember that we may need to uncommit user from the conclusion
				if ($committed) {
					$touncommit[$cid] = TRUE;
				}
			}
			$anum++;
		}
		// Now see if we really do need to uncommit user from these conclusions, because they're not committed to them by anything else
		// OPTIMISATION: Can't we skip the above way of generating the $touncommit array and fill it with *everything* in "SELECT implications FROM propositions WHERE p = $p" regardless of whether all other premises in that implication we're already accepted/committed to (what we're checking above)?
		if (isset($touncommit)) {
			print '*******523'; // √
			foreach ($touncommit as $cid => $value) {
				$__is = mysql_query("SELECT implicators FROM propositions WHERE p = $cid");
				$_is = mysql_fetch_row($__is);
				$is = explode(',', $_is[0]);
				$anum = 1;
				foreach ($is as $i) {
					// Load all the other premises in this implication into $as[X]
					$_ps = mysql_query("SELECT p1, p2, p3, p4 FROM implications WHERE id = $i");
					$ps = mysql_fetch_row($_ps);
					$pnum = 1;
					foreach ($ps as $premise) {
						if ($premise) {
							$as[$anum][$pnum]['p'] = $premise;
							$_pa = mysql_query("SELECT b, c FROM attitudes WHERE p = $premise AND u = $u");
							$pa = mysql_fetch_row($_pa);
							if ($pa[BELIEF] == ACCEPT || $pa[COMMITMENT] == ACCEPT) {
								$as[$anum][$pnum]['a'] = 1;
							} else {
								$as[$anum][$pnum]['a'] = 0;
							}
							$pnum++;
						}
					}
					// See if all the other premises, now in $as[X], are accepted or commited to
					if (!$FOUNDCOMMITTMENT) {
						$committed = TRUE;
						for ($i = $pnum - 1; $i > 0; $i--) {
							if ($as[$anum][$i][a] == FALSE) {
								$committed = FALSE;
							}
						}
						if ($committed == TRUE) {
							$FOUNDCOMMITTMENT = TRUE;
						}
					}
				}
				// If we haven't found another commitment for the current $cid, uncommit the user 
				if (!$FOUNDCOMMITTMENT) {
					set_attitude_ajax($cid,NOTSET,'NULL');
				}		
			}
		}
	}
}

function set_attitude ($p, $b, $c = NOTSET) {
		// Set attitude - would be nice to let them know, if it's just a commitment
		global $u;
		$converse = get_converse($p);
		if ($b == ACCEPT) { $oppositeb = REJECT; } 
		else if ($b == REJECT) { $oppositeb = ACCEPT; }
		else if ($b == 'NULL') { $oppositeb = 'NULL'; }
		if ($c == ACCEPT) { $oppositec = REJECT; } 
		else if ($c == REJECT) { $oppositec = ACCEPT; }
		else if ($c == 'NULL') { $oppositec = 'NULL'; }
		$_oldattitude = mysql_query("SELECT b, c FROM attitudes WHERE p = $p AND u=$u");
		$ENTRYALREADY = mysql_num_rows($_oldattitude);
		$oldattitude = mysql_fetch_row($_oldattitude);
		// CHANGE TO RETURN VALUE: print "<p style='font-weight:bold;'>b = $b; opposite = $oppositeb; c = $c; p = $p; converse = $converse;</p>";
		if ($ENTRYALREADY) { // needs looking at to check what happens if we're setting neutrality
			// Check whether we're going back on a belief or commitment, and remember for later
			if (($oldattitude[BELIEF] == ACCEPT || $oldattitude[COMMITMENT] == ACCEPT) && (($b == REJECT || $b == 'NULL') || ($b == NOTSET && ($oldattitude[BELIEF] == REJECT || $oldattitude[BELIEF] == NULL))) && (($c == REJECT || $c == 'NULL') || ($c == NOTSET && ($oldattitude[COMMITMENT] == REJECT || $oldattitude[COMMITMENT] == NULL))) ) {
				$GOINGBACK = TRUE;
			}
			if ($b != NOTSET) {
				mysql_query("UPDATE attitudes SET b = $b WHERE p = $p AND u=$u");
				// CHANGE TO RETURN VALUE: print "<p>UPDATE attitudes SET b = $b WHERE p = $p AND u=$u</p>";
				mysql_query("UPDATE attitudes SET b = $oppositeb WHERE p = $converse AND u=$u");
				// CHANGE TO RETURN VALUE: print "<p>UPDATE attitudes SET b = $oppositeb WHERE p = $converse AND u=$u</p>";
			}
			if ($c != NOTSET) {
				mysql_query("UPDATE attitudes SET c = $c WHERE p = $p AND u=$u");
				// CHANGE TO RETURN VALUE: print "<p>UPDATE attitudes SET c = $c WHERE p = $p AND u=$u</p>";
				mysql_query("UPDATE attitudes SET c = $oppositec WHERE p = $converse AND u=$u");
				// CHANGE TO RETURN VALUE: print "<p>UPDATE attitudes SET c = $oppositec WHERE p = $converse AND u=$u</p>";
			}
		} else {
			if ($b == NOTSET) { $newbentry = 'NULL'; $oppositeb = 'NULL'; } else { $newbentry = $b; }
			if ($c == NOTSET) { $newcentry = 'NULL'; $oppositec = 'NULL'; } else { $newcentry = $c; }
			mysql_query("INSERT INTO attitudes (u,p,b,c) VALUES ($u,$p,$newbentry,$newcentry)");
			// CHANGE TO RETURN VALUE: print "<p>INSERT INTO attitudes (u,p,b,c) VALUES ($u,$p,$newbentry,$newcentry)</p>";
			mysql_query("INSERT INTO attitudes (u,p,b,c) VALUES ($u,$converse,$oppositeb,$oppositec)");
			// CHANGE TO RETURN VALUE: print "<p>INSERT INTO attitudes (u,p,b,c) VALUES ($u,$converse,$oppositeb,$oppositec)</p>";
		}
		
		// Set any implied commitments
		if ($b == ACCEPT || $c == ACCEPT) {
			$trueProp = $p;
		} else if ($b == REJECT || $c == REJECT) {
			$trueProp = $converse;
		}
	if ($trueProp) {
		$__is = mysql_query("SELECT implications FROM propositions WHERE p = $trueProp");
		$_is = mysql_fetch_row($__is);
		$is = explode(',', $_is[0]);
		$anum = 1;
		foreach ($is as $i) {
			// Load all the other premises in this implication into $as[X]
			$_ps = mysql_query("SELECT p1, p2, p3, p4, c FROM implications WHERE id = $i");
			if ($_ps) {
				$ps = mysql_fetch_row($_ps);
				$as[$anum][c][p] = array_pop($ps);
				$pnum = 1;
				foreach ($ps as $premise) {
					if ($premise && $premise != $trueProp) {
						$as[$anum][$pnum]['p'] = $premise;
						$_pa = mysql_query("SELECT b, c FROM attitudes WHERE p = $premise AND u = $u");
						$pa = mysql_fetch_row($_pa);
						// CHANGE TO RETURN VALUE: print "<hr/><p>SELECT b, c FROM attitudes WHERE p = $premise AND u = $u :: RESULT = ";
						// CHANGE TO RETURN VALUE: print $pa[BELIEF];
						// CHANGE TO RETURN VALUE: print ', ';
						// CHANGE TO RETURN VALUE: print $pa[COMMITMENT].'</p>';
						if ($pa[BELIEF] == ACCEPT || $pa[COMMITMENT] == ACCEPT) {
							$as[$anum][$pnum]['a'] = 1;
							// CHANGE TO RETURN VALUE: print "<p>SET: $ as[$anum][$pnum][a] = 1</p>";
						} else {
							$as[$anum][$pnum]['a'] = 0;
							// CHANGE TO RETURN VALUE: print "<p>SET: $ as[$anum][$pnum][a] = 0</p>";
						}
						$pnum++;
					}
				}
				// CHANGE TO RETURN VALUE: print "<p>So, array $ a is...";
				// CHANGE TO RETURN VALUE: print_r($as);
				// See if all the other premises, now in $as[X], are accepted or commited to
				$committed = TRUE;
				for ($i = $pnum - 1; $i > 0; $i--) {
					if ($as[$anum][$i][a] == FALSE) {
						$committed = FALSE;
						// CHANGE TO RETURN VALUE: print "<p>FOUND: $ as[$anum][$i][a] == FALSE</p>";
						// CHANGE TO RETURN VALUE: print $as[$anum][$i][p].': uncommits<br/>';
						/* Good place to pass on potential implication... 
						if ($committed) {
							$cid = $as[$anum][c][p];
							$_text = mysql_query("SELECT text FROM propositions WHERE p = $cid");
							if ($_text) {
								$text = mysql_fetch_row($_text);
								print "<p>You would be committed to accepting: $text[0]</p>";
							} else {
								print "<p>You would be committed to accepting $cid</p>";
							}
							print "<p>If you accepted:</p>";
						} 
						$_text = mysql_query("SELECT text FROM propositions WHERE p =".$as[$anum][$i][p]);
						if ($_text) {
							$text = mysql_fetch_row($_text);
							print "<p>$text[0]</p>";
						} */
						// Has this covered case where 2 extra acceptances would be nec?
						// Ideally add commitments though arguments - register these in an extra columns in either props or subarguments
					} else { 
						// CHANGE TO RETURN VALUE: print $as[$anum][$i][p].': don\'t uncommit<br/>'; 
					}
				}
				// If they are, commit user to the conclusion
				if ($committed) {
					$cid = $as[$anum][c][p];
					set_attitude($cid,NOTSET,ACCEPT);
				}
			}
			$anum++;
		}
	}
	// If we're going back on a belief or commitment, check that we're still committed to everything it implied
	if ($GOINGBACK) {
		$__is = mysql_query("SELECT implications FROM propositions WHERE p = $p");
		$_is = mysql_fetch_row($__is);
		$is = explode(',', $_is[0]);
		$anum = 1;
		foreach ($is as $i) {
			// Load all the other premises in this implication into $as[X]
			$_ps = mysql_query("SELECT p1, p2, p3, p4, c FROM implications WHERE id = $i");
			if ($_ps) {
				$ps = mysql_fetch_row($_ps);
				$as[$anum][c][p] = array_pop($ps);
				$cid = $as[$anum][c][p];
				$_committed = mysql_query("SELECT c FROM attitudes WHERE p = $cid AND u=$u");
				// CHANGE TO RETURN VALUE: print "<hr/><h3>START</h3><p>SELECT c FROM attitudes WHERE p = $cid AND u=$u</p>";
				$committed = mysql_fetch_row($_committed);
				if ($committed[0] == ACCEPT) {
					$pnum = 1;
					foreach ($ps as $premise) {
						if ($premise && $premise != $p) {
							$as[$anum][$pnum]['p'] = $premise;
							$_pa = mysql_query("SELECT b, c FROM attitudes WHERE p = $premise AND u = $u");
							$pa = mysql_fetch_row($_pa);
							if ($pa[BELIEF] == ACCEPT || $pa[COMMITMENT] == ACCEPT) {
								$as[$anum][$pnum]['a'] = 1;
								// CHANGE TO RETURN VALUE: print "<p>gbSET: $ as[$anum][$pnum][a] = 1</p>";
							} else {
								$as[$anum][$pnum]['a'] = 0;
								// CHANGE TO RETURN VALUE: print "<p>gbSET: $ as[$anum][$pnum][a] = 0</p>";
							}
							$pnum++;
						}
					}
					// CHANGE TO RETURN VALUE: print "<p>So, array $ a is...";
					// CHANGE TO RETURN VALUE: print_r($as);
					// See if all the other premises, now in $as[X], are accepted or commited to
					$committed = TRUE;
					for ($i = $pnum - 1; $i > 0; $i--) {
						if ($as[$anum][$i][a] == FALSE) {
							$committed = FALSE;
							// CHANGE TO RETURN VALUE: print "<p>No longer accepted arguments check fails: ";
							// CHANGE TO RETURN VALUE: print $as[$anum][$i][p]." wasn't accepted / committed to anyway</p>";
						} else {
							// CHANGE TO RETURN VALUE: print "<p>No longer accepted arguments check not failed yet: ".$as[$anum][$i][p].' accepted / committed to</p>'; 
						}
					}
					// If they are, remember that we may need to uncommit user from the conclusion
					if ($committed) {
						$touncommit[$cid] = TRUE;
						// CHANGE TO RETURN VALUE: print "<p>SET: touncommit[$cid] = TRUE</p>";
					}
				}
			}
			$anum++;
		}
		// Now see if we really do need to uncommit user from these conclusions, because they're not committed to them by anything else
		if (isset($touncommit)) {
			// CHANGE TO RETURN VALUE: print "<h3>Other commitments check:</h3>";
			// CHANGE TO RETURN VALUE: print "<p>touncommit array = ";
			// CHANGE TO RETURN VALUE: print_r($touncommit);
			// CHANGE TO RETURN VALUE: print "</p>";
			foreach ($touncommit as $cid => $value) {
				$__is = mysql_query("SELECT implicators FROM propositions WHERE p = $cid");
				$_is = mysql_fetch_row($__is);
				$is = explode(',', $_is[0]);
				$anum = 1;
				foreach ($is as $i) {
					// Load all the other premises in this implication into $as[X]
					$_ps = mysql_query("SELECT p1, p2, p3, p4 FROM implications WHERE id = $i");
					if ($_ps) {
						$ps = mysql_fetch_row($_ps);
						$pnum = 1;
						foreach ($ps as $premise) {
							if ($premise) {
								$as[$anum][$pnum]['p'] = $premise;
								$_pa = mysql_query("SELECT b, c FROM attitudes WHERE p = $premise AND u = $u");
								$pa = mysql_fetch_row($_pa);
								if ($pa[BELIEF] == ACCEPT || $pa[COMMITMENT] == ACCEPT) {
									$as[$anum][$pnum]['a'] = 1;
									// CHANGE TO RETURN VALUE: print "<p>SET: $ as[$anum][$pnum][a] = 1</p>";
								} else {
									$as[$anum][$pnum]['a'] = 0;
									// CHANGE TO RETURN VALUE: print "<p>SET: $ as[$anum][$pnum][a] = 0</p>";
								}
								$pnum++;
							}
						}
						// See if all the other premises, now in $as[X], are accepted or commited to
						if (!$FOUNDCOMMITTMENT) {
							$committed = TRUE;
							for ($i = $pnum - 1; $i > 0; $i--) {
								if ($as[$anum][$i][a] == FALSE) {
									$committed = FALSE;
								}
							}
							if ($committed == TRUE) {
								$FOUNDCOMMITTMENT = TRUE;
							}
						}
					}
				}
				// If we haven't found another commitment for the current $cid, uncommit the user 
				if (!$FOUNDCOMMITTMENT) {
					set_attitude($cid,NOTSET,'NULL');
				}		
			}
		}
	}
}

/****************************************************************
***********************  Adding arguments ***********************
****************************************************************/

function create_implication ($c, $probabilisation, $p1, $p2 = 'NULL', $p3 = 'NULL', $p4 = 'NULL') {
	if (!$p2) { $p2 = 'NULL'; }
	if (!$p3) { $p3 = 'NULL'; }
	if (!$p4) { $p4 = 'NULL'; }
	// Add this implication and get its ID - rather than printing below, use example of grabbing email from output buffer at NI computer to pass it into return value, not printed by converses (or PHP, non-Ajax, version)
	if (!mysql_query("INSERT INTO implications (p1,p2,p3,p4,c,probabilistic) VALUES ($p1,$p2,$p3,$p4,$c,$probabilisation)")) { ?>
		<db-error>
			<query>
				<? print "Failed: INSERT INTO implications (p1,p2,p3,p4,c,probabilistic) VALUES ($p1,$p2,$p3,$p4,$c,$probabilisation)"; ?>
			</query>
			<error>
				<? print mysql_error(); ?>
			</error>
		</db-error>
	<? } else {
		$id = mysql_fetch_row(mysql_query("SELECT MAX(id) FROM implications"));
		$id = $id[0]; ?>
		<message>
			Added as implication <?php print $id; ?>
		</message>
	<? }
	// Add this implicator to conclusion
	$cimplicators = mysql_fetch_row(mysql_query("SELECT implicators FROM propositions WHERE p = $c"));
	if ($cimplicators[0]) {
		$cimplicators = "'$cimplicators[0],$id'";
	} else {
		$cimplicators = "'$id'";
	}
	mysql_query("UPDATE propositions SET implicators = $cimplicators WHERE p = $c");
	// Add this implication to propositions
	$p1implications = mysql_fetch_row(mysql_query("SELECT implications FROM propositions WHERE p = $p1"));
	if ($p1implications[0]) {
		$p1implications = "'$p1implications[0],$id'";
	} else {
		$p1implications = "'$id'";
	}
	mysql_query("UPDATE propositions SET implications = $p1implications WHERE p = $p1");
	if ($p2) {
		$p2implications = mysql_fetch_row(mysql_query("SELECT implications FROM propositions WHERE p = $p2"));
		if ($p2implications[0]) {
			$p2implications = "'$p2implications[0],$id'";
		} else {
			$p2implications = "'$id'";
		}
		mysql_query("UPDATE propositions SET implications = $p2implications WHERE p = $p2");
	}
	if ($p3) {
		$p3implications = mysql_fetch_row(mysql_query("SELECT implications FROM propositions WHERE p = $p3"));
		if ($p3implications[0]) {
			$p3implications = "'$p3implications[0],$id'";
		} else {
			$p3implications = "'$id'";
		}
		mysql_query("UPDATE propositions SET implications = $p3implications WHERE p = $p3");
	}
	if ($p4) {
		$p4implications = mysql_fetch_row(mysql_query("SELECT implications FROM propositions WHERE p = $p4"));
		if ($p4implications[0]) {
			$p4implications = "'$p4implications[0],$id'";
		} else {
			$p4implications = "'$id'";
		}
		mysql_query("UPDATE propositions SET implications = $4implications WHERE p = $p4");
	}
}

/****************************************************************
*************  Support functions for making changes *************
****************************************************************/


function create_converse_implication ($c, $probabilisation, $p1, $p2 = NULL, $p3 = NULL, $p4 = NULL) {
	if (!$p2) { // 1 premise version
		create_implication (get_converse($c), $probabilisation, get_converse($p1));
	} else if (!$p3) { // 2 premise version
		create_implication (get_converse($c), $probabilisation, get_converse($p1),$p2);
	} else if (!$p4) { // 3 premise version
		create_implication (get_converse($c), $probabilisation, get_converse($p1),$p2,$p3);
	} else { // 4 premise version
		create_implication (get_converse($c), $probabilisation, get_converse($p1),$p2,$p3,$p4);
	}
}

function get_converse ($p) {
	if ($p&1) {
		return $p + 1;
	} else {
		return $p - 1;
	}
}

?>