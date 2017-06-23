<?php

	//record template execution start time
	$startTimeMilis = filter_input(INPUT_SERVER, 'REQUEST_TIME_FLOAT');
	
	/**
	 * Base oPheme directory.
	 */
	define('__oDIR__', dirname(__DIR__));
	
	//get global functionality
	require_once(__oDIR__ . '/vendor/includes.inc.php');
	
	//disable output and start collecting it in buffer
	if (__oBUFFER__) { ob_start('ob_gzhandler'); }
	
	//get all POST parameters
	$_post = filter_input_array(INPUT_POST);
	//get all GET parameters
	$_get = filter_input_array(INPUT_GET);
	//get all SERVER parameters
	$_server = filter_input_array(INPUT_SERVER);
	
	/**
	 * @var oPheme oPheme handle.
	 */
	$site = new oPheme();
	
	//start session
	$site->session->startSession(__oSESSION_NAME__, true, __oSSL_ENABLED__);
	
	/**
	 * Requested module
	 */
	$module = $site->url->fetch('module'); if (!$module || !$site->isModule($module)) { $module = __oMOD_DEFAULT__; }
	
	//if module is not ajax, set custom error handler for framework usages - any errors happen prior to this need to be sorted using PHP's default error handler
	if ($module !== 'ajax') { set_error_handler('myErrorHandler'); }
	
	//login status
	if ($module !== 'logout') { $loggedIn = $site->login->isValid(); $fromLogout = false; }
	else { $loggedIn = false; $fromLogout = true; }
	
	if ($loggedIn === true) {
		
		//activity timestamp
		$_SESSION[__oSESSION_USER_PARAM__]['timestamp'] = time();
		
		if (!isset($_SESSION[__oSESSION_USER_PARAM__]['visited_interaction'])) {
			$_SESSION[__oSESSION_USER_PARAM__]['visited_interaction'] = 0;
		}
		
		if ($module === 'interaction') {
			//$_SESSION[__oSESSION_USER_PARAM__]['visited_interaction']++;
			//if ($_SESSION[__oSESSION_USER_PARAM__]['visited_interaction'] > 1) {
				$_SESSION[__oSESSION_USER_PARAM__]['previous_interaction_check'] = $_SESSION[__oSESSION_USER_PARAM__]['timestamp'];
			//}
		//} else {
			//$_SESSION[__oSESSION_USER_PARAM__]['visited_interaction'] = 0;
		}
		
		//get authorisation tokens
		foreach ($availableSMModules as $smModule) {
			$_SESSION[__oSESSION_USER_PARAM__]['authorisation'][$smModule] = $site->{$smModule}->getUserTokens('all', $_SESSION[__oSESSION_USER_PARAM__]['account']['id'], true);
		}
		
		//get new account info
		$_SESSION[__oSESSION_USER_PARAM__]['account'] = array_merge($_SESSION[__oSESSION_USER_PARAM__]['account'], $site->user->getUsersBy('id', $_SESSION[__oSESSION_USER_PARAM__]['account']['id'])[0]);
		
		//company representative status
		$_SESSION[__oSESSION_USER_PARAM__]['representative'] = $site->user->isRepresentative(__oCompanyID__, $_SESSION[__oSESSION_USER_PARAM__]['account']['id']);
		
		//refresh user info
		$site->user->refreshUserInfo();
		
		//save new allowance to session
		$_SESSION[__oSESSION_USER_PARAM__]['allowance'] = $site->user->get('allowance');
	
	} elseif ($loggedIn === 1) {
		
		$site->message->set('global', 'INFO', 'login_expired', 'You have been automatically logged out due to your current Session being inactive for more than 30 minutes.');
		$module = 'logout';
		//header('Location: /logout'); exit;
		
	} elseif (!$fromLogout) {
		
		//$site->message->set('global', 'INFO', 'login_invalid', 'Please Login before accessing ' . __oCompanyBrand__ . '\'s features.');
		//$module = 'login';
		
	}
	
	//company access to module
	$companyAccess = $site->company->hasAccessTo($module);
	
	//user access to module
	$userAccess = $site->user->hasAccessTo($module);
	
	/**
	 * Module load request logic
	 */
	if ($module === 'login') { //if LOGIN
		if ($loggedIn === true) { //and user is logged in
			$redirectNow = __oMOD_DEFAULT__; //redirect to default module
		}
	} else { //otherwise
		if (!in_array($module, $freeModules)) { //if not a free module
			if (!in_array($module, $restrictedModules)) { //and not a restricted module
				if ($loggedIn === true) { //and user is logged in
					if (!in_array($module, $freeUserModules) && !in_array($module, $freeCompanyModules)) { //and is not a free user module
						if (!$userAccess) { //and the user doesn't have access
							if (!$site->user->get('account', 'allSet') || !$site->user->hasAuthorised()) { // because details are not set
								$msg = ' Please deal with the displayed Notice messages.';
								$redirectNow = 'account';
							} else { // or because subscription isn't allowing it
								$msg = ' Please consider upgrading your Subscription.';
								$redirectNow = __oMOD_DEFAULT__;
							}
						} elseif (!$companyAccess) { //or the company doesn't have access
							$msg = ' Please contact ' . __oCompanySupport__ . ' for assistance.';
							$redirectNow = __oMOD_DEFAULT__;
						}
					}
				} else { //otherwise redirect to login module
					$msg = ' Please first Login before accessing ' . __oCompanyBrand__ . '. If you do not have an account, please use the form below to Register for one.';
					$redirectNow = 'login';
				}
			} else { $redirectNow = __oMOD_DEFAULT__; } //otherwise redirect to default module
		}
	}
	if (isset($redirectNow)) { //if redirect is required, do it now
		if (isset($msg)) { $site->message->set('global', 'WAR', 'access_denied', 'You do not have access to the ' . ucfirst($module) . ' area.' . $msg); }
		header('Location: /' . $redirectNow); exit;
	}
	
	//get task and extra parameters
	$task = $site->url->fetch('task');
	$extra = $site->url->fetch('extra');
	
	//data to display within templates
	$data = array();
	
	//load the required module
	include $site->getModuleToLoad($module);
	
	//handle critical notices
	$notices = array();
	
	//get all app errors, if any
	$notices['critical'] = $site->message->getAll(null, 'CRITICAL');
	
	//do redirect is set and there are no errors
	if ($site->url->issetRedirect() && empty($notices['critical'])) { header('Location: ' . $site->url->redirectTo()); exit; }
	
	//load the template module if it's not an ajax request
	include $site->getModuleToLoad('template');
	
	//send output to browser
	if (__oBUFFER__) { echo ob_get_clean(); }
	
	exit;