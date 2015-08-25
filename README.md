![Alt text](/public/images/lock.png?raw=true "MC logo")![Alt text](/public/images/logo.png?raw=true "MC logo")
megacrypter
===========

What do you need?

1) Apache (mod_rewrite + allowoverride)

2) PHP >= 5.5 (cURL + mcrypt + memcache + mbstring)

3) MySQL (optional for blacklist).

Instructions:

Step 1: download tarball (or clone repo) and upload to your server.

Step 2: install composer dependencies -> <code>$ php composer.phar install</code>

Step 3: remove ALL /config .sample extension and edit miscellaneous.php and any other file you need.

Step 4: prepare virtual host (set document root point to megacrypter public dir) and restart Apache.

Step 5 (optional): ask  developers of your favourite download manager to recognize your new megacrypter links.

You're alone from here. Good luck!
