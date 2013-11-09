<?php

/**
 * Elgg XRDS output pageshell
 */

header("Content-Type: application/xrds+xml");
echo "<?xml version=\"1.0\"?>\n";

$body = elgg_extract('body', $vars, '');

?>
<xrds:XRDS xmlns:xrds="xri://$xrds" xmlns="xri://$xrd*($v*2.0)">
  <XRD>
<?php
        echo elgg_view("xrds/services");
        echo $body;
?>
  </XRD>
</xrds:XRDS>
