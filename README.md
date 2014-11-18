megacrypter
===========

What do you need?


1) Apache (vhost + mod_rewrite + allowoverride)

2) PHP 5.5 (cURL + mcrypt + memcache + composer)

3) MySQL (optional for blacklist).

4) Edit application/config files with 

4.1) miscellaneous.php

-Your custom URL_BASE
-Your custom AES-256 key
-Your custom generic password 

4.2) memcache.php

4.3) database.php (optional)

4.4) gmail.php (optional)


From here you go alone. Good luck!
