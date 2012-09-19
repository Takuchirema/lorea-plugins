<?php

class PuSH {
	public static function updateRiverEventHandler($event, $object_type, $item) {
		global $CONFIG;

		$object_guid = $item->object_guid;
		$subject_guid = $item->subject_guid;

		$access_id = $item->access_id;

		$object = get_entity($object_guid);
		$subject = get_entity($subject_guid);

		$topic_urls = array();

		// ensure only public stuff gets notified away
		if ($object->access_id != ACCESS_PUBLIC || $subject->access_id != ACCESS_PUBLIC || $access_id != ACCESS_PUBLIC) {
			return NULL;
		}

		// notify user endpoint
		if ($subject && $subject instanceof ElggUser) {
			//check that it's a local person
			if ($subject->getURL() && strpos($subject->getURL(), $CONFIG->wwwroot) == 0) {
				$topic_urls[] = $subject->getURL() . "?view=atom";
			}
		}

		// notify group endpoint
		if ($object) {
			$container = get_entity($object->container_guid);
			if ($container instanceof ElggGroup) {
				//check that it's a local group
				if ($container->getURL() && strpos($container->getURL(), $CONFIG->wwwroot) == 0) {
					$topic_urls[] = $container->getURL() . "?view=atom";
				}
			} elseif ($object instanceof ElggGroup) {
				//check that it's a local group
				if ($object->getURL() && strpos($object->getURL(), $CONFIG->wwwroot) == 0) {
					$topic_urls[] = $object->getURL() . "?view=atom";
				}
			}
		}

		// notify network endpoint
		$topic_urls[] = $CONFIG->wwwroot . "activity/all?view=atom";
		$topic_urls[] = $CONFIG->wwwroot . "activity/all?view=rss";

		$hub_url = elgg_get_plugin_setting('hub', 'elgg-push');

		$p = new PuSHPublisher($hub_url);

		if (!$p->publish_update($topic_urls)) {
			elgg_log($p->last_response(), 'ERROR');
		}
	}
}
