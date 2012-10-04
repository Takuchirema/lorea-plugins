#! /usr/bin/php
<?php
// only allow running from cli
if (PHP_SAPI != 'cli') {
	exit();
}

// get command parameters
$size = $argv[1];
$user = $argv[2];
$sender = $argv[3];
$network = $argv[4];
$secret = $argv[5];

// get stdinput
$data = "";
$stdin = fopen("php://stdin", "rb");
while(strlen($data) < $size) {
	$data .= stream_get_contents($stdin);
	//$data .= fread($stdin, $size - strlen($data));
}
fclose($stdin);

// send post to network
$post_fields = array(
		'sender' => $sender,
		'user' => $user,
		'secret' => $secret,
		'data' => $data,
);
$request = curl_init($network . 'elggman/receive');

curl_setopt($request, CURLOPT_POST, TRUE);
curl_setopt($request, CURLOPT_POSTFIELDS, $post_fields);
curl_setopt ($request, CURLOPT_FOLLOWLOCATION, 1);

$data = curl_exec($request);
curl_close($request);

