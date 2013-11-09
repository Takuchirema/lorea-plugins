<?php
/**
 * Provide a way of setting your email
 *
 * @package Elgg
 * @subpackage Core
 */

$user = elgg_get_page_owner_entity();
$email = isset($user->new_email) ? $user->new_email : $user->email;

if ($user) {
	$title = elgg_echo('email:settings');
	$content = elgg_echo('email:address:label') . ': ';
	$content .= elgg_view('input/email', array(
		'name' => 'email',
		'value' => $email,
	));
	if (isset($user->new_email)) {
		$content .= elgg_echo('email:revalidate:label', array($user->email));
	}
	echo elgg_view_module('info', $title, $content);
}
