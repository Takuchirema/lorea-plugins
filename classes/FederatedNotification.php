<?php

class FederatedNotification {
	// River to atomid mapping
	public static function getRiverAtomID($river_id) {
		global $CONFIG;
		$prefix = $CONFIG->dbprefix;
		$river_id = (int)$river_id;
		$sql = "SELECT atom_id FROM {$prefix}river_atomid_mapping WHERE river_id=$river_id";
		$data = get_data_row($sql);
		if ($data)
			return $data->atom_id;
	}
	public static function getRiverID($atom_id) {
		global $CONFIG;
		$prefix = $CONFIG->dbprefix;
		$atom_id = sanitise_string($atom_id);
		$sql = "SELECT river_id FROM {$prefix}river_atomid_mapping WHERE atom_id='$atom_id'";
		$data = get_data_row($sql);
		if ($data)
			return $data->river_id;
	}
	public static function setIDMapping($river_id, $atom_id) {
		global $CONFIG;
		$river_id = (int)$river_id;
		$atom_id = sanitise_string($atom_id);
		$prefix = $CONFIG->dbprefix;
		$sql = "INSERT INTO {$prefix}river_atomid_mapping (river_id, atom_id) VALUES($river_id, '$atom_id')";
		insert_data($sql);
	}
	// Elgg Callbacks
	public static function river_id($hook, $type, $return, $params) {
		$item = $params['item'];
		$atom_id = FederatedNotification::getRiverAtomID($item->id);
		if ($atom_id)
			return $atom_id;
		return $return;
	}
	public static function entity_id($hook, $type, $return, $params) {
		$entity = $params['entity'];
		if ($entity->atom_id)
			return $entity->atom_id;
		return $return;
	}
	public static function notification($hook, $type, $return, $params) {
		// input parameters
		$entry = $params['entry'];
		$subscriber = $params['subscriber'];
		$salmon_link = $params['salmon_link'];

		$federated = new FederatedNotification();
		$federated->load($entry);

		// parse verb
		$verb = $federated->getVerb();

		// parse object type
		$object_type = $federated->getObjectType();

		$target = $federated->getObject();

		// output
		$params = array('notification' => $federated,
				'subscriber' => $subscriber,
				'salmon_link' => $salmon_link,
				'entry' => $entry);
		trigger_plugin_hook('federated_objects:'.$verb, $object_type, $params);
	}

	// Specific callbacks for river actions
	public static function postLogger($hook, $type, $return, $params) {
		$federated = $params['notification'];
		error_log("action: $hook $type");
	}

	public static function postObjectCreator($hook, $type, $return, $params) {
		$notification = $params['notification'];
		$subscriber = $params['subscriber'];
		$entry = $params['entry'];

		$author = $notification->getAuthor();
		$object = $notification->getObject();
		$target = $notification->getTarget();

		$id = $notification->getID();
		$river_id = FederatedNotification::getRiverID($id);

		if ($river_id || $notification->isLocal()) {
			return;
		}

		$author = FederatedObject::create($author);

		if ($target) {
			$target['entry'] = $entry;
			$target['notification'] = $notification;
			$container = FederatedObject::create($target);
			$object['container_entity'] = $container;
		}

		$object['owner_entity'] = $author;
		$object['entry'] = $entry;
		$object['notification'] = $notification;
		$note = FederatedObject::create($object);
	}

	/**
	 * Load xml
	 */
	public function load($xml) {
		$this->xml = $xml;
	}

	public function getID() {
		return @current($this->xml->xpath("atom:id"));
	}

	public function getVerb() {
		$entry = $this->xml;
		// parse verb
		$verbs = $entry->xpath("activity:verb");
		$verb = $verbs?trim(array_pop($verbs)):false;
		if (!$verb) {
				$verb = 'post';
		}
		return $verb;
	}
	public function getBody() {
		$entry = $this->xml;
		$body = @current($entry->xpath("activity:object/atom:content"));
		$body = elgg_strip_tags($body);
		if (empty($body)) {
			$body = $entry->xpath("atom:content");
			if (is_array($body))
				$body = @current($body);
			if ($body)
				$body = $body->asXML();
		}
		return $body;
	}
	
