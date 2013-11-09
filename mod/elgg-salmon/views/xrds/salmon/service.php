<?php
	$url = elgg_get_site_url();
?>
<Service>
      <Type>https://lorea.org/protocols/salmon</Type>
</Service>
<Link rel="salmon-endpoint" href="<?php echo $url; ?>salmon/" />
<Link rel="http://salmon-protocol.org/ns/salmon-mention" href="<?php echo $url; ?>salmon/" />
