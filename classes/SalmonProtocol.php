<?php

class SalmonProtocol {
	static function request($page) {
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$target_entity = null;
			if (count($page) && $page[0] === "endpoint") {
				$target_id = $page[1];
			}
			$raw = file_get_contents('php://input');

			$envelope = new SalmonEnvelope($raw);
			if ($envelope->valid) {
				$envelope->apply($target_id);
			}
		}
	}
	function sendUpdate($salmon_link, $item, $object, $subject) {
		if (strpos($salmon_link, 'http') !== 0) {
			return;
		}

		$viewtype = elgg_get_viewtype();
		elgg_set_viewtype('atom');
		$update = elgg_view('river/item',
				    array('item' => $item,
					// XXX what is standalone?
					  'standalone' => true),
				    false,
				    false,
				    'atom');
		$update = "<entry xmlns='http://www.w3.org/2005/Atom' xmlns:thr='http://purl.org/syndication/thread/1.0' xmlns:georss='http://www.georss.org/georss' xmlns:activity='http://activitystrea.ms/spec/1.0/' xmlns:media='http://purl.org/syndication/atommedia'>$update</entry>";
		elgg_set_viewtype($viewtype);
		SalmonProtocol::postEnvelope($salmon_link, $update, $subject);
	}

	static function postEnvelope($salmon_link, $update, $subject) {
		if (!$salmon_link)
			return;

		// create envelope
		$key = new SalmonKey($subject);
		$encoded_update = Base64url::encode($update);
		$postdata = '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$postdata .= '<me:provenance xmlns:me="http://salmon-protocol.org/ns/magic-env">'."\n";
		$postdata .= "<me:data type='application/atom+xml'>";
		$postdata .= $encoded_update;
		$postdata .= "</me:data>
	    <me:encoding>base64url</me:encoding>
	    <me:alg>RSA-SHA256</me:alg>
	    <me:sig keyhash='".$key->getHash()."'>";

		// prepare data for signing
		$data_type = Base64url::encode("application/atom+xml");
		$encoding = Base64url::encode("base64url");
		$alg = Base64url::encode("RSA-SHA256");
		$signed = "$encoded_update.$data_type.$encoding.$alg";

		$postdata .= $key->sign($signed);
		$postdata .= "</me:sig>";
		$postdata .= "</me:provenance>";

		// post using curl
		$ch = curl_init($salmon_link);
		// Expect header to solve Expectation failed problem from server
		// see: http://mattly.me/snippet/2011/09/20/php-curl-and-error-417-expectation-failed/
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/magic-envelope+xml', 'Expect:'));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		$error_n = curl_exec($ch);
		curl_close($ch);
	}

	static function checkSignature($data, $sig, $raw_key) {
		$key = openssl_get_publickey($raw_key);
		$res = openssl_verify($data, $sig, $key, "sha256");
		openssl_free_key($key);
		return ($res === 1);
	}

	static function makeAsnSegment($type, $string) {
		switch ($type){
		    case 0x02:
			if (ord($string) > 0x7f)
			    $string = chr(0).$string;
			break;
		    case 0x03:
			$string = chr(0).$string;
			break;
		}

		$length = strlen($string);

		if ($length < 128){
		   $output = sprintf("%c%c%s", $type, $length, $string);
		} else if ($length < 0x0100){
		   $output = sprintf("%c%c%c%s", $type, 0x81, $length, $string);
		} else if ($length < 0x010000) {
		   $output = sprintf("%c%c%c%c%s", $type, 0x82, $length/0x0100, $length%0x0100, $string);
		} else {
		    $output = NULL;
		}
		return($output);
	}

	static function convertRSA($modulus, $exponent) {
		/* make an ASN publicKeyInfo */
		$exponentEncoding = SalmonProtocol::makeAsnSegment(0x02, $exponent);
		$modulusEncoding = SalmonProtocol::makeAsnSegment(0x02, $modulus);
		$sequenceEncoding = SalmonProtocol::makeAsnSegment(0x30, $modulusEncoding.$exponentEncoding);
		$bitstringEncoding = SalmonProtocol::makeAsnSegment(0x03, $sequenceEncoding);
		$rsaAlgorithmIdentifier = pack("H*", "300D06092A864886F70D0101010500");
		$publicKeyInfo = SalmonProtocol::makeAsnSegment (0x30, $rsaAlgorithmIdentifier.$bitstringEncoding);

		/* encode the publicKeyInfo in base64 and add PEM brackets */
		$publicKeyInfoBase64 = base64_encode($publicKeyInfo);
		$encoding = "-----BEGIN PUBLIC KEY-----\n";
		$offset = 0;
		while ($segment=substr($publicKeyInfoBase64, $offset, 64)){
		   $encoding = $encoding.$segment."\n";
		   $offset += 64;
		}
		return $encoding."-----END PUBLIC KEY-----\n";
	}
}
