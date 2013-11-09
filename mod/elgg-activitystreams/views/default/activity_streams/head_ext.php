<?php
/**
 * Extends the page/elements/head view to add the alt links for AS formats.
 *
 * @todo There is no way to get a good title.
 */

/*global $autofeed;

if (!$autofeed) {
	return true;
}*/

$xml_url = elgg_http_add_url_query_elements(current_page_url(), array('view' => 'atom'));
$json_url = elgg_http_add_url_query_elements(current_page_url(), array('view' => 'json'));

?>
        <link rel="alternate" type="application/atom+xml" href="<?php echo $xml_url; ?>" />
        <link rel="alternate" type="application/stream+json" href="<?php echo $json_url; ?>" />
