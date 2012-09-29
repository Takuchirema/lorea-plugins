<?php

$user = $vars['entity'];
$key = new SalmonKey($user);

// salmon links
// newer ostatus
echo '    <Link rel="salmon" href="'. $vars['url'].'salmon/endpoint/'.$user->guid.'" />'."\n";
// older ostatus
echo '    <Link rel="http://salmon-protocol.org/ns/salmon-replies" href="'. $vars['url'].'salmon/endpoint/'.$user->guid.'" />'."\n";
echo '    <Link rel="http://salmon-protocol.org/ns/salmon-mention" href="'. $vars['url'].'salmon/endpoint/'.$user->guid.'" />'."\n";

// ostatus feed
echo '    <Link rel="http://schemas.google.com/g/2010#updates-from" type="application/atom+xml" href="'.$user->getURL().'?view=atom" />'."\n";

if ($user instanceof ElggUser) {
	// public key
        echo '    <Link rel="magic-public-key" href="'.$key->echoKeyUrl().'" />'."\n";
	// webfinger pointer to profile page
}
?>
