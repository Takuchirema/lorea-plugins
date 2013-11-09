<?php
/**
 * Federated Objects -- Generic view for remote objects
 *
 * @package        Lorea
 * @subpackage     FederatedObjects
 *
 * Copyright 2012-2013 Lorea Faeries <federation@lorea.org>
 *
 * This file is part of the FederatedObjects plugin for Elgg.
 *
 * FederatedObjects is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License
 * as published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * FederatedObjects is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this program. If not, see
 * <http://www.gnu.org/licenses/>.
 */

$entity = $vars['entity'];

$pars = array();

$pars['name'] = $entity->name;
$pars['type'] = $entity->getType();
$pars['subtype'] = $entity->getSubtype();
$pars['atom_id'] = $entity->atom_id;
$pars['atom_link'] = $entity->atom_link;

foreach($pars as $key => $value) {
	echo "<p><b>$key:</b> $value</p>";
}
