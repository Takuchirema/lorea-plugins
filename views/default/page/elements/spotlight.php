<?php
/**
 * Spotlight -- Page footer
 *
 * @package        Lorea
 * @subpackage     Spotlight
 *
 * Copyright 2011-2012 Lorea Faeries <federation@lorea.org>
 *
 * This file is part of the Lorea Spotlight plugin.
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

echo '<div class="spotlight clearfloat">';

echo '<div class="spotlight-column">';

echo elgg_view('page/elements/spotlight/module', array(
	'title' => elgg_echo('news:news'),
	'items' => array(
		'https://n-1.cc/pg/pages/view/9385/' => elgg_echo('news:features'),
	),
));

echo elgg_view('page/elements/spotlight/module', array(
	'title' => elgg_echo('about:lorea'),
	'items' => array(
		'https://lorea.org/' => elgg_echo('about:blog'),
		'https://n-1.cc/g/lorea/' => elgg_echo('about:group'),
		elgg_echo('lorea:sustainability:url') => elgg_echo('lorea:sustainability'),
	),
));

echo '</div><div class="spotlight-column">';

// Note the incorrect XMPP link: it's that Elgg wants to deal with web
// protocols and will prepend them to xmpp:links.
echo elgg_view('page/elements/spotlight/module', array(
	'title' => elgg_echo('contact:contact'),
	'items' => array(
		'mailto:federation@lorea.org' => 'federation@lorea.org',
		'irc://irc.freenode.net/lorea' => '#lorea on Freenode IRC',
		'xmpp://lorea@groups.n-1.cc?join' => "Lorea's XMPP Room",
		'https://n-1.cc/discussion/owner/7826' => 'Web Forum',
	),
));

echo elgg_view('page/elements/spotlight/module', array(
	'title' => elgg_echo('help:help'),
	'items' => array(
		'https://n-1.cc/faq/' => elgg_echo('help:faq'),
		'https://n-1.cc/dokuwiki/9394' => elgg_echo('help:howto'),
		'https://n-1.cc/g/help' => elgg_echo('help:group'),
	),
));

echo '</div><div class="spotlight-column">';

echo elgg_view('page/elements/spotlight/module', array(
	'title' => elgg_echo('dev:dev'),
	'items' => array( <<<<<<< HEAD
		 'https://n-1.cc/g/lorea+code' => elgg_echo('dev:group'),
		 'https://n-1.cc/spotlight/source-code' => elgg_echo('dev:source'),
		'https://n-1.cc/g/bughunting/' => elgg_echo('dev:bughunting'),
		'https://n-1.cc/g/testers-and-usability/' => elgg_echo('dev:testers'),
		'https://dev.lorea.org/' => elgg_echo('dev:network'),
		'https://gitorious.org/lorea/'=> elgg_echo('dev:repo'),

	),
));

echo '</div><div class="spotlight-column">';

$members     = get_number_users();
$online      = find_active_users(600, 0, 0, true);
$groups      = elgg_get_entities(array('type' => 'group', 'count' => true, 'limit' => 0));
$assemblies  = elgg_get_entities(array('type' => 'object', 'subtypes' => array('assembly'), 'count' => true, 'limit' => 0));
$pages       = elgg_get_entities(array('type' => 'object', 'subtypes' => array('page', 'page_top', 'etherpad', 'subpad'), 'count' => true, 'limit' => 0));
$blog        = elgg_get_entities(array('type' => 'object', 'subtype' => 'blog', 'count' => true, 'limit' => 0));
$file        = elgg_get_entities(array('type' => 'object', 'subtype' => 'file', 'count' => true, 'limit' => 0));
$tasks       = elgg_get_entities(array('type' => 'object', 'subtype' => 'task', 'count' => true, 'limit' => 0));

echo elgg_view('page/elements/spotlight/module', array(
	'title' => elgg_echo('stats:stats'),
	'items' => array(
		'members'        => $members . ' ' . elgg_echo('members'),
		'members/online' => $online . ' ' .  elgg_echo('members:label:online'),
		'groups/all'     => $groups . ' ' .  elgg_echo('item:group'),
		'assemblies/all' => $assemblies . ' ' . elgg_echo('stats:assemblies'),
		'tasks/all'      => $tasks . ' ' . elgg_echo('stats:tasks'),
		'pages/all'      => $pages . ' ' .   elgg_echo('item:object:page'),
		'blog/all'       => $blog . ' ' .    elgg_echo('item:object:blog'),
		'file/all'       => $file . ' ' .    elgg_echo('item:object:file'),
		//'tidypics/all' => $photos . ' ' .  elgg_echo('item:object:photo'),
	),
));

echo '</div>';
echo '</div>';
