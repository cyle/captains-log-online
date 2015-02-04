var sync_interval = 1000 * 30; // how often to try syncing automatically
var last_synced_ts = 0; // store this from last server response

$(document).ready(function() {
	
	// save every... once in awhile? after every change?
	
	last_synced_ts = $('#last-sync').val() * 1;
	
	$('.manual-sync-btn').click(sync); // let manual sync happen
	
	setInterval(sync, sync_interval); // autosync
	
	$('#activities-text').change(sync);
	$('#notes-text').change(sync);
	
	//sync(); // start with a sync to fetch latest from server
	
});

function sync() {
	// fetch latest from server -- is it newer than what's here?
	// send latest from here to server
	$('.saving-indicator').show();
	console.log('syncing...? ' + new Date());
	
	var when = $('#when').val();
	var activities = $('#activities-text').val();
	var notes = $('#notes-text').val();
	
	var activities_hash = '';
	var notes_hash = '';
	
	if (activities != '') {
		activities_hash = CryptoJS.SHA256(activities).toString();
	}
	
	if (notes != '') {
		notes_hash = CryptoJS.SHA256(notes).toString();
	}
	
	console.log('our activities hash: ' + activities_hash);
	console.log('our notes hash: ' + notes_hash);
	console.log('our current last sync time: ' + last_synced_ts);
	
	$.ajax({
		url: 'sync.php',
		data: { "wat": "check", "when": when, "activities_hash": activities_hash, "notes_hash": notes_hash, "last_sync": last_synced_ts },
		dataType: 'json',
		type: 'POST',
		error: function(xhr, status, code) {
			console.error('there was an error syncing!');
			console.error(status + ' ' + code);
			$('.saving-indicator').hide();
		},
		success: function(data, status, xhr) {
			console.log('sync result:');
			console.log(data);
			if (data.state == 'new-from-server') {
				// take latest from server
				console.log('got new data from the server');
				console.log('updating activities with: ' + data.activities);
				console.log('updating notes with: ' + data.notes);
				console.log('new last sync time: ' + data.last_sync);
				$('#activities-text').val(data.activities);
				$('#notes-text').val(data.notes);
				last_synced_ts = data.last_sync * 1;
			} else if (data.state == 'do-want') {
				// ours must be newer, so send it along
				console.log('ours must be new, send it along, server wants it');
				send_ours_to_server();
			} else if (data.state == 'same') {
				// the stuff on here is the same as on the server, do nothing
				console.log('data is the same on client and server, do nothing');
				console.log('new last sync time: ' + data.last_sync);
				last_synced_ts = data.last_sync * 1;
			} else {
				console.log('unknown state from server: ' + data.state);
			}
			$('.saving-indicator').hide();
		}
	});
}

function send_ours_to_server() {
	var when = $('#when').val();
	var activities = $('#activities-text').val();
	var notes = $('#notes-text').val();
	$.ajax({
		url: 'sync.php',
		data: { "wat": "save", "when": when, "activities": activities, "notes": notes },
		dataType: 'json',
		type: 'POST',
		error: function(xhr, status, code) {
			console.error('there was an error saving to server!');
			console.error(status + ' ' + code);
		},
		success: function(data, status, xhr) {
			console.log('saved success!');
			console.log(data);
			latest_synced_ts = data.sync_time;
		}
	});
}