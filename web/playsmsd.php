#!/usr/bin/php -q
<?php

/**
 * This file is part of playSMS.
 *
 * playSMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * playSMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with playSMS. If not, see <http://www.gnu.org/licenses/>.
 */

// Usage:
// playsmsd [<PLAYSMSD_CONF>] <COMMAND> <LOOP_FLAG> <CMD_PARAM>
set_time_limit(0);

error_reporting(4);

// functions


/**
 * Get pid for certain playsmsd process
 *
 * @param string $process
 *        process name
 * @return integer PID
 */
function playsmsd_pid_get($process) {
	global $PLAYSMSD_CONF;
	
	return trim(shell_exec("ps -eo pid,command | grep '" . $PLAYSMSD_CONF . "' | grep '" . $process . "' | grep -v grep | sed -e 's/^ *//' -e 's/ *$//' | cut -d' ' -f1 | tr '\n' ' '"));
}

/**
 * Get pids for all playsmsd main process
 *
 * @return array PIDs
 */
function playsmsd_pids() {
	$pids['schedule'] = playsmsd_pid_get('schedule');
	$pids['ratesmsd'] = playsmsd_pid_get('ratesmsd');
	$pids['dlrssmsd'] = playsmsd_pid_get('dlrssmsd');
	$pids['recvsmsd'] = playsmsd_pid_get('recvsmsd');
	$pids['sendsmsd'] = playsmsd_pid_get('sendsmsd');
	return $pids;
}

/**
 * Show pids
 */
function playsmsd_pids_show() {
	$pids = playsmsd_pids();
	echo "schedule at pid " . $pids['schedule'] . "\n";
	echo "ratesmsd at pid " . $pids['ratesmsd'] . "\n";
	echo "dlrssmsd at pid " . $pids['dlrssmsd'] . "\n";
	echo "recvsmsd at pid " . $pids['recvsmsd'] . "\n";
	echo "sendsmsd at pid " . $pids['sendsmsd'] . "\n";
}



/**
 * View log
 *
 * @param string $debug_file
 *        Save log to debug file
 */
function playsmsd_log($debug_file = '') {
	global $PLAYSMS_LOG_PATH;
	
	$log = $PLAYSMS_LOG_PATH . '/playsms.log';
	if (file_exists($log)) {
		
		$process = 'tail -n 0 -f ' . $log . ' 2>&1';
		if ($debug_file) {
			@shell_exec('touch ' . $debug_file);
			if (file_exists($debug_file)) {
				$process .= '| tee ' . $debug_file;
			}
		}
		
		$handle = popen($process, 'r');
		while (!feof($handle)) {
			$buffer = fgets($handle);
			echo $buffer;
			flush();
		}
		pclose($handle);
	}
}

