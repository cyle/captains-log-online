<?php

/*

	LOGIN CHECKING
	
	authenticate a user
		fill out the $current_user array with their info

*/

require_once('dbconn_mysql.php');

// set defaults for your user cookie
$current_user = array(
	'loggedin' => false,
	'username' => 'nobody',
	'userid' => 0,
	'is_admin' => false
);

if (isset($login_required) && $login_required == true) {
	
	// do whatever it takes to authorize the user
	// fill in their info into the "users" table in the database
	
}