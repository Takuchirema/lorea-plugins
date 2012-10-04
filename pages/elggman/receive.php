<?php

elgg_load_library('elggman');

$sender = get_input('sender');
$user = get_input('user');
$secret = get_input('secret');
$data = get_input('data');

incoming_mail($sender, $user, $data, $secret);