	public function getParentGUID() {
		$entry = $this->xml;
		$parent_id = @current($entry->xpath("activity:object/thr:in-reply-to/atom:id"));
		if ($parent_id) {
			$parent = FederatedObject::find($parent_id);
			$parent_guid = $parent->getGUID();
		}
		return $parent_guid;
	}

	public function getUpdated() {
		return @current($this->xml->xpath("atom:updated"));
	}

	public function getPublished() {
		return @current($this->xml->xpath("atom:published"));
	}

	public function getObjectType() {
		$entry = $this->xml;
		// parse object type
		$object_type = @trim(current($entry->xpath("activity:object/activity:object-type")));
		if (!$object_type) {
			$object_type = 'note';
		}
		return $object_type;
	}

	public function xpath($paths, $default=NULL) {
		$entry = $this->xml;
		foreach($paths as $path) {
			$result =  @current($entry->xpath($path));
			if ($result) {
				return trim($result);
			}
		}
		return $default;
	}

	public function getAuthor() {
		if (!isset($this->author)) {
			$entry = $this->xml;
			// author name
			$name = $this->xpath(array("atom:author/atom:name", "//atom:author/atom:name"));
			// subject
			$id = $this->xpath(array("atom:author/atom:id", "//atom:author/atom:id", "//atom:author/atom:uri"));
			$link = $this->xpath(array("atom:author/atom:link[attribute::rel='alternate']/@href",
						   "atom:link[attribute::rel='alternate']/@href",
						   "//activity:subject/atom:link[attribute::rel='alternate']/@href"
						), $id);
			$icon= $this->xpath(array("//atom:author/atom:link[attribute::rel='preview']/@href",
						  "activity:subject/atom:link[attribute::media:width='48']/@href",
						  "//activity:subject/atom:link[attribute::media:width='48']/@href"));
			$this->author = array('name' => $name,
				     'id' => $id,
				     'entry' => $entry,
				     'type' => 'person',
				     'link' => $link,
				     'icon' => $icon);
		}
		return $this->author;
	}

	public function getTarget() {
		if (!isset($this->target)) {
			$entry = $this->xml;
			 // container
			$id = @current($entry->xpath("activity:target/atom:id"));
			$type = @current($entry->xpath("activity:target/activity:object-type"));
			$icon = @current($entry->xpath("activity:target/atom:link[attribute::rel='preview']/@href"));
			$name = @current($entry->xpath("activity:target/atom:title"));
			$link = @current($entry->xpath("activity:target/atom:link[attribute::rel='alternate']/@href"));
			if (empty($id) && empty($type)) {
				return;
			}
			$this->target = array('id' => $id,
				     'name' => $name,
				     'entry' => $entry,
				     'icon' => $icon,
				     'link' => $link,
				     'type' => trim($type),
				);
		}
		return $this->target;
	}
	
	public function getObject() {
		if (!isset($this->object)) {
			$entry = $this->xml;
			$name = $this->xpath(array("activity:object/atom:title", "atom:title"));
			$id = $this->xpath(array("activity:object/atom:id", "atom:id"));
			$icon = @current($entry->xpath("activity:object/atom:link[attribute::rel='preview']/@href"));
			$link = @current($entry->xpath("activity:object/atom:link[attribute::rel='alternate']/@href"));
			$type = $this->getObjectType();
			$this->object = array('id' => $id,
				     'name' => $name,
				     'entry' => $entry,
				     'icon' => $icon,
				     'link' => $link,
				     'type' => trim($type));
		}
		return $this->object;
	}

	public function isLocal() {
		$id = $this->getID();

		if (FederatedObject::isLocalID($id)) {
			return true;
		}
		return false;
	}
}
