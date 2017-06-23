<?php

	//http://api.twitter.com/1.1/search/tweets.json?q=%20&count=100&geocode=lat,lng,rad&since_id=last_id
	//limits: 	180 requests / 15min / user
	//			450 requests / 15min / app
	
	include('twitter_checkTokenValidity.php');

	//if everything is OK, carry on looking
	if ($code === 200) {

		//search for messages
		$params = array(
			'q' => 				(strlen($job['filter'])>0?rawurlencode($job['filter']):rawurlencode(' ')),
			'count' => 			'100',
			'geocode' =>		$job['centre_lat'] . ',' . $job['centre_lng'] . ',' . $job['radius'] . 'mi',
			'since_id' =>		$job['since_id'],
			'result_type' => 	'recent'
		);

		//get content - multi-layered array
		$content_search = objectToArray($site->twitter->oauth->get('search/tweets', $params));

		if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . date('Y-m-d H:i:s') . ' - ' . $site->twitter->oauth->http_code . ': ' . $site->twitter->oauth->url; if (isset($content_search['errors'])) { foreach($content_search['errors'] as $error) { $message .= ' / ' . print_r($error, true); } } trigger_error(PHP_EOL . PHP_EOL .  $message . PHP_EOL . PHP_EOL); }

		//still OK, getting messages
		if ($site->twitter->oauth->http_code === 200) {
			
			//klout api v2 object
			$klout = new KloutAPIv2(__oKLOUT_KEY__);

			// filter example: ("my, first" "second, thing" third fourth) => [ my, first | second, thing | third | fourth ]
			//exclusion filter
			$filter_ex = str_getcsv($job['filter_ex'], ' '); //explode(' ', $job['filter_ex']);
			//inclusion filter
			$filter = str_getcsv($job['filter'], ' '); //explode(' ', $job['filter']);
			
			//keep track of processed messages this session
			$countValid = 0; $countSkippedFilter = 0; $countSkippedFilterEx = 0; $countInvalidCoordinates = 0; $countTooOld = 0; $countSkippedBlacklisted = 0; $countSkippedPreferences = 0; $countSkippedMessagePerTimeLimitReached = 0; $countServiceError = 0; $countSkippedKloutScoreTooLow = 0; $countDBError = 0;
			
			/*** Parse and Store results ***/
			foreach ($content_search['statuses'] as $messageBody) {
				
				//if job message limit has been reached, skip message
				if (isset($allowance['jobMessageLimit']) && intval($allowance['jobMessageLimit']) > 0) {
					if ($currentMessageCount >= $allowance['jobMessageLimit']) {
						if (__oDEBUG_BACKEND__) { trigger_error(PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' message limit reached. Stopped processing messages.' . PHP_EOL . PHP_EOL); }
						break;
					}
				}

				//check message timestamp - if older than job requirement, skip it
				if ($module === 'discover') {
					if (intval($job['messageLifeSpanLimit']) > 0) {
						$until = strtotime('+' . $job['messageLifeSpanLimit'], strtotime($messageBody['created_at']));
					} else { $until = $now; }
				} else {
					$until = strtotime('+' . __oBACKEND_MESSAGE_AGE_NEW__, strtotime($messageBody['created_at']));
				}
				if ($now > $until) { $countTooOld++; continue; }

				//if message has coordinates attached, it gets processed
				if (isset($messageBody['geo']['coordinates']) || isset($messageBody['coordinates']['coordinates'])) {

					//exclusion filter
					$stopEx = false; if (strlen($filter_ex[0]) > 0) { foreach ($filter_ex as $keyword) { if (stripos($messageBody['text'], trim($keyword)) !== false) { $stopEx = true; break; } } } if ($stopEx == true) { $countSkippedFilterEx++; unset($stopEx, $keyword); continue; }

					//inclusion filter
					$stop = false; if (strlen($filter[0]) > 0) { $stop = true; foreach ($filter as $keyword) { if (stripos($messageBody['text'], trim($keyword)) !== false) { $stop = false; break; } } } if ($stop == true) { $countSkippedFilter++; unset($stop, $keyword); continue; }
					
					//save message creation time as mongo date for later analysis
					$messageBody['created_mongo'] = strtotime($messageBody['created_at']);
					
					//if message isn't already in the DB, save it for future reference
					if (!$site->job->messageExists($tokenType, $messageBody['id_str'])) {
						
						$messageBody['_o_text'] = $messageBody['text'];
						
						//store token type for message
						$messageBody['smType'] = $tokenType;
						//store token id for message
						$messageBody['smId'] = $tokenId;
						
						//save message coordinates
						$coords = (
							isset($messageBody['geo']['coordinates'])?
								$messageBody['geo']['coordinates']:
								(isset($messageBody['coordinates']['coordinates'])?
									$messageBody['coordinates']['coordinates']:
									null
								)
						);
						$messageBody['coords'] = $coords;
						
						//get coordinates address
						$address = backend_latLngToAddress($coords[0], $coords[1]);
						if ($address !== false) { $messageBody['address'] = backend_latLngToAddress($coords[0], $coords[1]); }
						else { $messageBody['address'] = ''; }
						
						//get sentiment and attach it to message
						$messageBody['sentiment'] = backend_analyseSentiment($messageBody['text']);
						
						/* KLOUT INFO LOOKUP */
						
						//get twitter user id's klout id
						$kloutId = $klout->KloutIDLookupByID('tw', $messageBody['user']['id_str']);
						
						if (__oDEBUG_BACKEND__) { trigger_error(PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . PHP_EOL . 'Klout API reply to (KloutIDLookupByID for TwID-' . $messageBody['user']['id_str'] . '/TwScrN-' . $messageBody['user']['screen_name'] . '): ' . print_r($klout->lastResult, true) . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL); }
						
						//if user has a klout id
						if (isset($kloutId)) { //get all the data for later use
							
							//set up klout array for incoming data
							$messageBody['klout'] = array();
							
							//get their klout score
							$messageBody['klout']['score'] = floatval($klout->KloutScore($kloutId));
							
							if (__oDEBUG_BACKEND__) { trigger_error(PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . PHP_EOL . 'Klout API reply to (KloutScore for KID-' . $kloutId . '): ' . print_r($klout->lastResult, true) . PHP_EOL . PHP_EOL); }
							
							//get their klout topics of influence
							$messageBody['klout']['topics'] = json_decode($klout->KloutUserTopics($kloutId), true);
							
							if (__oDEBUG_BACKEND__) { trigger_error(PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . PHP_EOL . 'Klout API reply to (KloutUserTopics for KID-' . $kloutId . '): ' . print_r($klout->lastResult, true) . PHP_EOL . PHP_EOL); }
							
							//get their klout list of influencers and influencees
							$kloutInfluence = json_decode($klout->KloutUserInfluence($kloutId), true);
							$messageBody['klout']['influencers'] = $kloutInfluence['myInfluencers'];
							$messageBody['klout']['influencees'] = $kloutInfluence['myInfluencees'];
							
							if (__oDEBUG_BACKEND__) { trigger_error(PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . PHP_EOL . 'Klout API reply to (KloutUserInfluence for KID-' . $kloutId . '): ' . print_r($klout->lastResult, true) . PHP_EOL . PHP_EOL); }
							
						}
						
						if ($module === 'discover') {
							if ($site->job->storeMessage($tokenType, $messageBody, $job['id'], 'discover')) {
								$countValid++; $currentMessageCount++;
							} else { $countDBError++; }
						} else {
							$site->job->storeMessage($tokenType, $messageBody);
						}
						
					//otherwise, just create the link for discover
					} else if ($module === 'discover') {
						if ($site->job->storeMessage($tokenType, $messageBody, $job['id'], 'discover', null, true)) {
							$countValid++; $currentMessageCount++;
						} else { $countDBError++; }
					}

					//if module is Campaign, send out a reponse message as well
					if ($module === 'campaign') {
						
						//check job hourly limit
						if (isset($job['hourly_limit']) && intval($job['hourly_limit']) > 0 && $site->job->timeFrameLimitExceeded('1 hour', $job['hourly_limit'], $job['id'], $module, true)) { $countSkippedMessagePerTimeLimitReached++; continue; }

						//if message user has blacklisted themselves, skip message
						if ($site->job->userIsBlacklisted($tokenType, $messageBody['user']['screen_name'])) { $countSkippedBlacklisted++; continue; }

						//if the message user has saved preferences, and this campaign is not something they are interested in, skip message
						//if (!$site->job->userIsPreferenced($tokenType, $messageBody['user']['screen_name'], $job['category'])) { $countSkippedPreferences++; continue; }
						
						//if the system has not already sent a message as part of this campaign
						if (!$site->job->messageToUserExists($tokenType, $messageBody['user']['id_str'], $job['id'])) {
							
							//if the message user's Klout score is too low, skip sending a reply
							if (!isset($messageBody['klout']) || $messageBody['klout']['score'] < __oBACKEND_CAMPAIGN_REPLY_KLOUT_LOWER_LIMIT__) { $countSkippedKloutScoreTooLow++; continue; }
							
							//shorten URL
							$shortCode = $site->urlService->urlToShortCode(__oCompanyBrandURL__ . '/url/' . $module . '/' . $job['id'] . '/' . $tokenType . '/' . $messageBody['id_str']);
							$url = __oCompanyBrandURL__ . '/url/' . $shortCode;

							$parsed_text = str_replace(array('%r', '%c'), array('@' . $messageBody['user']['screen_name'], $user['business_type']), $job['response_text']);
							$text = $parsed_text . ' ' . $url; //text to send to people

							$params = array(
								'in_reply_to_status_id' => $messageBody['id_str'],
								'status' => $text
							);
							$content_message = objectToArray($site->twitter->oauth->post('statuses/update', $params));

							if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . date('Y-m-d H:i:s') . ' - ' . $site->twitter->oauth->http_code . ': ' . $site->twitter->oauth->url; if (isset($content_message['errors'])) { foreach($content_message['errors'] as $error) { $message .= ' / ' . print_r($error, true); } } trigger_error(PHP_EOL . PHP_EOL .  $message . PHP_EOL . PHP_EOL); }

							//store it to disk
							if ($site->twitter->oauth->http_code === 200) {

								//attach coordinates so it can be displayed on a map later on
								$content_message['coords'] = $coords;

								//save initial message info
								$content_message['user'] = $messageBody['user'];

								//save initial message message id
								$content_message['initiating_message_id_str'] = $messageBody['id_str'];
								
								//save initial message text
								$content_message['text'] = $messageBody['text'];
								
								//save message creation time as mongo date for later analysis
								$content_message['created_mongo'] = strtotime($content_message['created_at']);

								//store the sent message
								if ($site->job->storeMessage($tokenType, $content_message, $job['id'], 'campaign', true)) {
									$countValid++; $currentMessageCount++;
								} else { $countDBError++; }

							} else { $countServiceError++; }

						}

				   }

				} else { $countInvalidCoordinates++; }

			}
			
			trigger_error(
				PHP_EOL . PHP_EOL
				. ucfirst($module) . ' with ID ' . $job['id'] . ' belonging to User ID ' . $job['user_id'] . ' (' . $user['firstname'] . ' ' . $user['lastname'] . ' / ' . $user['email'] . ' / ' . $user['business_type'] . ') ' . PHP_EOL
				. 'processed ' . count($content_search['statuses']) . ' new TWITTER messages'
				. (count($content_search['statuses'])?
					', of which: ' . PHP_EOL
					. ($countValid?'    ' . $countValid . ' were valid ' . PHP_EOL:'')
					. ($countServiceError?'    ' . $countServiceError . ' gave Service Errors ' . PHP_EOL:'')
					. ($countDBError?'    ' . $countDBError . ' gave Database Errors ' . PHP_EOL:'')
					. ($countInvalidCoordinates?'    ' . $countInvalidCoordinates . ' had invalid coordinates ' . PHP_EOL:'')
					. ($countTooOld?'    ' . $countTooOld . ' were too old ' . PHP_EOL:'')
					. ($countSkippedFilter?'    ' . $countSkippedFilter . ' were skipped because Filter (' . $job['filter'] . ') had no matches ' . PHP_EOL:'')
					. ($countSkippedFilterEx?'    ' . $countSkippedFilterEx . ' were skipped because FilterEx (' . $job['filter_ex'] . ') had at least a match ' . PHP_EOL:'')
					. ($countSkippedMessagePerTimeLimitReached?'    ' . $countSkippedMessagePerTimeLimitReached . ' were skipped because Hourly Limit (' . $job['hourly_limit'] . ') was reached ' . PHP_EOL:'')
					. ($countSkippedBlacklisted?'    ' . $countSkippedBlacklisted . ' were blacklisted' . PHP_EOL:'')
					. ($countSkippedPreferences?'    ' . $countSkippedPreferences . ' had preferences and none were matched' . PHP_EOL:'')
					. ($countSkippedKloutScoreTooLow?'    ' . $countSkippedKloutScoreTooLow . ' were skipped because of too low Klout score (<' . __oBACKEND_CAMPAIGN_REPLY_KLOUT_LOWER_LIMIT__ . ') [Campaign]' . PHP_EOL:'')
					:'.' . PHP_EOL
				) . PHP_EOL
			);

			//store last message id for future calls
			if (isset($content_search['search_metadata'])) {
				$site->job->setStatus(array('jobType' => $module, 'id' => $job['id'], 'since_id' => $content_search['search_metadata']['max_id_str']));
			}

		//otherwise, remove token due to being invalid
		} elseif ($site->twitter->oauth->http_code === 401) {
			$removeToken = true;
		} else {
	
			if (!__oDEBUG_BACKEND__) {
				$message = ''; if (isset($content_search['errors'])) { foreach($content_search['errors'] as $error) { $message .= ' / ' . print_r($error, true); } } trigger_error(PHP_EOL . PHP_EOL .  $message . PHP_EOL . PHP_EOL);
				trigger_error(
					PHP_EOL . PHP_EOL
					. ucfirst($module) . ' with ID ' . $job['id'] . ' belonging to User ID ' . $job['user_id'] . ' (' . $user['firstname'] . ' ' . $user['lastname'] . ' - ' . $user['email'] . ') '
					. 'processed 0 new messages.' . (strlen($message)?' Service Error: (' . $message . ').':'') .
					PHP_EOL . PHP_EOL
				);
			}
		
		}
		
	}
	
	//invalid token
	if (isset($removeToken) && $removeToken === true) {
		if ($site->twitter->removeToken(false, $job['authKeyId'])) {
			if ($site->job->setStatus(array('jobType' => $module, 'authKeyId' => -1, 'authKeyType' => ''))) {
				trigger_error(PHP_EOL . PHP_EOL . 'Authentication Token from ' . ucfirst($tokenType) . ' with ID ' . $job['authKeyId'] . ' was removed due to being invalid. Skipped.' . PHP_EOL . PHP_EOL);
			}
		}
	}