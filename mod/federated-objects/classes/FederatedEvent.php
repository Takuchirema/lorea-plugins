<?php
/**
 * Federated Objects -- Federated Event
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

class FederatedEvent {
	public static function create($params, $entity, $tag) {
		$owner = $params['owner_entity'];
		$entry = $params['entry'];
		$notification = $params['notification'];
		$access_id = ACCESS_PUBLIC;
		$entry->registerXPathNamespace('cal', 'urn:ietf:params:xml:ns:xcal');
		$entry->registerXPathNamespace('context', 'http://activitystrea.ms/context/');
		$start_date = $notification->xpath(array("$tag/cal:dtstart", "cal:dtstart"));
		$end_date = $notification->xpath(array("$tag/context:location", "context:location"));
		$venue = $notification->xpath(array("$tag/context:location/poco:address/poco:formatted", "context:location/poco:address/poco:formatted"));

		$summary = htmlspecialchars_decode($notification->xpath(array("$tag/atom:summary", "atom:summary")));
		$body = $notification->getBody();

		if ($entity) {
			$event = $entity;
		}
		else {
			$access = elgg_set_ignore_access(true);

			$event = new ElggObject();
			$event->owner_guid = $owner->getGUID();
			$event->subtype = 'event_calendar';
			$event->title = $params['name'];
			$event->description = $summary;
			if ($body)
				$event->long_description = $body;
			$event->access_id = $access_id;
			if ($venue) {
				$event->venue = $venue;
			}

			if (isset($params['container_entity'])) {
				$event->container_guid = $params['container_entity']->getGUID();
			}
			if ($params['tags']) {
				$event->tags = $params['tags'];
			}
			FederatedEvent::setDate($event, $start_date, $end_date);
			
			$event->save();
			$id = add_to_river('river/object/event_calendar/create', 'create', $owner->getGUID(), $event->guid);
			AtomRiverMapper::setIDMapping($id, $notification->getID(), $notification->provenance);
			$event->atom_id = $params['id'];
			$event->atom_link = $params['link'];
			$event->foreign = true;


			elgg_set_ignore_access($access);
		}
		return $event;
	}
	public static function setDate($event, $start_date, $end_date) {
		$event->start_date = FederatedEvent::getDate($start_date);
                $event->end_date = FederatedEvent::getDate($end_date);

                $start_date = getdate($event->start_date);
                $end_date = getdate($event->end_date);

		$event->start_time = ($start_date['hours']*60)+$start_date['minutes'];
                $event->end_time = ($end_date['hours']*60)+$end_date['minutes'];
                if (($event->start_time == 0) && ($event->end_time == 0))
                         $event->allday = true;

	}
	public static function getDate($str_time) {
		return strtotime($str_time);
	}
	public static function url($object) {
		if ($object->atom_link)
			return $object->atom_link;
		return event_calendar_url($object);
	}
}

