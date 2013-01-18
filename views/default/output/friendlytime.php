<?php
/**
 * Friendly time
 * Translates an epoch time into a human-readable time.
 *
 * @uses string $vars['time'] Unix-style epoch timestamp
 *
 * @package      Lorea
 * @subpackage   FriendlyTime
 *
 * Copyright 2011-2013 Lorea Faeries <federation@lorea.org>
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License
 * as published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this program. If not, see
 * <http://www.gnu.org/licenses/>.
 */

$friendly_time = elgg_get_friendly_time($vars['time']);
$timestamp     = htmlspecialchars(date(elgg_echo('friendlytime:date_format'), $vars['time']));

echo <<<___HTML
	<span class="elgg-friendlytime">
		<time datetime="$timestamp">$friendly_time</time>
		<span class="hidden">{$vars['time']}</span>
	</span>
___HTML;
