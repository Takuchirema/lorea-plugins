<?php
/**
 * Elgg XRD output pageshell
 */

$host = $_SERVER['HTTP_HOST'];

header("Content-Type: application/xrds+xml");

$body = elgg_extract('body', $vars, '');

$namespaces = elgg_view('extensions/xmlns');
$extensions = elgg_view('extensions/channel');

echo '<?xml version="1.0" encoding="UTF-8"?>';

echo <<<END
<XRD xmlns="http://docs.oasis-open.org/ns/xri/xrd-1.0" xmlns:hm="http://host-meta.net/xrd/1.0" $namespaces>
  <hm:Host>$host</hm:Host>
  $body
  $extensions
</XRD>
END;
