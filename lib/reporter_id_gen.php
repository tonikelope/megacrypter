#!/usr/bin/php

<?php

	if($argc>=3)
		echo "\n".base64_encode("{$argv[1]}#".hash_hmac('sha1', $argv[1], $argv[2]))."\n";
	else
		echo "\n{$argv[0]} reporter_email hmac_key\n\n";

?>
