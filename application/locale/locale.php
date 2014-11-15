<?php

define('TEXT_DOMAIN', 'MegaCrypter');

$LOCALES = [
    'es' => 'es_ES',
    'fr' => 'fr_FR'
];

/* This is for Cloudflare...  */
if(array_key_exists(($l=strtolower($_SERVER['HTTP_CF_IPCOUNTRY'])), $LOCALES)) {
    
    $locale = $LOCALES[$l];
    define('HTML_LANG', $l);
    
} else {
    
    define('HTML_LANG', 'en');
}

if(isset($locale))
{
    setlocale(LC_MESSAGES, "{$locale}.utf8");
    bindtextdomain(TEXT_DOMAIN, APP_PATH.'locale/');
    textdomain(TEXT_DOMAIN);
    bind_textdomain_codeset(TEXT_DOMAIN, 'UTF-8');
}