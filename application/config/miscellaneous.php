<?php

    define('URL_BASE', 'http://megacrypter.yourdomain.com'); //domain or subdomain is required (API URL is relative to root path) 
    define('MASTER_KEY', 'ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff'); //YOU MUST GENERATE YOUR OWN AES KEY (and keep it) 
    define('GENERIC_PASSWORD', 'CgCv8QmhXjbcmdhNFvPuUw=='); //IDEM AS AES KEY (min recommeded 16 bytes base64-encoded)
    define('GOOGLE_URL_SHORT_API_KEY', ''); //Required for tiny urls
    define('MEGA_API_KEY', ''); //Not used (for now)
    define('API_VERSION', '0.7');
    define('STOP_IT_ALL', false);
    define('WEB_MAINTENANCE', false);
    define('ERROR_LOG', false);
    define('TAKEDOWN_TOOL', false); //Disabled by default
    define('BLACKLIST_LEVEL', Utils_MegaCrypter::BLACKLIST_LEVEL_OFF); //Blacklist check disabled by default
    define('BLACKLIST_MEMCACHE_PREFIX', 'BLACKLIST_');
    define('ZOMBIE_LINKS', false);
    define('CURL_USERAGENT', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/537.36');
    define('_CURLPROXY_SOCKS5_HOSTNAME', 7);
    define('TOR_PROXY_SOCKS', '127.0.0.1:9050');
    
    /* Required for TAKEDOWN TOOL */
    define('RECAPTCHA_PUBLIC_KEY', ''); 
    define('RECAPTCHA_PRIVATE_KEY', '');
