<?php
	// newer ostatus way?
        echo '        <link rel="salmon" href="'. $vars['url']."salmon/\" />\n";

	// older ostatus?
        if ($entity = elgg_get_page_owner_entity()) {
                echo '        <link rel="http://salmon-protocol.org/ns/salmon-replies" href="'. $vars['url'].'salmon/endpoint/'.$entity->guid."\" />\n";
                echo '        <link rel="http://salmon-protocol.org/ns/salmon-mention" href="'. $vars['url'].'salmon/endpoint/'.$entity->guid."\" />\n";
        }
?>
