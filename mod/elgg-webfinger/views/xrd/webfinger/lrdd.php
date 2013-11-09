<?php
	$url = elgg_get_site_url();
?>
<Link rel="lrdd" template="<?php echo $url; ?>webfinger?uri={uri}"  type="application/xrd+xml" />
