<?php
/**
 * Elggman plugin language pack
 */

$english = array(
	'elggman' => "Mailing list",
	'elggman:mailname' => "Mailing list domain",
	'elggman:subscribe' => "Subscribe",
	'elggman:unsubscribe' => "Unsubscribe",
	'elggman:subscribe:info' => "You'll receive forum posts in your mailbox and you'll be able to reply writing a message.",
	'elggman:subscription:failure' => "Something went wrong while you was subscribing",
	'elggman:unsubscription:failure' => "Something went wrong while you was unsubscribing",
	'elggman:subscribed' => "You subscribed successfully!",
	'elggman:unsubscribed' => "You unsubscribed successfully!",
	'elggman:alreadysubscribed' => "You are already subscribed",
	'elggman:nopermissions' => "You have no permissons to subscribe to list, maybe you need to send a join request",
	'elggman:welcome:subject' => "Welcome to the %s mailing list!",
	'elggman:welcome:body' => "Hi %s!

You are now a member of the '%s' mailing list! Click below to begin posting!

%s

or send a message to

%s",
	'elggman:subscription:options' => "Subscription options",
	'elggman:owner' => "%s's mailing list",
	'elggman:members' => "Mailing list members",
	'elggman:obfuscated' => "Send my email obfuscated on mails (%s@%s)",
	'elggman:starred' => "Receive mails only from starred threads",
	'notification:method:mailshot' => 'Mailing list',
	'elggman:dependency_fail' => 'You need to install php-mail-mimedecode before using this plugin.',
	// api key
	'elggman:api_key' => "Mail server api key",
	'elggman:api_key:description' => "Configure your mail server to use this api key when sending to elgg.",
	
		
);

add_translation('en', $english);
