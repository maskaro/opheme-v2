<?php

	if (strtotime('+' . __oBACKEND_SM_INTERACTION_CHECK__, $token['last_checked_interaction']) < $now) {
		
		// update check timestamp
		$site->database->query(array(
			'name' => 'updateInteractionCheckTime',
			'type' => 'mysql',
			'table' => 'twitter.keys',
			'operation' => 'update',
			'columns' => array(
				'last_checked_interaction' => $now
			),
			'where' => array(
				'id' => $token['id']
			)
		))->fetch();
		
		// fetch interaction information for current token owner
		$data = array(
			'name' => 'getUserIds',
			'type' => 'mysql',
			'operation' => 'select',
			'table' => 'socialMedia.interaction',
			'where' => array(
				'opheme_user_id' => $token['user_id'],
				'authKeyId' => $token['id'],
				'authKeyType' => 'twitter',
				'done' => 0
			)
		);
		$interactionResults = $site->database->query($data)->fetch()['getUserIds'];

		// if any interaction info available
		if (is_array($interactionResults)) {
			
			if ( is_assoc_array( $interactionResults ) ) {
			
				$interactionResults = array($interactionResults);

			}
			
			/**
			 * Check response to follows.
			 */
			
			// go through all interactions
			foreach ($interactionResults as $iRes) {
				
				if ($iRes['type'] !== 'follow_out') { continue; }
				if (intval($iRes['done']) === 1) { continue; }
		
				$params = array(
					'stringify_ids' => true,
					'screen_name' => $token['screen_name'],
					'cursor' => -1,
					'count' => 5000 //max Twitter allows is 5000
				);

				// get followers for current token owner
				$content = objectToArray($site->twitter->oauth->get('followers/ids', $params));

				if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - ' . $site->twitter->oauth->http_code . ': ' . $site->twitter->oauth->url; if (isset($content['errors'])) { foreach($content['errors'] as $error) { $message .= ' / ' . print_r($error, true); } } trigger_error(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL); }

				if ($site->twitter->oauth->http_code === 200 && !empty($content['ids'])) {
					
					// look at all users following this token owner
					foreach ($content['ids'] as $userId) {
						
						/**
						 * Check follow status.
						 */
						
						// match interactions with current user id that has not yet followed token owner
						if ($iRes['sm_user_id'] == $userId) {

							// update interactions to show current user is following token owner
							$data1 = array(
								'name' => 'addFollowInfo',
								'type' => 'mysql',
								'operation' => 'insert',
								'table' => 'socialMedia.interaction',
								'columns' => array(
									'opheme_user_id' => $token['user_id'],
									'sm_user_id' => $iRes['sm_user_id'],
									'sm_user_screen_name' => $iRes['sm_user_screen_name'],
									'type' => 'follow_in',
									'done' => 1,
									'authKeyId' => $token['id'],
									'authKeyType' => 'twitter',
									'added_at' => $now
								)
							);
							$site->database->query($data1)->fetch();
							
							$data2 = array(
								'name' => 'updateFollowInfo',
								'type' => 'mysql',
								'operation' => 'update',
								'table' => 'socialMedia.interaction',
								'columns' => array(
									'done' => 1,
								),
								'where' => array(
									'opheme_user_id' => $token['user_id'],
									'sm_user_id' => $iRes['sm_user_id'],
									'type' => 'follow_out',
									'authKeyId' => $token['id'],
									'authKeyType' => 'twitter'
								)
							);
							$site->database->query($data2)->fetch();

						}

					}

				}
				
			}
			
			/**
			 * Check response to reply status.
			 */
			
			// go through all interactions
			foreach ($interactionResults as $iRes) {
				
				if ($iRes['type'] !== 'message_out') { continue; }
				if (intval($iRes['done']) === 1) { continue; }

				$paramsMsgs = array(
					'user_id' => $iRes['sm_user_id'],
					'include_entities' => false,
					'trim_user' => true,
					'include_rts' => true,
					'exclude_replies' => false,
					'count' => 200 //max Twitter allows is 200
				);

				// get all follower messages
				$messagesMsgs = objectToArray($site->twitter->oauth->get('statuses/user_timeline', $paramsMsgs));

				if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - ' . $site->twitter->oauth->http_code . ': ' . $site->twitter->oauth->url; if (isset($messagesMsgs['errors'])) { foreach($messagesMsgs['errors'] as $error) { $message .= ' / ' . print_r($error, true); } } trigger_error(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL); }

				if ($site->twitter->oauth->http_code === 200 && !empty($messagesMsgs)) {

					foreach ($messagesMsgs as $msg) {

						// match interaction to current message id that has not yet been detected as a response to any of the token owner's messages
						if ($iRes['message_id'] == $msg['in_reply_to_status_id_str']) {

							// update interaction to reflect current user has replied to one of token owner's messages
							$data1 = array(
								'name' => 'addReply',
								'type' => 'mysql',
								'operation' => 'insert',
								'table' => 'socialMedia.interaction',
								'columns' => array(
									'opheme_user_id' => $token['user_id'],
									'sm_user_id' => $iRes['sm_user_id'],
									'sm_user_screen_name' => $iRes['sm_user_screen_name'],
									'type' => 'message_in',
									'message' => $msg['text'],
									'message_id' => $msg['id_str'],
									'message_added_at' => strtotime($msg['created_at']),
									'original_message' => $iRes['message'],
									'original_message_id' => $iRes['message_id'],
									'original_message_added_at' => $iRes['message_added_at'],
									'done' => 1,
									'authKeyId' => $token['id'],
									'authKeyType' => 'twitter',
									'added_at' => $now
								)
							);
							$site->database->query($data1)->fetch();
							
							$data2 = array(
								'name' => 'updateReplyInfo',
								'type' => 'mysql',
								'operation' => 'update',
								'table' => 'socialMedia.interaction',
								'columns' => array(
									'done' => 1
								),
								'where' => array(
									'opheme_user_id' => $token['user_id'],
									'sm_user_id' => $iRes['sm_user_id'],
									'type' => 'message_out',
									'message_id' => $iRes['message_id'],
									'authKeyId' => $token['id'],
									'authKeyType' => 'twitter'
								)
							);
							$site->database->query($data2)->fetch();

						}

					}

				}

				unset($messagesMsgs);

			}
			
			/**
			 * Check if messages were favourited.
			 */

			// go through all interactions
			foreach ($interactionResults as $iRes) {
				
				if ($iRes['type'] !== 'message_out') { continue; }
				if (intval($iRes['favourited']) === 1) { continue; }
			
				$paramsFavs = array(
					'user_id' => $iRes['sm_user_id'],
					'include_entities' => false,
					'count' => 200 //max Twitter allows is 200
				);

				// get all follower messages
				$messagesFavs = objectToArray($site->twitter->oauth->get('favorites/list', $paramsFavs));

				if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - ' . $site->twitter->oauth->http_code . ': ' . $site->twitter->oauth->url; if (isset($messagesFavs['errors'])) { foreach($messagesFavs['errors'] as $error) { $message .= ' / ' . print_r($error, true); } } trigger_error(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL); }

				if ($site->twitter->oauth->http_code === 200 && !empty($messagesFavs)) {

					foreach ($messagesFavs as $msg) {

						// match interaction to current message id that has not yet been detected as a favourite of any of the token owner's messages
						if ($iRes['message_id'] == $msg['id_str']) {

							// update interaction to reflect current user has favourited one of token owner's messages
							$data1 = array(
								'name' => 'addFavouritedInfo',
								'type' => 'mysql',
								'operation' => 'insert',
								'table' => 'socialMedia.interaction',
								'columns' => array(
									'opheme_user_id' => $token['user_id'],
									'sm_user_id' => $iRes['sm_user_id'],
									'sm_user_screen_name' => $iRes['sm_user_screen_name'],
									'type' => 'favourite_in',
									'original_message' => $iRes['original_message'],
									'original_message_id' => $iRes['original_message_id'],
									'original_message_added_at' => $iRes['original_message_added_at'],
									'message' => $iRes['message'],
									'message_id' => $iRes['message_id'],
									'message_added_at' => $iRes['message_added_at'],
									'done' => 1,
									'favourited' => 1,
									'authKeyId' => $token['id'],
									'authKeyType' => 'twitter',
									'added_at' => $now
								)
							);
							$site->database->query($data1)->fetch();
							
							$data2 = array(
								'name' => 'updateFavouritedInfo',
								'type' => 'mysql',
								'operation' => 'update',
								'table' => 'socialMedia.interaction',
								'columns' => array(
									'favourited' => 1
								),
								'where' => array(
									'opheme_user_id' => $token['user_id'],
									'sm_user_id' => $iRes['sm_user_id'],
									'type' => 'message_out',
									'message_id' => $iRes['message_id'],
									'authKeyId' => $token['id'],
									'authKeyType' => 'twitter',
								)
							);
							$site->database->query($data2)->fetch();

						}

					}

				}

				unset($messagesFavs);
				
			}

		}
		
	} else {
		trigger_error(PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - Not yet time to Check Interaction.' . PHP_EOL . PHP_EOL);
	}