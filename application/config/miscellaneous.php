<?php

    /* Note: always BASE64 pass!! */

    define('URL_BASE', 'http://megacrypter.yourdomain.com'); 
    define('MASTER_KEY', 'e6f1cb17b81089f6846a93ca6771c13a7e366849e44e239d38918b5c2f9e46c4'); //YOU MUST GENERATE YOUR OWN AES KEY
    define('GENERIC_PASSWORD', 'CgCv8QmhXjbcmdhNFvPuUw=='); //IDEM AS AES KEY
    define('GOOGLE_URL_SHORT_API_KEY', ''); //Required for tiny urls
    define('MEGA_API_KEY', ''); //Not used for now
    define('API_VERSION', '0.7');
    define('STOP_IT_ALL', false);
    define('WEB_MAINTENANCE', false);
    define('ERROR_LOG', false);
    define('TAKEDOWN_TOOL', false);
    define('BLACKLIST_LEVEL', Utils_MegaCrypter::BLACKLIST_LEVEL_OFF); //Blacklist links
    define('BLACKLIST_MEMCACHE_PREFIX', 'BLACKLIST_');
    define('ZOMBIE_LINKS', false);
    define('CURL_USERAGENT', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/537.36');
    define('_CURLPROXY_SOCKS5_HOSTNAME', 7);
    define('TOR_PROXY_SOCKS', '127.0.0.1:9050');
    
    /* Required for TAKEDOWN TOOL */
    define('RECAPTCHA_PUBLIC_KEY', ''); 
    define('RECAPTCHA_PRIVATE_KEY', '');
