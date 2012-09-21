<?php
class Base64url {
	function decode($base64url)
	{
		$base64 = strtr($base64url, '-_', '+/');
		$plainText = base64_decode($base64);
		return ($text);
	}
	function encode($text)
	{
		$base64 = base64_encode($text);
		$base64url = strtr($base64, '+/', '-_');
		return ($base64url);
	}
}
