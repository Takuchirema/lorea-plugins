<?php
/**
 * @uses string $vars['body'] The body
 */

header("Content-Type: application/json");

// allow caching as required by stupid MS products for https feeds.
header('Pragma: public', true);

echo $vars['body'];