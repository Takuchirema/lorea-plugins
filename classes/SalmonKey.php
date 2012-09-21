<?php
class SalmonKey {
        function __construct($entity) {
		$this->entity = $entity;
		$this->key = null;
	}
	function __destruct() {
		if ($this->key) {
			openssl_pkey_free($this->key);
			unset($this->key);
		}
	}
	public function getKey() {
		$entity = $this->entity;
		if ($this->key)
			return $this->key;
		if ($entity->salmonkey) {
                        $res = openssl_get_privatekey($entity->salmonkey);
                }
		else {
                        $res = openssl_pkey_new ();
                        openssl_pkey_export($res, $privkey);
                        $ignored = elgg_set_ignore_access(true);
                        $entity->salmonkey = $privkey;
                        elgg_set_ignore_access($ignored);
                }
		$this->key = $res;
		return $res;
	}
	public function echoKey() {
		$key = $this->getKey();
		$key_details = openssl_pkey_get_details($key);
		$key_data = $key_details["key"];
		return $key_data;
	}
	public function echoKeyUrl() {
                $key = $this->getKey();

                $key_details = openssl_pkey_get_details($key);
		$mod = $key_details['rsa']['n'];
		$exp = $key_details['rsa']['e'];

		$text = "data:application/magic-public-key,RSA.".Base64url::encode($mod).".".Base64url::encode($exp);
                return $text;
	}
	public function sign($text) {
		$key = $this->getKey();
		openssl_sign($text, $signature, $key, "sha256");
		return Base64url::encode($signature);
	}
	public function getHash() {
		$key = $this->getKey();
		$key_details = openssl_pkey_get_details($key);
		$pubkey = $key_details["key"];
		return Base64url::encode(hash('sha256', trim($pubkey)));
	}
}
