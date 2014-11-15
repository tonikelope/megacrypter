megacrypter
===========

1) Apache with mod_rewrite + allowoverride

2) PHP 5.5 (cURL + mcrypt + memcache)

3) MySQL (optional for blacklist).

And...

<VirtualHost *:80>

Servername megacrypter.yourdomain.com

DocumentRoot /var/www/megacrypter.com/public

RewriteEngine On

</VirtualHost>

Also you have to edit app/config files

GL!
