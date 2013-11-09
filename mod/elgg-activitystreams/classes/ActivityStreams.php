<?php
/**
 * Static helper methods
 */
class ActivityStreams {
	public static function getEntityAtomID(ElggEntity $entity) {
                $host = parse_url(elgg_get_site_url(), PHP_URL_HOST);
                $date = date('Y-m-d', $entity->time_created);
                $type = $entity->getType();
                $guid = $entity->getGUID();

                $id = "tag:$host,$date:/$type/$guid";

		#$id = htmlspecialchars($entity->getURL());
		$params = array('entity' => $entity);

		// allow plugins to override default id
		$atom_id = elgg_trigger_plugin_hook('activitystreams:id', 'entity', $params, $id);
		
		return $atom_id;
	}
	public static function getObject($item) {
		$object = $item->getObjectEntity();
		$params = array('item' => $item);
		return elgg_trigger_plugin_hook('activitystreams:object', 'river', $params, $object);
	}

	public static function getParent(ElggEntity $entity) {
		$params = array('entity' => $entity);
		return elgg_trigger_plugin_hook('activitystreams:parent', 'entity', $params, false);
	}
	public static function formatAvatarIcons($entity) {
		echo elgg_view('profile/details', array('entity' => $entity));
	}

	public static function getRiverAtomID(ElggRiverItem $item) {
		$host = parse_url(elgg_get_site_url(), PHP_URL_HOST);
		$date = date('Y-m-d', $item->posted);
		$id = $item->id;
		
		$atom_id = "tag:$host,$date:/river/$id";
		$params = array('item' => $item);

		// allow plugins to override default id
		$atom_id = elgg_trigger_plugin_hook('activitystreams:id', 'river', $params, $atom_id);
		return $atom_id;
	}

	public static function getAnnotationAtomID($comment) {
		$site_url = elgg_get_site_url();

		$atom_id = "{$site_url}annotation/$comment->id";

		$params = array('annotation' => $comment);

		// allow plugins to override default id
		$atom_id = elgg_trigger_plugin_hook('activitystreams:id', 'annotation', $params, $atom_id);
		return $atom_id;
	}


	public static function formatDate($date) {
		return date(DATE_ATOM, $date);
	}

	public static function formatTags($entity) {
		$tags = $entity->getTags();
		if (!$tags) {
			return "";
		}
		$output = "";
		foreach ($tags as $tag) {
			$tag = htmlspecialchars($tag);
			$output .= '<category scheme="urn:tag" term="'.$tag.'" label="'.$tag.'" />';
		}
		return $output;
	}
}
