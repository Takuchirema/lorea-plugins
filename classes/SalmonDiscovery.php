<?php

class SalmonDiscovery
{
	static function getSalmonEndpointEntity($ent) {
		if (!empty($ent->salmon_link)) {
			return $ent->salmon_link;
		}
		if (strpos($ent->atom_link, 'http') === 0) {
			if ($ent instanceof ElggUser || $ent instanceof ElggGroup)
				$endpoint = SalmonDiscovery::getSalmonEndpoint($ent->atom_link);
			else
				$endpoint = SalmonDiscovery::getSalmonEndpoint(get_entity($ent->container_guid)->atom_link);
			$ent->salmon_link = $endpoint;
		}

		return $endpoint;
	}

	static function getPersonalXrds($uri) {
	    require_once 'Auth/Yadis/Yadis.php';
	    $fetcher = Auth_Yadis_Yadis::getHTTPFetcher();

	    if (strpos($uri, '@')) {
		$uri = 'acct:' . $uri;
		$uri_parts = explode('@', $uri);
		$hostbase = 'http://' . $uri_parts[1];
	    } else {
		$hostparts = parse_url($uri);
		$hostbase = $hostparts['scheme'] . "://" . $hostparts['host'];
	    }


	    $hostmeta = $hostbase . '/.well-known/host-meta';
	    $response = Auth_Yadis_Yadis::discover($hostmeta, $fetcher);
	    if ($response->isXRDS() || !empty($response)) {
		$xrds = new SimpleXMLElement($response->response_text,null,false,'xrd');
		$xrds->registerXPathNamespace('xrd', 'http://docs.oasis-open.org/ns/xri/xrd-1.0');
		$lrdd = @current($xrds->xpath("//xrd:Link[attribute::rel='lrdd']/@template"));
		if (!empty($lrdd)) {
			$uri = str_replace('{uri}', urlencode($uri), $lrdd);
			return $uri;
		}
	    }
	    return $uri;
	}

	static function getYadisEndpoint($webid, $pattern) {
	    require_once 'Auth/Yadis/Yadis.php';
	    $fetcher = Auth_Yadis_Yadis::getHTTPFetcher();

	    $xrds_url = SalmonDiscovery::getPersonalXrds($webid);

	    $response = Auth_Yadis_Yadis::discover($xrds_url, $fetcher);
	    $endpoint = false;
	    if ($response->isXRDS() || !empty($response)) {
			error_log("XRDS:".$response->response_text);
			$xrds = new SimpleXMLElement($response->response_text,null,false,'xrd');
			if ($response->isXRDS()) {
				$xrds->registerXPathNamespace('xrds', 'xri://$xrds');
				$xrds->registerXPathNamespace('xrd', 'xri://$xrd*($v*2.0)');
			}
			else {
				// statusnet way
				$xrds->registerXPathNamespace('xrd', 'http://docs.oasis-open.org/ns/xri/xrd-1.0');
			}
			$endpoints = $xrds->xpath($pattern);
			foreach($endpoints as $link) {
				$endpoint = $link;
			}
	    }
	    return $endpoint;
	}

	static function getSalmonEndpoint($webid) {
		return SalmonDiscovery::getYadisEndpoint($webid,
			"//xrd:Link[attribute::rel='http://salmon-protocol.org/ns/salmon-mention']/@href");
	}

	static function getRemoteKey($uri, $sig_hash) {
	    require_once 'Auth/Yadis/Yadis.php';
	    $fetcher = Auth_Yadis_Yadis::getHTTPFetcher();

	    $uri = SalmonDiscovery::getPersonalXrds($uri);
	    $response = Auth_Yadis_Yadis::discover($uri, $fetcher);
	    if ($response->isXRDS() || !empty($response)) {
			$xrds = new SimpleXMLElement($response->response_text,null,false,'xrd');
			if ($response->isXRDS()) {
				$xrds->registerXPathNamespace('xrds', 'xri://$xrds');
				$xrds->registerXPathNamespace('xrd', 'xri://$xrd*($v*2.0)');
			}
			else {
				// statusnet way
				$xrds->registerXPathNamespace('xrd', 'http://docs.oasis-open.org/ns/xri/xrd-1.0');
			}
			$certs = $xrds->xpath("//xrd:Link[attribute::rel='magic-public-key']/@href");
			foreach($certs as $cert) {
				if (strpos($cert, 'data:') === 0) {
					// inline data!!!
					$text = preg_replace('/\s+/', '', $cert);
					if (!preg_match('/RSA\.([^\.]+)\.([^\.]+)(.([^\.]+))?/', $text, $matches)) {
						return false;
					}
					$mod = Base64url::decode($matches[1]);
					$exp = Base64url::decode($matches[2]);
					$key = SalmonProtocol::convertRSA($mod, $exp);
					if ($key && empty($sig_hash)) {
						// is this allowed??????
						return $key;
					}
					if ($key && trim($sig_hash) == trim(hash("sha256", trim($key)))) {
						return $key;
					}


				} else {
					$fetcher = new Auth_Yadis_ParanoidHTTPFetcher();
					$res = $fetcher->get($cert);
					if (trim($sig_hash) == trim(hash("sha256", trim($res->body)))) {
						return $res->body;
					}
				}
			}
	    }
	}


}
