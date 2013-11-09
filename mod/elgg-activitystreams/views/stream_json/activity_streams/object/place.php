<?php
/**
 * AS Place
 *
 * @uses string $vars['position']       ISO 6709 compliant GPS string. For instance: +27.5916+086.5640+8850/
 * @uses string $vars['formatted']      A formatted representation of the address
 * @uses string $vars['street_address']
 * @uses string $vars['locality']
 * @uses string $vars['region']
 * @uses string $vars['postal_code']
 * @uses string $vars['country']
 * 
 */

$vars['type'] = 'place';
$vars['properties'] = activity_streams_build_array(array('position'), $vars);

$address_map = array(
	'formatted',
	'street_address' => 'streetAddress',
	'locality',
	'region',
	'postal_code',
	'country'
);

$address = activity_streams_build_array($address_map, $vars);

if ($address) {
	$vars['properties']['address'] = $address;
}

echo elgg_view('activity_streams/object/elements/base', $vars);