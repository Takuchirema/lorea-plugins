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
			$id = @current($xml->xpath("//atom:author/atom:uri"));

		if (!$key) {
			$key = SalmonDiscovery::getRemoteKey($id, $sig_hash);
		}
		if (SalmonProtocol::checkSignature($b64data, $sig, $key)) {
			$this->valid = true;
		}
		else {
			//error_log("doesnt validate");
		}
		return null;

	}

	function apply($entity=null) {
		$magicenv_raw = $this->raw;
		$magicenv = $this->xml;
		$magicenv->registerXPathNamespace('me', 'http://salmon-protocol.org/ns/magic-env');
		$provenance = @current($magicenv->xpath('//me:provenance'));
		if (empty($provenance) && $magicenv->getName() == 'provenance') {
			// status.net sends like this but seems to like ours too
			$text_provenance = $magicenv_raw;
		}
		elseif($provenance) {
			$text_provenance = $provenance->asXml();
		}
		else {
			error_log("salmon:no provenance!");
		}
		// XXX no salmon_link .. no good!

		$params = array('entry' => $this->data,
				'internal' => true,
				'provenance' => $text_provenance,
				'target_entity' => $entity);
		trigger_plugin_hook('push:notification', 'atom', $params);
	}


}
