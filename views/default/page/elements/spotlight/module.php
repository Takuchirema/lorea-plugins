<?php
echo "<div class=\"spotlight-module\">";
echo "<h2>{$vars['title']}</h2>";

echo "<ul>";

foreach($vars['items'] as $item_url => $item_label) {
	echo "<li><a href=\"$item_url\">$item_label</a></li>";
}

echo "</ul>";
echo "</div>";
