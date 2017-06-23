<?php
	
	/**
	 * Production status. Also change the __oCIo__ constant in OPHEME/vendor/constants.php
	 */
	define('__oLIVE__', false);

	/**
	 * @var array MySQL Database Configuration details and Tables.
	 */
	$__oMyDB__ = array(
		'db.connection.user' => 'opheme',
		'db.connection.pass' => 'oPheme1357!',
		'db.connection.host' => 'localhost',
		'db.connection.db' => 'opheme2' . (__oLIVE__?'1':'_ci'),
		'db.tables' => array(
			'app.campaign.jobs' => 'campaigns',
			'app.discover.jobs' => 'discovers',
			'app.jobs.messages' => 'jobs_messages',
			'app.jobs.share' => 'jobs_share_zip',
			'app.jobs.tokens' => 'jobs_tokens',
			'app.subscription.limits' => 'subscription_limits',
			'app.urls' => 'short_urls',
			'company' => 'companies',
			'email.notification.history' => 'email_notification_history',
			'instagram.keys' => 'instagram_keys',
			'logs.forms.submits' => 'logs_form_submits',
			'logs.operations' => 'logs_operations',
			'socialMedia.interaction' => 'sm_interaction',
			'twitter.campaign.blacklist' => 'twitter_campaign_marketing_blacklist',
			'twitter.campaign.preferences' => 'twitter_campaign_marketing_preferences',
			'twitter.keys' => 'twitter_keys',
			'user.accounts' => 'users',
			'user.login.attempts' => 'login_attempts',
			'user.login.sessions' => 'session_data',
			'user.modules' => 'user_modules',
			'user.registration.tokens' => 'registration_tokens'
		)
	);
	
	/**
	 * @var array MongoDB Database Configuration details and Tables.
	 */
	$__oMoDB__ = array(
		'db.tables' => array(
			'message.twitter' => array(
				'db' => 'messages20' . (__oLIVE__?'':'_ci') ,
				'coll' => 'tweets'
			),
			'message.twitter.sent' => array(
				'db' => 'messages20' . (__oLIVE__?'':'_ci') ,
				'coll' => 'tweets_sent'
			),
			'message.instagram' => array(
				'db' => 'messages20' . (__oLIVE__?'':'_ci') ,
				'coll' => 'instamedia'
			),
			'message.instagram.sent' => array(
				'db' => 'messages20' . (__oLIVE__?'':'_ci') ,
				'coll' => 'instamedia_sent'
			)
		)
	);
	
	/**
	 * Backend run frequency. Must match the OPHEME/backend/etc/settings.conf value. Seconds. +5 latency seconds.
	 */
	define('__oBACKEND_RUN_FREQUENCY__', '30 seconds');
	
	/**
	 * Controls day of the week to send out weekly email.
	 */
	define('__oEMAIL_NOTIFICATION_WEEKLY_DAY__', 'Monday');
	
	/**
	 * Controls the age of Messages that are kept when analysed by the backend modules.
	 */
	define('__oBACKEND_MESSAGE_AGE_NEW__', '5 minutes');
	
	/**
	 * Controls the minimum Klout score for Campaign module response.
	 */
	define('__oBACKEND_CAMPAIGN_REPLY_KLOUT_LOWER_LIMIT__', 30);
	
	/**
	 * Controls the frequency of Social Media token validity checks.
	 */
	define('__oBACKEND_SM_TOKEN_VALIDITY_CHECK__', '4 hours');
	
	/**
	 * Controls the frequency of Social Media interaction checks.
	 */
	define('__oBACKEND_SM_INTERACTION_CHECK__', '15 minutes');
	
	/**
	 * Controls the maximum age of the messages to check
	 */
	define('__oBACKEND_SM_AVG_TIME_CHECK__', '7 days');
	
	/**
	 * Controls the maximum age of the messages to check
	 */
	define('__oBACKEND_SM_AVG_TIME_MSG_AGE__', '30 days');
	
	/**
	 * Controls frontend operations debugging output.
	 */
	define('__oDEBUG_OPS__', false);
	
	/**
	 * Controls frontend PHP globals debugging output.
	 */
	define('__oDEBUG_GLOBALS__', false);
	
	/**
	 * Controls backend debugging output.
	 */
	define('__oDEBUG_BACKEND__', false);
	
	/**
	 * Controls buffering output.
	 */
	define('__oBUFFER__', true);
	
	/**
	 * Controls billing link display on Dashboard.
	 */
	define('__oBILLING_ENABLED__', true);
	
	/**
	 * Regular Session name.
	 */
	if (__oLIVE__) { define('__oSESSION_NAME__', '__oSecret_Sessions__'); }
	else { define('__oSESSION_NAME__', '__oSecret_ci_Sessions__'); }
	
	/**
	 * Impersonate Session name.
	 */
	define('__oIMPERSONATE_SESSION_NAME__', '__oSecret_Sessions_Impersonate__');
	
	/**
	 * Session User context parameter.
	 */
	define('__oSESSION_USER_PARAM__', 'user');
	
	/**
	 * Vendor default module location
	 */
	define('__oMOD_DEFAULT__', 'dashboard');
	
	/**
	 * System Twitter application Consumer Key.
	 */
	define('__oTWITTER_CONSUMER_KEY__', 'ixsSi0R6alETD4hsZjq6YkZA2');
	/**
	 * System Twitter application Consumer Secret.
	 */
	define('__oTWITTER_CONSUMER_SECRET__', 'wwCR1Sf1zzsKC0oAbL3J0uT6MAaRScB9vQT6gaMZtSBl8E6x5s');
	
	if (__oLIVE__ === true) {
		
		/**
		* System Instagram application Client Key.
		*/
	   define('__oINSTAGRAM_CONSUMER_KEY__', '064f3934e99f4497a2b281779158d42f');
	   /**
		* System Instagram application Client Secret.
		*/
	   define('__oINSTAGRAM_CONSUMER_SECRET__', 'b13aeae61823486186f9a0018d2da118');
		
	} else {
	
		/**
		 * System Instagram application Client Key.
		 */
		define('__oINSTAGRAM_CONSUMER_KEY__', 'dfdfeda2aca3486b99996507bc3a7857');
		/**
		 * System Instagram application Client Secret.
		 */
		define('__oINSTAGRAM_CONSUMER_SECRET__', 'd7373f57d42b42ffba7d2edd8bd3b834');
	
	}
	
	/**
	 * System Klout application Key
	 */
	define('__oKLOUT_KEY__', 'ruqph6qvqzxfkure3zpnuavg');
	/**
	 * System Klout application Shared Secret
	 */
	define('__oKLOUT_SHARED_SECRET__', 'dCF5bKdm99');
	
	/**
	 * Google Maps v3 API Key
	 */
	define('__oGMAPS_API_KEY__', 'AIzaSyDRmiZvB4SUycET2FUbLP0CRRLTx3agaPQ');
	
	/**
	 * Mapquest API key
	 */
	define('__oMAPQUEST_API_KEY__', 'Fmjtd%7Cluur256tl9%2Crl%3Do5-9w72qy');
	
	/**
	 * Geonames API username
	 */
	define('__oGEONAMES_USERNAME__', 'maskaro');
	
	/**
	 * User inactivity automatic timeout.
	 */
	define('__oUSER_INACTIVITY_TIMEOUT__', 1800);
	
	/**
	 * Free to access modules
	 */
	$freeModules = array('terms', 'logout', 'url', 'register', 'confirm', 'cookie-policy');
	
	/**
	 * Modules restricted to system
	 */
	$restrictedModules = array('template', 'template_settings', 'debug', 'camp-disc', 'reseller-admin');
	
	/**
	 * Free to access modules within a valid user context
	 */
	$freeUserModules = array('account', 'authorisation', 'callback');
	
	/**
	 * Free to access modules within company context
	 */
	$freeCompanyModules = array('ajax', 'dashboard');
	
	/**
	 * Available Company modules
	 */
	$availableModules = array('discover', 'campaign', 'interaction', 'admin', 'reseller', 'email', 'sentiment-analysis', 'klout', 'csv');
	
	/**
	 * Available Social Media modules
	 */
	$availableSMModules = array('twitter', 'instagram');
	
	/**
	 * Available Jobs modules
	 */
	$availableJModules = array('discover', 'campaign');
	
	/**
	 * Available Email Notification frequency. Days => Display
	 */
	$availableEmailNotificationFrequency = array('0' => 'Never', '1' => '24 Hours', '2' => '48 Hours', '7' => 'Week');
	
	/**
	 * @var array [ (string) module => [ (string) action, (boolean) extra_data_required ] ] pairs.
	 */
	$checkAjaxRequest = array(
		'job' => array(
			'getNewMessages' => true,
			'setStatus' => true,
			'share' => true,
			'unShare' => true
		),
		'twitter' => array(
			'follow' => true,
			'sendReply' => true
		),
		'socialMedia' => array(
			'follow' => true,
			'sendReply' => true,
			'getInteraction' => true
		)
	);