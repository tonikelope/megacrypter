#!/usr/bin/php

<?php

	define('GENERIC_PASSWORD', ''); //Usar misma contraseña de application/config

	echo "\n".base64_encode("{$argv[1]}#".hash_hmac('sha1', $argv[1], base64_decode(GENERIC_PASSWORD)))."\n";

?>
