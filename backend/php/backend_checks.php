<?php

	$msgInfo = PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' belonging to User ID ' . $job['user_id'] . ' (' . $user['firstname'] . ' ' . $user['lastname'] . ' / ' . $user['email'] . ' / ' . $user['business_type'] . ')';
	
	/**
	 * User allowance checks.
	 */
	
	//check account time limit
	if (isset($allowance['accountTimeLimit']) && intval($allowance['accountTimeLimit']) > 0) {
		$until = strtotime('+' . $allowance['accountTimeLimit'], strtotime($user['created']));
		if ($now >= $until) {
			trigger_error($msgInfo . ' has an associated User Account with ID ' . $user['id'] . ' whose Trial period has Expired. Skipped.' . PHP_EOL . PHP_EOL);
			exit;
		}
	}
	
	//check account job TIME LIMIT
	$continue = false;
	if (isset($allowance['jobTimeLimit']) && intval($allowance['jobTimeLimit']) > 0) {
		$until = strtotime('+' . $allowance['jobTimeLimit'], strtotime($job['added']));
		if ($until >= $now) { $continue = true; }
	} else { $continue = true; }
	if ($continue === false) {
		trigger_error($msgInfo . ' time limit expired. Skipped.' . PHP_EOL . PHP_EOL);
		exit;
	}
	
	//if message limit has been reached, don't run job
	if (isset($allowance['jobMessageLimit']) && intval($allowance['jobMessageLimit']) > 0) {
		if ($currentMessageCount >= $allowance['jobMessageLimit']) {
			trigger_error($msgInfo . ' message limit reached. Skipped.' . PHP_EOL . PHP_EOL);
			exit;
		}
	}
	
	/**
	 * Job checks.
	 */
	
	//check DATE
	if ($job['start_date'] !== '0000-00-00' && $job['end_date'] !== '0000-00-00') {
		$continue = false;
		$start = strtotime($job['start_date']);
		$end = strtotime($job['end_date']);
		$today = strtotime(date('Y-m-d'));
		if ($today >= $start && $today <= $end) { $continue = true; }
		if ($continue === false) { 
			trigger_error($msgInfo . ' running Dates are not current. Skipped.' . PHP_EOL . PHP_EOL);
			exit;
		}
	}
	
	//check WEEKDAY
	if (strlen($job['weekdays']) > 0) {
		$continue = false;
		$weekdays = explode(',', $job['weekdays']);
		$today = getDayOfWeek();
		foreach ($weekdays as $day) { if ($day === $today) { $continue = true; break; } }
		if ($continue === false) { 
			trigger_error($msgInfo . ' running Weekdays has none that match Today (' . $today . '). Skipped.' . PHP_EOL . PHP_EOL);
			exit;
		}
	}
	
	//check TIMEs
	if ($job['start_time'] !== '00:00:00' || $job['end_time'] !== '00:00:00') {
		$continue = false;
		$reference_time = date('H:i');
		$absolute = strtotime($reference_time);
		if (
			strtotime($job['start_time'], $absolute) <= $absolute
			&&
			strtotime($job['end_time'], $absolute) >= $absolute
		) { $continue = true; }
		if ($continue === false) {
			trigger_error($msgInfo . ' running Times are not yet current. Skipped.' . PHP_EOL . PHP_EOL);
			exit;
		}
	}