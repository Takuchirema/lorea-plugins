<?php
/**
 * An activity stream Person object.
 *
 * @uses ElggUser $vars['entity'] If this is set, the relevant info is extracted
 *
 * Mandatory - See AS/objects/elements/base for descriptions.
 * @uses string        $vars['display_name']
 * @uses AS/media_link $vars['image']
 * @uses string        $vars['id']
 * @uses string        $vars['object_url']
 *
 * Optional-ish (The spec in one place says these are required, then below that says they're optional)
 * @uses string        $vars['published']
 * @uses string        $vars['updated']
 *
 * All other valid AS/Object attributes are accepted as optional attributes.
 */

$vars['type'] = 'person';

echo elgg_view('activity_streams/object/elements/base', $vars);