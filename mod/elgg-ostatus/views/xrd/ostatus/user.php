<?php
$site_url = elgg_get_site_url();

?>
	<Link rel="http://ostatus.org/schema/1.0/subscribe" template="<?php echo $site_url; ?>ostatus/subscribe/?uri={uri}" type="text/html" />
