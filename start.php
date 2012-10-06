<?php
/**
 * Elgg email revalidate plugin
 *
 * @package ElggEmailRevalidate
 */


elgg_register_event_handler('init', 'system', 'email_revalidate_init');

/**
 * Init function
 */
function email_revalidate_init() {
	elgg_register_plugin_hook_handler('usersettings:save', 'user', 'email_revalidate_user_settings_save');
	elgg_register_page_handler('revalidate_email', 'email_revalidate_page_handler');
}

/**
 * Checks sent passed validation code and user guids and validates the user.
 *
 * @param array $page
 * @return bool
 */
function email_revalidate_page_handler($page) {

	if (isset($page[0]) && $page[0] == 'confirm') {
		$code = sanitise_string(get_input('c', FALSE));
		$user_guid = get_input('u', FALSE);
		$user = get_entity($user_guid);

		if ($code && $user) {
			if (email_revalidate_email($user_guid, $code)) {
				system_message(elgg_echo('email:confirm:success'));

				try {
					login($user);
				} catch(LoginException $e){
					register_error($e->getMessage());
				}
			} else {
				register_error(elgg_echo('email:confirm:fail'));
			}
		} else {
			register_error(elgg_echo('email:confirm:fail'));
		}

	} else {
		register_error(elgg_echo('email:confirm:fail'));
	}

	// forward to front page
	forward('');
}

/**
 * Plugin hook handler for usersettings:save
 * 
 * @return boolean|null 
 */
function email_revalidate_user_settings_save() {
	$email = get_input('email');
	$user_id = get_input('guid');

	if (!$user_id) {
		$user = elgg_get_logged_in_user_entity();
	} else {
		$user = get_entity($user_id);
	}

	if (!is_email_address($email)) {
		register_error(elgg_echo('email:save:fail'));
		return false;
	}

	if ($user) {
		if (strcmp($email, $user->email) != 0) {
			if (!get_user_by_email($email)) {
				$user->new_email = $email;
				set_input('email', $user->email);
				email_revalidate_request_validation($user->guid);
			} else {
				register_error(elgg_echo('registration:dupeemail'));
			}
		} else {
			// no change
			return null;
		}
	} else {
		register_error(elgg_echo('email:save:fail'));
	}
	return false;
}

/**
 * Request user validation email.
 * Send email out to the address and request a confirmation.
 *
 * @param int  $user_guid       The user's GUID
 * @param bool $admin_requested Was it requested by admin
 * @return mixed
 */
function email_revalidate_request_validation($user_guid) {

	$site = elgg_get_site_entity();

	$user_guid = (int)$user_guid;
	$user = get_entity($user_guid);

	if (($user) && ($user instanceof ElggUser)) {
		// Work out validate link
		$code = uservalidationbyemail_generate_code($user_guid, $user->new_email);
		$link = "{$site->url}revalidate_email/confirm?u=$user_guid&c=$code";

		// Send validation email
		$subject = elgg_echo('email:validate:subject', array($user->name, $site->name));
		$body = elgg_echo('email:validate:body', array($user->name, $site->name, $link, $site->name, $site->url));
		$result = notify_user($user->guid, $site->guid, $subject, $body, NULL, 'email');

		if ($result) {
			system_message(elgg_echo('email:revalidate:ok'));
		}

		return $result;
	}

	return FALSE;
}

/**
 * Validate a user
 *
 * @param int    $user_guid
 * @param string $code
 * @return bool
 */
function email_revalidate_email($user_guid, $code) {
	$user = get_entity($user_guid);

	if ($code == uservalidationbyemail_generate_code($user_guid, $user->new_email)) {
		$user->email = $user->new_email;
		unset($user->new_email);
		return $user->save();
	}

	return false;
}