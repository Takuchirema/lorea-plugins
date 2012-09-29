<?php

class FederatedNotification {
	public static function notification($hook, $type, $return, $params) {
		// input parameters
		$entry = $params['entry'];
		$salmon_link = $params['salmon_link'];
		$provenance = $params['provenance']; // coming from salmon

		$federated = new FederatedNotification();
		$federated->load($entry, $provenance);
		if (!$federated->valid) {
			error_log("Invalid provenance");
			return;
		}

		// parse verb
		$verb = $federated->getVerb();

		// parse object type
		$object_type = $federated->getObjectType();

		$target = $federated->getObject();

		// output
		$params = array('notification' => $federated,
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
		$entry = $params['entry'];

		$author = $notification->getAuthor();
		$object = $notification->getObject();
		$target = $notification->getTarget();

		$id = $notification->getID();
		$river_id = AtomRiverMapper::getRiverID($id);

		if ($river_id || $notification->isLocal()) {
			return;
		}

		$author = FederatedObject::create($author, 'atom:author');

		if ($target) {
			$container = FederatedObject::create($target, 'activity:target');
			$object['container_entity'] = $container;
		}

		$object['owner_entity'] = $author;
		$note = FederatedObject::create($object, 'activity:object');
	}

	/**
	 * Load xml
	 */
	public function load($xml, $provenance) {
		$xml->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
		$xml->registerXPathNamespace('activity', 'http://activitystrea.ms/spec/1.0/');
		$xml->registerXPathNamespace('me', 'http://salmon-protocol.org/ns/magic-env');
		$xml->registerXPathNamespace('media','http://purl.org/syndication/atommedia');
		$provenance_xml = @current($xml->xpath("me:provenance"));

		if ($provenance_xml) {
			$env = SalmonEnvelope($provenance_xml->asXml());
			if ($env->valid) {
				$this->xml = $env->data;
				$this->provenance = $provenance_xml;
			}
			else {
				$this->valid = false;
				return;
			}
		}
		else {
			$this->xml = $xml;
			$this->provenance = $provenance;
		}
		$this->valid = true;
	}

	public function getID() {
		return @current($this->xml->xpath("atom:id"));
	}

	public function getVerb() {
		$entry = $this->xml;
		// parse verb
		$verbs = $entry->xpath("activity:verb");
		$verb = $verbs?trim(array_pop($verbs)):false;
		$verb = str_replace("http://activitystrea.ms/schema/1.0/", "", $verb);
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
				$body = elgg_strip_tags($body->asXML());
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
		$object_type = str_replace('http://activitystrea.ms/schema/1.0/', '', $object_type);
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
			$link = $id;
			/*$link = $this->xpath(array("atom:author/atom:link[attribute::rel='alternate']/@href",
						   "//atom:author/atom:link[attribute::rel='alternate']/@href",
						   "//activity:subject/atom:link[attribute::rel='alternate']/@href"
						), $id);*/
			$icon= $this->xpath(array("//atom:author/atom:link[attribute::rel='preview']/@href",
						  "activity:subject/atom:link[attribute::media:width='48']/@href",
						  "//activity:subject/atom:link[attribute::media:width='48']/@href"));
			$this->author = array('name' => $name,
				     'id' => $id,
				     'entry' => $entry,
				     'notification' => $this,
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
			$type = str_replace('http://activitystrea.ms/schema/1.0/', '', $type);
			$icon = @current($entry->xpath("activity:target/atom:link[attribute::rel='preview']/@href"));
			$name = @current($entry->xpath("activity:target/atom:title"));
			$link = @current($entry->xpath("activity:target/atom:link[attribute::rel='alternate']/@href"));
			$tags = $entry->xpath("activity:target/atom:category/@term");
			if ($tags)
				$tags = string_to_tag_array(implode(", ", $tags));
			if (empty($id) && empty($type)) {
				return;
			}
			$this->target = array('id' => $id,
				     'name' => $name,
				     'entry' => $entry,
				     'icon' => $icon,
				     'notification' => $this,
				     'link' => $link,
				     'tags' => $tags,
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
			$tags = $entry->xpath("activity:object/atom:category/@term");
			if ($tags)
				$tags = string_to_tag_array(implode(", ", $tags));
			$type = $this->getObjectType();
			$this->object = array('id' => $id,
				     'name' => $name,
				     'entry' => $entry,
				     'notification' => $this,
				     'icon' => $icon,
				     'link' => $link,
				     'tags' => $tags,
				     'type' => trim($type));
		}
		return $this->object;
	}

	public function getSalmonEndpoint() {
		return $this->xpath(array("//atom:link[attribute::rel='http://salmon-protocol.org/ns/salmon-replies']/@href"));
	}

	public function getHub() {
		return $this->xpath(array("//atom:link[attribute::rel='hub']/@href"));
	}

	public function getIcon($tag="atom:author") {
		$icons = $this->getIcons($tag);
		if (count($icons)) {
			$largest = max(array_keys($icons));
			return $icons[$largest];
		}
		return;
	}

	public function getIcons($tag="atom:author") {
		$icons = $this->xml->xpath("//$tag/atom:link[attribute::rel='avatar']");
		$result = array();
		foreach($icons as $icon) {
			$attrs = $icon->attributes('http://purl.org/syndication/atommedia');
			$result[(int)$attrs['width']] = (string)$icon['href'];
		}
		return $result;
	}

	public function isLocal() {
		$id = $this->getID();

		if (FederatedObject::isLocalID($id) && empty($this->provenance)) {
			return true;
		}
		return false;
	}
}
