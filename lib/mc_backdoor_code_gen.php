#!/usr/bin/php

<?php

	if($argc>=3)
		echo "\n{$argv[1]}!".hash_hmac('sha1', preg_replace('/^.*(![^!]+![0-9a-f]+).*$/i', '\1', trim($argv[1])), base64_decode($argv[2]))."\n\n";
	else
		echo "\n{$argv[0]} mc_link hmac_key\n\n";

?>
