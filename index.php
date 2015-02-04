<?php

$login_required = true;
require_once('logincheck.php');

if (isset($_GET['d']) && trim($_GET['d']) != '') {
	// use provided date
	$when_ts = strtotime(trim($_GET['d']));
	if ($when_ts == false) {
		die('You need to input a valid date, i.e. YYYY-MM-DD, please try again.');
	}
	$when_friendly = date('M jS, Y', $when_ts);
} else {
	// use today
	$when_ts = time();
	$when_friendly = 'Today';
}

$when_db = "'".date('Y-m-d', $when_ts)."'";
$rightnow = time();

$today = array(); // will hold today info from database
$get_today_from_db = $mysqli->query('SELECT * FROM rawlog WHERE thedate='.$when_db.' AND user_id='.$current_user['userid']);
if ($get_today_from_db->num_rows == 0) {
	// make a new day log for this user!
	$new_day_query = $mysqli->query('INSERT INTO rawlog (user_id, thedate, tsu) VALUES ('.$current_user['userid'].', '.$when_db.', '.$rightnow.')');
	$today['activities'] = '';
	$today['notes'] = '';
	$today['last_sync'] = $rightnow;
} else {
	// ok cool
	$today_row = $get_today_from_db->fetch_assoc();
	$today['activities'] = $today_row['activities'];
	$today['notes'] = $today_row['notes'];
	$today['last_sync'] = $today_row['tsu'] * 1;
}

?><!doctype html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Captain's Log, <?php echo $when_friendly; ?></title>
<link href="http://fonts.googleapis.com/css?family=Source+Code+Pro:400,700" rel="stylesheet" type="text/css" />
<link href="log.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="container">

<input type="hidden" id="when" value="<?php echo date('Y-m-d', $when_ts); ?>" />
<input type="hidden" id="last-sync" value="<?php echo $today['last_sync']; ?>" />

<h1>Captain's Log, Stardate <?php echo date('Ymd', $when_ts); ?></h1>

<p>Hello there <?php echo $current_user['username']; ?>. Enter what you're working on or have worked on. This will auto-sync every half-minute or so, or you can manually sync, if you'd like. Use it on multiple machines and it'll all sync up (hopefully). Got questions? Ask Cyle.</p>

<h2><?php echo $when_friendly; ?>'s Activities:</h2>

<div><textarea id="activities-text" placeholder="- worked on a ticket [20 mins] {27652}"><?php echo $today['activities']; ?></textarea></div>

<div><input type="button" class="manual-sync-btn" value="Sync" /> <span class="saving-indicator" style="display:none;">Saving right now...</span></div>

<p>Need help formatting your activities list? Read about the format for your activities list <a href="log-format.html">here</a>.</p>

<h2><?php echo $when_friendly; ?>'s Notes:</h2>

<div><textarea id="notes-text"><?php echo $today['notes']; ?></textarea></div>

<div><input type="button" class="manual-sync-btn" value="Sync" /> <span class="saving-indicator" style="display:none;">Saving right now...</span></div>

<h2>Older Entries</h2>

<p>Enter a date and go: <form action="./" method="get"><input name="d" placeholder="2014-01-01" type="date" /> <input type="submit" value="Engage &raquo;" /></form></p>

</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/sha256.js"></script>
<script src="log.js" type="text/javascript"></script>
</body>
</html>