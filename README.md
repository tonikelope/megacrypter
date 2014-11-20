megacrypter
===========

What do you need?


1) Apache (vhost + mod_rewrite + allowoverride)

2) PHP 5.4 (cURL + mcrypt + memcache + composer)

3) MySQL (optional for blacklist).

4) Edit application/config files: 

4.1) miscellaneous.php

4.2) memcache.php

4.3) database.php

4.4) gmail.php

Step 1: upload megacrypter to your server.

Step 2: prepare virtual host (set document root point to megacrypter public dir)

Step 3: php composer install (in megacrypter dir)

Step 4: edit config files

You're alone from here. Good luck!
