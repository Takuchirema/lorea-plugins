o->xml
<?php
class SalmonEnvelope {
	function __construct($raw) {
		$this->valid = false;
		$this->raw = $raw;
		$this->xml = @ new SimpleXMLElement($raw);
		$this->parseXml($this->xml);

	}
	function parseXml($salmon_xml, $key=false) {
		$salmon_xml->registerXPathNamespace('me', 'http://salmon-protocol.org/ns/magic-env');
		$b64data = @current($salmon_xml->xpath('//me:data'));
		$data = Base64url::decode($b64data);
		$data_type = @current($salmon_xml->xpath('//me:data/@type'));
		$encoding = @current($salmon_xml->xpath('//me:encoding'));
		$alg = @current($salmon_xml->xpath('//me:alg'));
		$enc_data = $b64data.".".Base64url::encode($data_type).".".Base64url::encode($encoding).".".Base64url::encode($alg);
		$sig_hash = Base64url::decode(@current($salmon_xml->xpath('//me:sig/@keyhash')));
		$sig = Base64url::decode(@current($salmon_xml->xpath('//me:sig')));
		$xml = @ new SimpleXMLElement($data, null, false, "atom");
		$this->data = $xml;
		$xml->registerXPathNamespace('thr', 'http://purl.org/syndication/thread/1.0');
		$xml->registerXPathNamespace('activity', 'http://activitystrea.ms/spec/1.0/');
		$xml->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
		//$id = @current($xml->xpath('//thr:in-reply-to/@ref'));
		foreach(array("atom:author/atom:link[attribute::rel='alternate']/@href",
                                                   "atom:link[attribute::rel='alternate']/@href",
                                                   "//activity:subject/atom:link[attribute::rel='alternate']/@href"
                                                ) as $xpath) {
			$id = @current($xml->xpath($xpath));
			if ($id) {
				break;
			}
		}
		if (!$id)
			$id = @current($xml->xpath("atom:author/atom:uri"));
		if (!$id)
			$id = @current($xml->xpath("/atom:feed/atom:author/atom:uri"));
		if (!$id)
			$id = @current($xml->xpath("atom:author/atom:id"));
		if (!$id)
			$id = @current($xml->xpath("/atom:feed/atom:author/atom:id"));
		if (!$key) {
			$key = SalmonDiscovery::getRemoteKey($id, $sig_hash);
		}
		if (SalmonProtocol::checkSignature($enc_data, $sig, $key)) {
			$this->valid = true;
		}
		elseif (SalmonProtocol::checkSignature($b64data, $sig, $key)) {
			// old style..
			$this->valid = true;
		}
		return null;

	}

	function apply($entity_guid=null) {
		if ($entity_guid) {
			$entity = get_entity($entity_guid);
		}
		$magicenv_raw = $this->raw;
		$magicenv = $this->xml;

		$provenance = @current($magicenv->xpath('//me:provenance'));

		if (empty($provenance) && $magicenv->getName() == 'provenance') {
			// echo without <?xml part
			$roots = $magicenv->xpath('/me:provenance/*');
			// status.net sends like this but seems to like ours too
			$text_provenance = '<me:provenance xmlns:me="http://salmon-protocol.org/ns/magic-env">';
			foreach($roots as $root) {
				$text_provenance .= $root->asXml();
			}
			$text_provenance .= '</me:provenance>';
		}
		elseif($provenance) {
			$text_provenance = $provenance->asXml();
		}
		else {
			// can happen if the message is direct salmon 'to target'
			error_log("salmon:no provenance!");
		}
		// XXX no salmon_link .. no good!

		$params = array('entry' => $this->data,
				'internal' => true,
				'provenance' => $text_provenance,
				'target_entity' => $entity);
		elgg_trigger_plugin_hook('push:notification', 'atom', $params);
	}


}
