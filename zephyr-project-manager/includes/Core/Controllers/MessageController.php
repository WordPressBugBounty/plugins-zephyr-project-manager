<?php

/**
* @package ZephyrProjectManager
*/

namespace ZephyrProjectManager\Core\Controllers;

use ZephyrProjectManager\Core\Utillities;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

class MessageController {
	public function __construct() {

	}

	public function getUnreadMessages() {

	}

	public function getUserReadMessages() {
		$userId = get_current_user_id();
		$messages = maybe_unserialize( get_user_meta( $userId, 'zpm_read_msg', true ) );

		if (!$messages) {
			$messages = [];
		}

		return (array) $messages;
	}

	public function addReadMessage( $msgId ) {
		$userId = get_current_user_id();
		$messages = $this->getUserReadMessages();
		$added = false;

		foreach($messages as $msg) {
			if ($msg == $msgId) {
				$added = true;
			}
		}

		if (!$added) {
			$messages[] = $msgId;
			$added = update_user_meta( $userId, 'zpm_read_msg', serialize($messages));
		}
	}

	public function isRead( $msgId ) {
		$messages = $this->getUserReadMessages();

		foreach($messages as $msg) {
			if ($msg == $msgId) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Returns a message by ID.
	 * @param int $messageID The message ID.
	 * @return array|object|\stdClass|null The message row.
	 */
	public static function get($messageID) {
		global $wpdb;
		$table_name = ZPM_MESSAGES_TABLE;
		$comment = $wpdb->get_row($wpdb->prepare("SELECT id, parent_id, user_id, subject_id, subject, message, type, date_created FROM $table_name WHERE id = %d", $messageID));
		return $comment;
	}

	/**
	 * Checks if the current user can edit a message.
	 *
	 * @param int $messageID The message ID.
	 */
	public static function canEditMessage(int $messageID): bool {
		$message = MessageController::get($messageID);
		$userID = get_current_user_id();

		if (!$message) return false;

		if ($userID == intval($message->user_id)) return true;
		if (current_user_can('administrator')) return true;

		return false;
	}


	/**
	 * Checks if the current user can delete a message.
	 *
	 * @param int $messageID The message ID.
	 */
	public static function canDeleteMessage(int $messageID): bool {
		$message = MessageController::get($messageID);
		$userID = get_current_user_id();

		if (!$message) return false;

		if ($userID == intval($message->user_id)) return true;
		if (current_user_can('administrator')) return true;

		return false;
	}
}