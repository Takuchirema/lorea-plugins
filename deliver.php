#! /usr/bin/php
<?php
// only allow running from cli
if (PHP_SAPI != 'cli') {
	exit();
}

// get command parameters
$user = $argv[1];
$sender = $argv[2];
$network = $argv[3];
$secret = $argv[4];

// get stdinput
$stdin = fopen("php://stdin", "r");
$data = stream_get_contents($stdin);
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

