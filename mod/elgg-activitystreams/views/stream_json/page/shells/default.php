<?php
/**
 * Activity streams json output pageshell
 */

// at this point everything should be jsonified
echo activity_streams_json_encode(array('items' => $vars['body']), false);