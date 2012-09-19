<?php

class FederatedNotification {

	/**
	 * Load xml
	 */
	public function load($xml) {
		$this->xml = $xml;
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
			$this->target = array('id' => $id,
				     'name' => $name,
				     'entry' => $entry,
				     'icon' => $icon,
				     'link' => $link,
				     'type' => $type,
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
				     'type' => $type);
		}
		return $this->object;
	}

}
