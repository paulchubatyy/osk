<?php
/*
 * Plugin Name: Old Spam Killer
 * Plugin URI: http://blog.xobb.me/old-spam-killer
 * Description: Removes spam comments older than 1 month, no options, just bizniz.
 * Version: 0.0.1
 * Author: Paul Chubatyy
 * Author URI: http://blog.xobb.me/
 */
// Activate
register_activation_hook(__FILE__, 'osk_activation');
// Deactivate
register_deactivation_hook(__FILE__, 'osk_deactivation');

function osk_activation()
{
	wp_schedule_event(time(), 'hourly', 'osk_hook');
}

function osk_deactivation()
{
	wp_clear_scheduled_hook('osk_hook');
}

add_action('osk_hook', 'osk_remove_comments');

function osk_remove_comments()
{
	global $wpdb;
	// Get the comments to delete
	$comments = $wpdb->get_col($wpdb->prepare("
		SELECT `comment_ID`
		FROM $wpdb->comments
		WHERE `comment_approved` = 'spam'
			AND `comment_date` < DATE_SUB(NOW(), INTERVAL 1 MONTH)
		ORDER BY `comment_date` ASC
		LIMIT 100
	"));
	// loop through
	foreach ($comments as $comment_id) {
		// and kill the spammy stuff
		wp_delete_comment($comment_id);
	}
}