echo "hello";
					// playsmsd();
					// rate_update();
					// dlrd();
					// getsmsstatus();
					// recvsmsd();
					// getsmsinbox();
					// init step
					// $core_config['sendsmsd_queue'] = number of simultaneous queues
					// $core_config['sendsmsd_chunk'] = number of chunk per queue
					$c_list = array();
					$list = dba_search(_DB_PREF_ . '_tblSMSOutgoing_queue', 'id, queue_code', array(
						'flag' => '0' 
					));
					// foreach ($list as $db_row) {
						// $c_datetime_scheduled = strtotime($db_row['datetime_scheduled']);
						// if ($c_datetime_scheduled <= strtotime(core_get_datetime())) {
							// $c_list[] = $db_row;
						// }
						// echo "blah";
					// }
					
					echo "bye";
					
					// $list = array();
					// $sendsmsd_queue_count = (int) $core_config['sendsmsd_queue'];
					// if ($sendsmsd_queue_count > 0) {
						// for ($i=0;$i<$sendsmsd_queue_count;$i++) {
							// if ($c_list[$i]) {
								// $list[] = $c_list[$i];
							// }
						// }
					// } else {
						// $list = $c_list;
					// }
					
					// foreach ($list as $db_row) {
						// // $db_row['queue_code'] = queue code
						// // $db_row['queue_count'] = number of entries in a queue
						// // $db_row['sms_count'] = number of SMS in an entry
						// $num = 0;
						// $db_query2 = "SELECT id FROM " . _DB_PREF_ . "_tblSMSOutgoing_queue_dst WHERE queue_id='" . $db_row['id'] . "'";
						// $db_result2 = dba_query($db_query2);
						// while ($db_row2 = dba_fetch_array($db_result2)) {
							// $num++;
							// if ($chunk = floor($num / $core_config['sendsmsd_chunk_size'])) {
								// $db_query3 = "UPDATE " . _DB_PREF_ . "_tblSMSOutgoing_queue_dst SET chunk='" . $chunk . "' WHERE id='" . $db_row2['id'] . "'";
								// $db_result3 = dba_query($db_query3);
							// }
						// }
						
						// if ($num > 0) {
							// // destination found, update queue to process step
							// sendsms_queue_update($db_row['queue_code'], array(
								// 'flag' => 3 
							// ));
						// } else {
							// // no destination found, something's not right with the queue, mark it as done (flag 1)
							// if (sendsms_queue_update($db_row['queue_code'], array(
								// 'flag' => 1 
							// ))) {
								// _log('enforce init finish queue:' . $db_row['queue_code'], 2, 'playsmsd sendsmsd');
							// } else {
								// _log('fail to enforce init finish queue:' . $db_row['queue_code'], 2, 'playsmsd sendsmsd');
							// }
						// }
					// }
					
					// // process step
					// $queue = array();
					
					// $list = dba_search(_DB_PREF_ . '_tblSMSOutgoing_queue', 'id, queue_code', array(
						// 'flag' => '3' 
					// ), '', $extras);
					// foreach ($list as $db_row) {
						// // get chunks
						// $c_chunk_found = 0;
						// $db_query2 = "SELECT chunk FROM " . _DB_PREF_ . "_tblSMSOutgoing_queue_dst WHERE queue_id='" . $db_row['id'] . "' AND flag='0' GROUP BY chunk LIMIT " . $core_config['sendsmsd_chunk'];
						// $db_result2 = dba_query($db_query2);
						// while ($db_row2 = dba_fetch_array($db_result2)) {
							// $c_chunk = (int) $db_row2['chunk'];
							// $queue[] = 'Q_' . $db_row['queue_code'] . '_' . $c_chunk;
							// $c_chunk_found++;
						// }
						
						// if ($c_chunk_found < 1) {
							// // no chunk found, something's not right with the queue, mark it as done (flag 1)
							// if (sendsms_queue_update($db_row['queue_code'], array(
								// 'flag' => 1 
							// ))) {
								// _log('enforce finish process queue:' . $db_row['queue_code'], 2, 'playsmsd sendsmsd');
							// } else {
								// _log('fail to enforce finish process queue:' . $db_row['queue_code'], 2, 'playsmsd sendsmsd');
							// }
						// }
					// }
					
					// // execute step
					// $queue = array_unique($queue);
					// if (count($queue) > 0) {
						// foreach ($queue as $q) {
							// $is_sending = (playsmsd_pid_get($q) ? TRUE : FALSE);
							// if (!$is_sending) {
								// $RUN_THIS = "nohup $PLAYSMSD_COMMAND sendqueue once $q >/dev/null 2>&1 &";
								// echo $COMMAND . " execute: " . $RUN_THIS . "\n";
								// shell_exec($RUN_THIS);
							// }
						// }
					// }
			
			// // END OF MAIN LOOP BLOCK
			

			// //echo $COMMAND . " end time:" . time() . "\n";
			
	
			// // empty buffer, yes doubled :)
			// ob_end_flush();
			// ob_end_flush();
		
		
		// // while TRUE
