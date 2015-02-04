<?php

/*

	have there been any recent updates that are newer than the info i have?
	
	if the hashes coming in are empty and the last_sync is 0, then send back the latest info + sync time
	if there is no row for this day yet, make one!
	
	compare incoming hashes with the "today" row in the database
		if they are not the same, compare the last sync time from the client
		if the database's info is newer, then send back the new stuff
		otherwise, accept the new stuff from the client

	hash('sha256', $message); -- outputs hex string
	
*/

$login_required = true;
require_once('logincheck.php');

header('Content-type: application/json');

$client_request = trim($_REQUEST['wat']);
$rightnow = time();

if (isset($_REQUEST['when']) && trim($_REQUEST['when']) != '') {
	$theday_ts = strtotime(trim($_REQUEST['when']));
	if ($theday_ts == false) {
		die( json_encode( array('error' => 'Invalid date specified!') ) );
	}
	$theday_db = "'".date('Y-m-d', $theday_ts)."'";
} else {
	$theday_db = "'".date('Y-m-d')."'"; // today!
}

$return_info = array(); // will return this

$today = array(); // will hold today info from database
$get_today_from_db = $mysqli->query('SELECT * FROM rawlog WHERE thedate='.$theday_db.' AND user_id='.$current_user['userid']);
if ($get_today_from_db->num_rows == 0) {
	// make a new day log for this user!
	$new_day_query = $mysqli->query('INSERT INTO rawlog (user_id, thedate, tsu) VALUES ('.$current_user['userid'].', '.$theday_db.', '.$rightnow.')');
	$today['activities'] = '';
	$today['notes'] = '';
	$today['activities_hash'] = '';
	$today['notes_hash'] = '';
	$today['last_sync'] = $rightnow;
} else {
	// ok cool
	$today_row = $get_today_from_db->fetch_assoc();
	$today['activities'] = $today_row['activities'];
	$today['notes'] = $today_row['notes'];
	$today['activities_hash'] = $today_row['activities_chksum'];
	$today['notes_hash'] = $today_row['notes_chksum'];
	$today['last_sync'] = $today_row['tsu'] * 1;
}

if ($client_request == 'check') {
	if (!isset($_REQUEST['activities_hash'])) {
		die( json_encode( array('error' => 'No activities hash sent to the server.') ) );
	}
	
	if (!isset($_REQUEST['notes_hash'])) {
		die( json_encode( array('error' => 'No notes hash sent to the server.') ) );
	}
	
	if (!isset($_REQUEST['last_sync'])) {
		die( json_encode( array('error' => 'No last sync time sent to the server.') ) );
	}
	
	$client_activities_hash = trim($_REQUEST['activities_hash']);
	if ($client_activities_hash == '') { $client_activities_hash = hash('sha256', ''); }
	$client_notes_hash = trim($_REQUEST['notes_hash']);
	if ($client_notes_hash == '') { $client_notes_hash = hash('sha256', ''); }
	$client_last_sync = (int) trim($_REQUEST['last_sync']) * 1;
	
	if ($client_activities_hash == '' && $client_notes_hash == '' && $client_last_sync == 0) {
		// client is connecting for the first time
		// send them the latest from the database for today
		$return_info = $today;
		$return_info['state'] = 'new-from-server';
	} else {
		// client has sent along hashes of what it has, compare it to what's in the database
		// possible state to send back:
		//   'new-from-server' means we have new data to share
		//   'do-want' means the client has new data we want
		if ($today['activities_hash'] == $client_activities_hash && $today['notes_hash'] == $client_notes_hash) {
			// same stuff, don't do anything
			$return_info['state'] = 'same';
			$return_info['last_sync'] = $today['last_sync'];
		} else {
			// the hashes are different
			if ($client_last_sync < $today['last_sync']) {
				// client data is old, send new data back
				$return_info['state'] = 'new-from-server';
				$return_info['activities'] = $today['activities'];
				$return_info['notes'] = $today['notes'];
				$return_info['last_sync'] = $today['last_sync'];
			} else {
				// client data must be newer... request the new info
				$return_info['state'] = 'do-want';
			}
		}
	}
} else if ($client_request == 'save') {
	// override current data in database with this, send client back the timestamp
	if (!isset($_REQUEST['activities'])) {
		die( json_encode( array('error' => 'No activities sent to the server.') ) );
	}
	
	if (!isset($_REQUEST['notes'])) {
		die( json_encode( array('error' => 'No notes sent to the server.') ) );
	}
	$new_activities = trim($_REQUEST['activities']);
	$new_notes = trim($_REQUEST['notes']);
	$new_activities_hash = hash('sha256', $new_activities);
	$new_notes_hash = hash('sha256', $new_notes);
	$new_activities_db = $mysqli->escape_string($new_activities);
	$new_notes_db = $mysqli->escape_string($new_notes);
	$update_query = $mysqli->query("UPDATE rawlog SET activities='$new_activities_db', notes='$new_notes_db', activities_chksum='$new_activities_hash', notes_chksum='$new_notes_hash', tsu=$rightnow WHERE thedate=$theday_db AND user_id=".$current_user['userid']);
	if (!$update_query) {
		$return_info['error'] = 'There was an error saving your new data!';
	} else {
		$return_info['sync_time'] = time();
	}
} else {
	$return_info['error'] = 'Not sure what to do, no valid request given to server.';
}


echo json_encode($return_info);
