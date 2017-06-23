<?php

	/**
	 * NOT YET IMPLEMENTED.
	 */

	if (strtotime('+' . __oBACKEND_SM_INTERACTION_CHECK__, $token['last_checked_interaction']) < $now) {
		
		// update check timestamp
		$site->database->query(array(
			'name' => 'updateInteractionCheckTime',
			'type' => 'mysql',
			'table' => 'instagram.keys',
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
				'authKeyType' => 'instagram',
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
		
				//get followers
				$content = objectToArray($site->instagram->oauth->getUserFollower());

				if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - ' . $content['meta']['code'] . ': getUserFollower()'; if ($content_search['meta']['code'] > 200) { $message .= ' / ' . $content_search['meta']['error_type'] . ' / ' . $content_search['meta']['error_message']; } trigger_error(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL); }
		
				if ($content['meta']['code'] === 200 && !empty($content['data'])) {
					
					// look at all users following this token owner
					foreach ($content['data'] as $user) {
						
						/**
						 * Check follow status.
						 */
						
						// match interactions with current user id that has not yet followed token owner
						if ($iRes['sm_user_id'] == $user['id']) {

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
									'authKeyId' => $token['id'],
									'authKeyType' => 'instagram',
									'added_at' => time()
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
									'authKeyType' => 'instagram'
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
			
			/*
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
				$messagesMsgs = objectToArray($site->instagram->oauth->get('statuses/user_timeline', $paramsMsgs));

				if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - ' . $site->instagram->oauth->http_code . ': ' . $site->instagram->oauth->url; if (isset($messagesMsgs['errors'])) { foreach($messagesMsgs['errors'] as $error) { $message .= ' / ' . print_r($error, true); } } trigger_error(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL); }

				if ($site->instagram->oauth->http_code === 200 && !empty($messagesMsgs)) {

					foreach ($messagesMsgs as $msg) {

						// match interaction to current message id that has not yet been detected as a response to any of the token owner's messages
						if ($iRes['reply_message_id'] == $msg['in_reply_to_status_id_str']) {

							// update interaction to reflect current user has replied to one of token owner's messages
							$data = array(
								'name' => 'updateReplyInfo',
								'type' => 'mysql',
								'operation' => 'update',
								'table' => 'socialMedia.interaction',
								'columns' => array(
									'responded' => 1,
									'responded_message' => $msg['text'],
									'responded_message_id' => $msg['id_str'],
									'responded_at' => strtotime($msg['created_at'])
								),
								'where' => array(
									'opheme_user_id' => $token['user_id'],
									'sm_user_id' => $iRes['sm_user_id'],
									'authKeyId' => $token['id'],
									'authKeyType' => 'instagram',
									'reply_message_id' => $iRes['reply_message_id']
								)
							);
							$site->database->query($data)->fetch();

						}

					}

				}

				unset($messagesMsgs);

			}
			*/
			
			/**
			 * Check if messages were favourited.
			 */

			/*
			// go through all interactions
			foreach ($interactionResults as $iRes) {
				
				if ($iRes['type'] !== 'message_out') { continue; }
				if (intval($iRes['done']) === 1) { continue; }
			
				$paramsFavs = array(
					'user_id' => $iRes['sm_user_id'],
					'include_entities' => false,
					'count' => 200 //max Twitter allows is 200
				);

				// get all follower messages
				$messagesFavs = objectToArray($site->instagram->oauth->get('favorites/list', $paramsFavs));

				if (__oDEBUG_BACKEND__) { $message = ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - ' . $site->instagram->oauth->http_code . ': ' . $site->instagram->oauth->url; if (isset($messagesFavs['errors'])) { foreach($messagesFavs['errors'] as $error) { $message .= ' / ' . print_r($error, true); } } trigger_error(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL); }

				if ($site->instagram->oauth->http_code === 200 && !empty($messagesFavs)) {

					foreach ($messagesFavs as $msg) {

						// match interaction to current message id that has not yet been detected as a favourite of any of the token owner's messages
						if ($iRes['reply_message_id'] == $msg['id_str']) {

							// update interaction to reflect current user has favourited one of token owner's messages
							$data = array(
								'name' => 'updateFavouritedInfo',
								'type' => 'mysql',
								'operation' => 'update',
								'table' => 'socialMedia.interaction',
								'columns' => array(
									'favourited_at' => $now
								),
								'where' => array(
									'opheme_user_id' => $token['user_id'],
									'sm_user_id' => $iRes['sm_user_id'],
									'authKeyId' => $token['id'],
									'authKeyType' => 'instagram',
									'reply_message_id' => $iRes['reply_message_id']
								)
							);
							$site->database->query($data)->fetch();

						}

					}

				}

				unset($messagesFavs);
				
			}
			*/
			
		}
		
	} else {
		trigger_error(PHP_EOL . PHP_EOL . ucfirst($module) . ' with ID ' . $tokenId . ' (@' . $token['screen_name'] . ') - ' . date('Y-m-d H:i:s') . ' - Not yet time to Check Interaction.' . PHP_EOL . PHP_EOL);
	}