![Alt text](/public/images/lock.png?raw=true "MC logo")![Alt text](/public/images/logo.png?raw=true "MC logo")
megacrypter
===========

What do you need?

1) Apache (mod_rewrite + allowoverride)

2) PHP 5.4 (cURL + mcrypt + memcache)

3) MySQL (optional for blacklist).

Instructions:

Step 1: upload megacrypter to your server.

Step 2: prepare virtual host (set document root point to megacrypter public dir)

Step 3: php composer.phar install

Step 4: Remove ALL /config .sample extension and edit (miscellaneous.php, database.php and gmail.php).

You're alone from here. Good luck!
