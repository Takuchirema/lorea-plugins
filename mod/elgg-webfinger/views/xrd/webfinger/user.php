<?php
$site_url = elgg_get_site_url();

// check if id starts with tag:host,
$host = parse_url($site_url, PHP_URL_HOST);
	
$user = $vars['entity'];
?>
	<Subject>acct:<?php echo $user->username."@".$host; ?></Subject>
	<Alias><?php echo $user->getURL(); ?></Alias>
	<Link rel="http://webfinger.net/rel/profile-page" type="text/html" href="<?php echo $user->getURL(); ?>" />
