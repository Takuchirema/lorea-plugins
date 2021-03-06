<?php
/**
 * ElggPG -- Download page
 *
 * @package        Lorea
 * @subpackage     ElggPG
 *
 * Copyright 2011-2013 Lorea Faeries <federation@lorea.org>
 *
 * This file is part of the ElggPG plugin for Elgg.
 *
 * ElggPG is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * ElggPG is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this program. If not, see
 * <http://www.gnu.org/licenses/>.
 */

$user = get_user_by_username(get_input('username'));
if (!elgg_is_logged_in() || !$user || !($user->isFriend() || $user->guid == elgg_get_logged_in_user_guid())) {
	forward();
}

header("Pragma: public");
header("Content-type: application/pgp-keys");
header("Content-Disposition: attachment; filename=\"{$user->username}.asc\"");

ob_clean();
flush();

elgg_load_library('elggpg');
echo elggpg_export_key($user);
exit();
