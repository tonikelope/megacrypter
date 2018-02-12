![Alt text](/public/images/lock.png?raw=true "MC logo")![Alt text](/public/images/logo.png?raw=true "MC logo")

![Diagrama](/public/images/diagrama.png?raw=true)
##What do you need to deploy your own Megacrypter?

1. Apache (mod_rewrite ON) (Or another web server that supports URL rewrite)

2. PHP >= 5.5 (cURL + mcrypt + memcache + mbstring)

3. MySQL (optional for blacklist).

###5 steps installation instructions:

Step 1: download tarball (or clone repo) and upload to your server.

Step 2: install composer dependencies -> <code>$ php composer.phar install</code>

Step 3: rename ALL /config .sample extension and edit miscellaneous.php and any other file you need.

Step 4: prepare Apache virtual host:

```
<VirtualHost *:80>
  Servername megacrypter.mydomain
  DocumentRoot /var/www/megacrypter/public
  RewriteEngine On
  <directory /var/www/megacrypter/public>
    AllowOverride None
    Include /var/www/megacrypter/public/.htaccess
  </directory> 
</VirtualHost>
```

Step 5: ask developers of your favourite download manager to recognize your new megacrypter links (or give it a try to [Megabasterd](https://github.com/tonikelope/megabasterd) that supports any MC clon)

You're alone from here. Good luck!

##API DOC

```
API URL -> http(s)://[BASE_URL]/api
(Content-Type: application/json)
```

###Protecting MEGA links
####Request:
```
{"m": "crypt", 
"links": ["MEGA_LINK_1", "MEGA_LINK_2" ... "MEGA_LINK_N"],
*"expire": 0-6,
*"no_expire_token": true OR false,
*"tiny_url": true OR false,
*"app_finfo": true OR false,
*"hide_name": true OR false,
*"pass": "PASS",
*"referer": "DOMAIN_NAME",
*"extra_info": "EXTRA_INFO",
*"email": "EMAIL"}
```
#####*Optional params:
1. Expiration values: 0 -> never (default), 1 -> 10 minutes, 2 -> 1 hour...
2. True by default.
3. Tiny url option is false by default.
4. Append file info option is false by default.
5. Hide name option is false by default.
6. Passwords are case-sensitive.
7. Referer is not required to include 'http://'. It's limited to 256 chars
8. Extra-info is limited to 256 chars.
9. Email is limited to 256 chars.
Note: link list is limited to 500

####Response:
```
{"links": ["MC_LINK_1", "MC_LINK_2" ... "MC_LINK_N"]}
```

###Retrieving link information
####Request:
```
{"m": "info", 
"link": "MC_LINK",
*"reverse": "port:b64_proxy_auth[:host]"}
```
#####*Optional params:
1. Reverse query: Megacrypter will connect to MEGA API using HTTPS proxy running on the client. Client must send port and 'user:password' (base64 encoded) for proxy auth (host is optional).

####Response:
```
{"name": "FILE_NAME" OR "CRYPTED_FILE_NAME", 
"path": false OR "PATH" OR "CRYPTED_FILE_PATH",
"size": FILE_SIZE, 
"key": "FILE_KEY" OR "CRYPTED_FILE_KEY",
"extra": false OR "EXTRA_INFO" OR "CRYPTED_EXTRA_INFO",
"expire": false OR "EXPIRE_TIMESTAMP#NOEXPIRE_TOKEN",
"pass": false OR "ITER_LOG2#KCV#SALT#IV"}
```
#####About password protected files: 

File name, file key, and extra-info will be returned crypted using AES CBC (PKCS7) with 256 bits key derivated from pass (PBKDF2 SHA256).

Follow this algorithm to decrypt crypted fields:

```
REPEAT
        
    password := read_password()
    
    info_key := hmac := hmac_sha256(password, base64_dec(SALT) + hex2bin('00000001'))
    
    FOR i=2 : 1 : pow(2, ITER_LOG2)
        
        hmac := hmac_sha256(password, hmac)
    
        info_key := info_key XOR hmac
    
    END

UNTIL aes_cbc_dec(base64_dec(KCV), info_key, base64_dec(IV)) = info_key

crypted_field := aes_cbc_dec(base64_dec(CRYPTED_FIELD), info_key, base64_dec(IV))
```

###Getting a temporary download url to the (crypted) file
####Request:
```
{"m": "dl", 
"link": "MC_LINK",
*"ssl": true OR false,
*"noexpire": "NOEXPIRE_TOKEN",
*"sid" : "MEGA_SID",
*"reverse": "port:b64_proxy_auth[:host]"}
```
#####*Optional params:
1. Default is false (better performance in slow machines)
2. If link has expiration time you can use NOEXPIRE_TOKEN (cached from a previous "info-request") to bypass it and get the download url.
3. MEGA SESSION ID (for download MegaCrypter link using your MEGA PRO ACCOUNT)
4. Reverse query: Megacrypter will connect to MEGA API using HTTPS proxy running on the client. Client must send port and 'user:password' (base64 encoded) for proxy auth (host is optional).

####Response:
```
{"url": "MEGA_TEMP_URL" OR "CRYPTED_MEGA_TEMP_URL",
"pass": false OR "IV"}
```

Note: use the same algorithm described above to decrypt temp url (if password protected)


###Error responses (because shit happens...)
```
{"error": ERROR_CODE}
```

####Error codes:
```
MC_EMETHOD(1)
MC_EREQ(2)
MC_ETOOMUCHLINKS(3)
MC_ENOLINKS(4)
MC_INTERNAL_ERROR(21)
MC_LINK_ERROR(22)
MC_BLACKLISTED_LINK(23)
MC_EXPIRED_LINK(24)
MEGA_EINTERNAL(-1)
MEGA_EARGS(-2)
MEGA_EAGAIN(-3)
MEGA_ERATELIMIT(-4)
MEGA_EFAILED(-5)
MEGA_ETOOMANY(-6)
MEGA_ERANGE(-7)
MEGA_EEXPIRED(-8)
MEGA_ENOENT(-9)
MEGA_ECIRCULAR(-10)
MEGA_EACCESS(-11)
MEGA_EEXIST(-12)
MEGA_EINCOMPLETE(-13)
MEGA_EKEY(-14)
MEGA_ESID(-15)
MEGA_EBLOCKED(-16)
MEGA_EOVERQUOTA(-17)
MEGA_ETEMPUNAVAIL(-18)
MEGA_ETOOMANYCONNECTIONS(-19)
MEGA_EWRITE(-20)
MEGA_EREAD(-21)
MEGA_EAPPKEY(-22)
MEGA_EDLURL(-101)
```
