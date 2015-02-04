<?php

$login_required = true;
require_once('logincheck.php');

?><!doctype html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Captain's Log, Your Entries</title>
<link href="http://fonts.googleapis.com/css?family=Source+Code+Pro:400,700" rel="stylesheet" type="text/css" />
<link href="log.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container">

<h1>Captain's Log, Your Entries</h1>

<p><a href="./">&laquo; back to today's log</a></p>

<p>Enter a date and go: <form action="./" method="get"><input name="d" placeholder="2014-01-01" type="date" /> <input type="submit" value="Engage &raquo;" /></form></p>
	
<p>Or check out the big list:</p>

<?php
$get_all_dates = $mysqli->query('SELECT thedate FROM rawlog WHERE user_id='.$current_user['userid'].' ORDER BY thedate DESC');
if ($get_all_dates->num_rows > 0) {
	?>
	<ul>
	<?php
	while ($entry = $get_all_dates->fetch_assoc()) {
		echo '<li><a href="./?d='.$entry['thedate'].'">'.date('M jS, Y', strtotime($entry['thedate'])).'</a></li>'."\n";
	}
	?>
	</ul>
	<?php
} else {
	?>
	<p>It looks like you have no saved entries! Oh dear.</p>
	<?php
}
?>

</div>
</body>
</html>