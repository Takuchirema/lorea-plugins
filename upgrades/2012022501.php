<?php

$items = elgg_get_entities(array(
	'type' => 'user',
	'limit' => 5,
	'order_by' => 'e.time_created asc',
));

// if not items, no upgrade required
if (!$items) {
	return;
}

$local_version = elgg_get_plugin_setting('version', 'elggpg');
if (2012022501 < $local_version) {
	error_log("ElggPG requires no upgrade");
	// no upgrade required
	return;
}

global $MIGRATED;
$MIGRATED = 0;
/**
 * Sets the opengpg_publickey for users having a public key
 *
 * @param ElggObject $item
 * @return bool
 */
function elggpg_2012022501($user) {
	// it is necessary to load the gpg library to make sure gpg path is set.
	global $MIGRATED;
	$MIGRATED += 1;
	if ($MIGRATED % 100 == 0) {
		error_log(" * elggpg $user->guid");
	}
	elgg_load_library('elggpg');
	$user_fp = current(elgg_get_metadata(array(
                'guid' => $user->guid,
                'metadata_name' => 'openpgp_publickey',
        )));
	$gnupg = new gnupg();
	if (!$user_fp && $user->email) {
		try {
			$info = $gnupg->keyinfo($user->email);
			$fingerprint = $info[0]['subkeys'][0]['fingerprint'];
			if ($fingerprint) {
				create_metadata($user->guid, "openpgp_publickey", $fingerprint, 'text', $user->guid, ACCESS_LOGGEDIN);
				error_log("   fingerprint $user->email $fingerprint");
			}
		}
		catch (Exception $e) {
			// no encryption key
		}
	}
	return true;
}

$previous_access = elgg_set_ignore_access(true);
$options = array(
	'type' => 'user',
	'limit' => 0,
);
$batch = new ElggBatch('elgg_get_entities', $options, 'elggpg_2012022501', 100);
elgg_set_ignore_access($previous_access);

if ($batch->callbackResult) {
	error_log("Elgg elggpg upgrade (2012022501) succeeded");
	elgg_set_plugin_setting('version', 2012022501, 'elggpg');
} else {
	error_log("Elgg elggpg upgrade (2012022501) failed");
}
