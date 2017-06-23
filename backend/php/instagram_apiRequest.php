<?php
	
	//limits: 	5000 requests / 60min / user
	//			5000 requests / 15min / app
	
	include('instagram_checkTokenValidity.php');

	//if everything is OK, carry on looking
	if ($code === 200) {

		// calculate distance in meters
		$distance = intval(floor($job['radius'] / 0.00062137));
		
		// calculate oldest message timestamp limit
		if ($job['last_check'] > 0) {
			$since = $job['last_check'];
		} else {
			if (intval($job['messageLifeSpanLimit']) > 0) {
				$since = strtotime('-' . $job['messageLifeSpanLimit'], $now);
			} else {
				$since = strtotime('-7 days', $now);
			}
		}
		
		// get content
		$search_call = $site->instagram->oauth->searchMedia(
			$job['centre_lat'],
			$job['centre_lng'],
			($distance>5000?5000:$distance),
			$since,
			$now
		);
		$content_search = objectToArray($search_call);

		if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . date('Y-m-d H:i:s') . ' - ' . $content_search['meta']['code'] . ': searchMedia()'; if ($content_search['meta']['code'] > 200) { $message .= ' / ' . $content_search['meta']['error_type'] . ' / ' . $content_search['meta']['error_message']; } trigger_error(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL); }

		//still OK, getting messages
		if ($content_search['meta']['code'] === 200) {
			
			//klout api v2 object
			$klout = new KloutAPIv2(__oKLOUT_KEY__);

			// filter example: ("my, first" "second, thing" third fourth) => [ my, first | second, thing | third | fourth ]
			//exclusion filter
			$filter_ex = str_getcsv($job['filter_ex'], ' '); //explode(' ', $job['filter_ex']);
			//inclusion filter
			$filter = str_getcsv($job['filter'], ' '); //explode(' ', $job['filter']);
			
			//keep track of processed messages this session
			$countValid = 0; $countSkippedFilter = 0; $countSkippedFilterEx = 0; $countInvalidCoordinates = 0; $countTooOld = 0; $countSkippedBlacklisted = 0; $countSkippedPreferences = 0; $countSkippedMessagePerTimeLimitReached = 0; $countServiceError = 0; $countSkippedKloutScoreTooLow = 0; $countDBError = 0;
			
			do {
			
				/*** Parse and Store results ***/
				foreach ($content_search['data'] as $messageBody) {
					
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
							$until = strtotime('+' . $job['messageLifeSpanLimit'], $messageBody['created_time']);
						} else { $until = $now; }
					} else {
						$until = strtotime('+' . __oBACKEND_MESSAGE_AGE_NEW__, $messageBody['created_time']);
					}
					if ($now > $until) { $countTooOld++; continue; }

					//if message has coordinates attached, it gets processed
					if (
						isset($messageBody['location']['latitude']) && is_numeric($messageBody['location']['latitude']) &&
						(
							isset($messageBody['images']['standard_resolution']['url']) ||
							isset($messageBody['videos']['standard_resolution']['url'])
						)
					) {
						
						//standardise field
						if (isset($messageBody['caption']['text'])) { $messageBody['text'] = $messageBody['caption']['text']; }
						else { $messageBody['text'] = ''; }

						//exclusion filter
						$stopEx = false; if (strlen($filter_ex[0]) > 0) { foreach ($filter_ex as $keyword) { if (stripos($messageBody['text'], trim($keyword)) !== false) { $stopEx = true; break; } } } if ($stopEx == true) { $countSkippedFilterEx++; unset($stopEx, $keyword); continue; }

						//inclusion filter
						$stop = false; if (strlen($filter[0]) > 0) { $stop = true; foreach ($filter as $keyword) { if (stripos($messageBody['text'], trim($keyword)) !== false) { $stop = false; break; } } } if ($stop == true) { $countSkippedFilter++; unset($stop, $keyword); continue; }
						
						//save message creation time as mongo date for later analysis
						$messageBody['created_mongo'] = $messageBody['created_time'];
						
						$messageBody['id_str'] = $messageBody['id'];
						
						//if message isn't already in the DB, save it for future reference
						if (!$site->job->messageExists($tokenType, $messageBody['id'])) {
							
							//store token type for message
							$messageBody['smType'] = $tokenType;
							//store token id for message
							$messageBody['smId'] = $tokenId;
							
							//standardise fields
							$messageBody['user']['id_str'] = $messageBody['user']['id'];
							$messageBody['user']['screen_name'] = $messageBody['user']['username'];
							$messageBody['user']['profile_image_url'] = $messageBody['user']['profile_picture'];
							
							$messageBody['_o_text'] = $messageBody['text'];
							
							//if image present, add its link to the text
							if (isset($messageBody['images']['standard_resolution']['url'])) {
								//$shortCode = $site->urlService->urlToShortCode($messageBody['images']['standard_resolution']['url']);
								$shortCode = null;
								if (is_string($shortCode) && strlen($shortCode)) {
									$url = __oCompanyBrandURL__ . '/url/' . $shortCode;
								} else { 
									$url = $messageBody['images']['standard_resolution']['url'];
									if (__oDEBUG_BACKEND__) { trigger_error(PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' failed to generate URL for link (' . $messageBody['images']['standard_resolution']['url'] . ').' . PHP_EOL . PHP_EOL); }
								}
								//$messageBody['text'] .= ' /-/ Image: ' . $url;
								$messageBody['_o_text'] .= '<br><a href="' . $url . '" target="_blank"><img src="' . $url . '" alt="Instagram Image"></a>';
							}
							
							//if video present, add its link to the text
							if (isset($messageBody['videos']['standard_resolution']['url'])) {
								//$shortCode = $site->urlService->urlToShortCode($messageBody['videos']['standard_resolution']['url']);
								$shortCode = null;
								if (is_string($shortCode) && strlen($shortCode)) {
									$url = __oCompanyBrandURL__ . '/url/' . $shortCode;
								} else { 
									$url = $messageBody['videos']['standard_resolution']['url'];
									if (__oDEBUG_BACKEND__) { trigger_error(PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' failed to generate URL for link (' . $messageBody['videos']['standard_resolution']['url'] . ').' . PHP_EOL . PHP_EOL); }
								}
								//$messageBody['text'] .= ' /-/ Video: ' . $url;
								$messageBody['_o_text'] .= '<br>Video: ' . $url;
							}

							//save message coordinates
							$coords = array($messageBody['location']['latitude'], $messageBody['location']['longitude']);
							$messageBody['coords'] = $coords;
							
							//get coordinates address
							$address = backend_latLngToAddress($coords[0], $coords[1]);
							if ($address !== false) { $messageBody['address'] = backend_latLngToAddress($coords[0], $coords[1]); }
							else { $messageBody['address'] = ''; }

							//get sentiment and attach it to message
							if (strlen($messageBody['text']) > 0) {
								$messageBody['sentiment'] = backend_analyseSentiment($messageBody['text']);
							} else {
								$messageBody['sentiment'] = 'none';
							}
							
							//get twitter user id's klout id
							$kloutId = $klout->KloutIDLookupByID('ig', $messageBody['user']['id_str']);

							if (__oDEBUG_BACKEND__) { trigger_error(PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . PHP_EOL . 'Klout API reply to (KloutIDLookupByID for IgID-' . $messageBody['user']['id_str'] . '/IgScrN-' . $messageBody['user']['screen_name'] . '): ' . print_r($klout->lastResult, true) . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL); }

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
							if ($site->job->userIsBlacklisted($tokenType, $messageBody['user']['username'])) { $countSkippedBlacklisted++; continue; }

							//if the message user has saved preferences, and this campaign is not something they are interested in, skip message
							//if (!$site->job->userIsPreferenced($tokenType, $messageBody['user']['username'], $job['category'])) { $countSkippedPreferences++; continue; }

							//if the system has not already sent a message as part of this campaign
							if (!$site->job->messageToUserExists($tokenType, $messageBody['user']['id'], $job['id'])) {

								//shorten URL
								$shortCode = $site->urlService->urlToShortCode(__oCompanyBrandURL__ . '/url/' . $module . '/' . $job['id'] . '/' . $tokenType . '/' . $messageBody['id']);
								$url = __oCompanyBrandURL__ . '/url/' . $shortCode;

								$parsed_text = str_replace(array('%r', '%c'), array('@' . $messageBody['user']['username'], $user['business_type']), $job['response_text']);
								$text = $parsed_text . ' ' . $url; //text to send to people

								$content_message = $site->instagram->oauth->addMediaComment($messageBody['id'], $text);

								if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $job['id'] . ' - ' . date('Y-m-d H:i:s') . ' - ' . $content_message['meta']['code'] . ': addMediaComment'; if ($content_message['meta']['code'] > 200) { $message .= ' / ' . $content_message['meta']['error_type'] . ' / ' . $content_message['meta']['error_message']; } trigger_error(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL); }

								//store it to disk
								if ($content_message['meta']['code'] === 200) {

									//attach coordinates so it can be displayed on a map later on
									$content_message['coords'] = $coords;

									//save initial message info
									$content_message['user'] = $messageBody['user'];

									//save initial message message id
									$content_message['initiating_message_id'] = $messageBody['id'];

									//save initial message text
									$content_message['text'] = $messageBody['text'];
									
									//save message creation time as mongo date for later analysis
									$content_message['created_mongo'] = $content_message['created_time'];
									
									$content_message['id_str'] = $content_message['id'];

									// remove unwanted response data
									unset($content_message['meta'], $content_message['data']);

									//store the sent message
									if ($site->job->storeMessage($tokenType, $content_message, $job['id'], 'campaign', true)) {
										$countValid++; $currentMessageCount++;
									} else { $countDBError++; }

								} else { $countServiceError++; }

							}

					   }

					} else { $countInvalidCoordinates++; }

				}
				
				if (isset($content_search['pagination'])) {
					$search_call = $site->instagram->oauth->pagination($search_call);
					$content_search = objectToArray($search_call);
					$stopLoopNow = false;
				} else {
					$stopLoopNow = true;
				}
			
			} while($stopLoopNow === false);
			
			trigger_error(
				PHP_EOL . PHP_EOL
				. ucfirst($module) . ' with ID ' . $job['id'] . ' belonging to User ID ' . $job['user_id'] . ' (' . $user['firstname'] . ' ' . $user['lastname'] . ' / ' . $user['email'] . ' / ' . $user['business_type'] . ') ' . PHP_EOL
				. 'processed ' . count($content_search['data']) . ' new INSTAGRAM messages'
				. (count($content_search['data'])?
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
					:'.' . PHP_EOL
				) . PHP_EOL
			);

			//store last message id for future calls
			//$site->job->setStatus(array('jobType' => $module, 'id' => $job['id'], 'since_id' => $maxId));

		//otherwise, remove token due to being invalid
		} elseif (isset($content_search['meta']['error_type']) && $content_search['meta']['error_type'] === 'OAuthException') {
			$removeToken = true;
		} else {
	
			if (!__oDEBUG_BACKEND__) {
				$message = ''; if ($content_search['meta']['code'] > 200) { $message .= ' / ' . $content_search['meta']['error_type'] . ' / ' . $content_search['meta']['error_message']; } trigger_error(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL);
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
		if ($site->instagram->removeToken(false, $job['authKeyId'])) {
			if ($site->job->setStatus(array('jobType' => $module, 'authKeyId' => -1, 'authKeyType' => ''))) {
				trigger_error(PHP_EOL . PHP_EOL . 'Authentication Token from ' . ucfirst($tokenType) . ' with ID ' . $job['authKeyId'] . ' was removed due to being invalid. Skipped.' . PHP_EOL . PHP_EOL);
			}
		}
	}