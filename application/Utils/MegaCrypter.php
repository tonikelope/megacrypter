<?php

class Utils_MegaCrypter
{
    const PBKDF2_ITERATIONS_LOG2 = 14; // [1-256]
    const ZOMBIE_LINK_TTL = 86400;
    const MAX_FILE_NAME_BYTES = 255;
    const CACHE_BLACKLISTED_TTL = 3600;
    const BLACKLIST_LEVEL_OFF = 0;
    const BLACKLIST_LEVEL_MC = 1;
    const BLACKLIST_LEVEL_MEGA = 2;

    /* Inicio códigos de error (los códigos positivos por debajo del 21 están reservados para errores del APIController) */
    const INTERNAL_ERROR = 21;
    const LINK_ERROR = 22;
    const BLACKLISTED_LINK = 23;
    const EXPIRED_LINK = 24;
    /* Fin códigos de error */

    private static function _encryptLink($link, array $options=[]) {
        
        if (preg_match('/^.*?!(?P<file_id>[^!]+)!(?P<file_key>.+)$/', trim($link), $match)) {
            
            $iv = openssl_random_pseudo_bytes(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

            $flags = 0;

            $mask = 0x8000;

            $i=0;

            $optional_data='';

            if(!empty($options)) {

                $available_fields = self::_getOptionalFields();

                foreach($available_fields as $label => $val) {

                    if(array_key_exists($label, $options) && !empty($options[$label])) {

                        $flags|=($mask>>$i);

                        if(!is_null($val)) {

                            $optional_data.=$val['pack']($options[$label]);
                        }
                    }

                    $i++;
                }
            }

            $file_id=$match['file_id'];

            $file_key=Utils_MiscTools::urlBase64Decode($match['file_key']);

            $data=$iv.Utils_CryptTools::aesCbcEncrypt(gzdeflate(pack('C', strlen($file_id)-1) . $file_id . pack('C', strlen($file_key)-1) . $file_key . pack('n', $flags) . $optional_data, 9), hex2bin(MASTER_KEY), $iv);

            $hash = hash_hmac('crc32', $data, md5(hex2bin(MASTER_KEY), true));

            $b64data = Utils_MiscTools::urlBase64Encode($data);

            $url_path = preg_replace('/.{' . self::MAX_FILE_NAME_BYTES . '}(?!$)/', '\0/', "!$b64data!$hash");
            
            $c_link = URL_BASE . "/$url_path";

            return isset($options['tiny_url']) && $options['tiny_url'] ? Utils_MiscTools::deflateUrl($c_link) : $c_link;

        } else {
            throw new Exception_MegaCrypterLinkException(self::LINK_ERROR);
        }
    }

    public static function decryptLink($link, $no_expire=null, $ignore_blacklist=false) {

        if (preg_match('/^.*?!(?P<data>[0-9a-z_-]+)!(?P<hash>[0-9a-f]+)/i', trim(str_replace('/', '', $link)), $match)) {

            if ( ($mc_key=self::_checkLinkHmac($match['data'], $match['hash'])) === false ) {

                if( TRY_LEGACY_LINK_DECRYPT && ($mc_key=self::_checkLegacyLinkHmac($match['data'], $match['hash'])) !== false ) {

                    return self::_decryptLegacyLink($match['data'], $mc_key, $no_expire, $ignore_blacklist);

                } else {

                    throw new Exception_MegaCrypterLinkException(self::LINK_ERROR);
                }

            } else if (!$ignore_blacklist && BLACKLIST_LEVEL >= self::BLACKLIST_LEVEL_MC && self::isBlacklistedLink($match['data'])) {

                throw new Exception_MegaCrypterLinkException(self::BLACKLISTED_LINK);

            } else {

                $iv = substr(($data=Utils_MiscTools::urlBase64Decode($match['data'])), 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

                $dec_data = gzinflate(Utils_CryptTools::aesCbcDecrypt(substr($data, strlen($iv)), hex2bin($mc_key), $iv));

                $file_id = substr($dec_data, 1, unpack('Clength', substr($dec_data, 0, 1) )['length']+1);

                $file_key = substr($dec_data, 1 + strlen($file_id) + 1, unpack('Clength', substr($dec_data, 1 + strlen($file_id), 1) )['length']+1);

                $optional_fields = [];

                if (!$ignore_blacklist && BLACKLIST_LEVEL == self::BLACKLIST_LEVEL_MEGA && self::isBlacklistedLink($file_id)) {

                    throw new Exception_MegaCrypterLinkException(self::BLACKLISTED_LINK);

                } else {

                    $flags = unpack('nflags', substr($dec_data, ($offset=1 + strlen($file_id) + 1 + strlen($file_key)), 2))['flags'];

                    if ($flags !== 0) {

                        $optional_data = substr($dec_data, $offset+2);

                        $offset=0;

                        $available_fields = self::_getOptionalFields();

                        $mask = 0x8000;

                        $i = 0;

                        foreach($available_fields as $label => $val) {

                            if(($mask >> $i) & $flags) {

                                if(!is_null($val)) {

                                    $optional_fields[$label] = $val['unpack']($optional_data, $offset);

                                } else {

                                    $optional_fields[$label] = true;
                                }
                            }

                            $i++;
                        }

                        if(array_key_exists('NOEXPIRETOKEN', $optional_fields)) {

                            $optional_fields['NOEXPIRETOKEN'] = hash_hmac('sha256', $iv, GENERIC_PASSWORD, true);
                        }

                        if( array_key_exists('EXPIRE', $optional_fields) && time() >= $optional_fields['EXPIRE'] && ( !array_key_exists('NOEXPIRETOKEN', $optional_fields) || is_null($no_expire) || !Utils_CryptTools::hash_equals(base64_decode($no_expire), $optional_fields['NOEXPIRETOKEN']) ) ) {
                                throw new Exception_MegaCrypterLinkException(self::EXPIRED_LINK);
                        }

                        if (array_key_exists('ZOMBIE', $optional_fields) && $optional_fields['ZOMBIE'] != $_SERVER['REMOTE_ADDR']) {

                            throw new Exception_MegaCrypterLinkException(self::LINK_ERROR);

                        }
                    }

                    return [
                        'file_id' => $file_id,
                        'file_key' => Utils_MiscTools::urlBase64Encode($file_key),
                        'extra_info' => array_key_exists('EXTRAINFO', $optional_fields)? $optional_fields['EXTRAINFO'] : false,
                        'pass' => array_key_exists('PASSWORD', $optional_fields) ? $optional_fields['PASSWORD'] : false,
                        'hide_name' => array_key_exists('HIDENAME', $optional_fields),
                        'expire' => array_key_exists('EXPIRE', $optional_fields) ? $optional_fields['EXPIRE'] : false,
                        'no_expire_token' => array_key_exists('NOEXPIRETOKEN', $optional_fields)?base64_encode($optional_fields['NOEXPIRETOKEN']):false,
                        'referer' => array_key_exists('REFERER', $optional_fields)? $optional_fields['REFERER'] : false,
                        'email' => array_key_exists('EMAIL', $optional_fields)? $optional_fields['EMAIL'] : false,
                        'zombie' => array_key_exists('ZOMBIE', $optional_fields)? $optional_fields['ZOMBIE'] : false
                    ];
                }
            }
        } else {
            throw new Exception_MegaCrypterLinkException(self::LINK_ERROR);
        }
    }

    /* CAMPOS OPCIONALES DEL ENLACE CIFRADO (MUY IMPORTANTE)

    **********************************************************************************************************************

    Reglas (para garantizar la "retrocompatibilidad" de enlaces):

    1) El número MÁXIMO de campos opcionales es 16.

    2) Los campos NUEVOS tienen que añadirse por el FINAL del array.

    3) NO está permitido ELIMINAR campos.

    4) NO está permitido alterar el ORDEN de los campos.

    5) NO está permitido alterar la LONGITUD MÁXIMA de los campos.


    + Los campos opcionales (no booleanos) tienen que implementar dos métodos pack y unpack que empaqueten y desempaqueten
    los datos respectivamente. Al empaquetador se le pasa el contenido del campo mientras que unpack() recibe la cadena
    binaria completa con toda la información opcional del enlace y un offset por referencia que debe ser actualizado al
    terminar la lectura.

    + Los campos opcionales de tipo booleano irán a NULL en el array ya que no es necesario guardar más información.

    **********************************************************************************************************************

    */
    private static function _getOptionalFields() {

        return [

            'EXTRAINFO' => [

                'pack' => function($data) {return pack('n', strlen($data)-1) . $data;},

                'unpack' => function($data, &$offset) {$ret=substr($data, $offset+2, unpack('nlength', substr($data, $offset, 2))['length']+1); $offset+=2+strlen($ret); return $ret;}
            ],

            'HIDENAME'      => null,

            'PASSWORD'      => [

                'pack' => function($data, $salt=null) {return pack('C', self::PBKDF2_ITERATIONS_LOG2 - 1) . hash_pbkdf2('sha256', $data, ($pbkdf2_salt = is_null($salt)?openssl_random_pseudo_bytes(16):$salt), pow(2, self::PBKDF2_ITERATIONS_LOG2), 0, true) . $pbkdf2_salt;},

                'unpack' => function($data, &$offset) { $ret = ['iterations' => unpack('Citer', $data[$offset])['iter']+1, 'pbkdf2_hash' => substr($data, $offset + 1, 32), 'salt' => substr($data, $offset + 33, 16) ]; $offset+=1+strlen($ret['pbkdf2_hash'])+strlen($ret['salt']); return $ret;}
            ],

            'EXPIRE'        => [

                'pack' => function($data) {return pack('NN', ($data >> 32) & 0xFFFFFFFF, $data & 0xFFFFFFFF);},

                'unpack' => function($data, &$offset) {

                    $expire = unpack('Nmsw/Nlsw', substr($data, $offset, 8));

                    $offset+=8;

                    return ($expire['msw'] << 32) | $expire['lsw'];}
            ],

            'NOEXPIRETOKEN' => null,

            'REFERER'       => [

                'pack' => function($data) {return pack('n', strlen($data)-1) . $data;},

                'unpack' => function($data, &$offset) {$ret=substr($data, $offset+2, unpack('nlength', substr($data, $offset, 2))['length']+1); $offset+=2+strlen($ret); return $ret;}

            ],

            'EMAIL'         => [

                'pack' => function($data) {return pack('C', strlen($data)-1) . $data;},

                'unpack' => function($data, &$offset) {$ret=substr($data, $offset+1, unpack('Clength', substr($data, $offset, 1))['length']+1); $offset+=1+strlen($ret); return $ret;}

            ],

            'ZOMBIE'        => [

                'pack' => function($data) { list($o1,$o2,$o3,$o4) = explode('.', $data); return pack('CCCC', $o1,$o2,$o3,$o4); },

                'unpack' => function($data, &$offset) {

                    $octetos = unpack('Co1/Co2/Co3/Co4', substr($data, $offset, 4));

                    $offset+=4;

                    return "{$octetos['o1']}.{$octetos['o2']}.{$octetos['o3']}.{$octetos['o4']}"; }

            ]
        ];
    }

    public static function encryptLinkList(array $links=null, array $options = [], $app_finfo = false, $cook_options=true, $anti_timeout=false) {

        if (!empty($links)) {
            
            if($cook_options) {
                
                $options = self::_cookOptionsArray($options);
            }
            
            $crypt_links = [];

            foreach ($links as $link) {

                if(($domain=Utils_MiscTools::extractHostFromUrl(($link=trim($link)), true))) {
                    
                    try {
                        
                        switch(str_replace('mega.co.nz', 'mega.nz', $domain)) {
                            
                            case 'mega.nz':
                                
                                $crypter = '_encryptMega'.(stripos($link, '/#F!') !== false?'Folder':'Single').'Link';

                                $c_link = self::$crypter($link, $options, $app_finfo);
                                
                                break;
                            
                            case preg_replace('/^https?\:\/\//i', '', trim(URL_BASE, '/ ')):
                                
                                $c_link = self::_reEncryptMegaCrypterLink($link, $options);
                                
                                break;
                        }

                    } catch (Exception_LinkException $exception) {
                        
                        $c_link = "LINK ERROR (".$exception->getCode().") {$link}";
                    }
                    
                    if(isset($c_link)) {
                        
                        $crypt_links[] = $c_link;
                        
                        if($anti_timeout) {
                            echo '<span style="display:none"></span>';
                        }
                    }
                }
            }

            return $crypt_links;
        }
    }

    private static function _encryptMegaSingleLink($link, array $options=[], $app_finfo=false) {

        $link = preg_replace('/#(?:!N\?|N!)([^!]+)(.*?)###n=(.+)$/', '#!\1#\3*\2', $link);

		list(, $file_id, $file_key) = explode('!', $link);

        $c_link = self::_encryptLink($link, $options);

        if ($app_finfo) {
            
            $ma = new Utils_MegaApi(MEGA_API_KEY);
            
            try {

                $file_info = $ma->getFileInfo($file_id, $file_key, IGNORE_CACHE_ON_LINK_CRYPT);

                $info = "{$file_info['name']} [" . Utils_MiscTools::formatBytes($file_info['size']) . "]";

            } catch (Exception_MegaLinkException $exception) {
                $info = '---['.$exception->getMessage().']---';
            }

            $c_link = "{$info} {$c_link}";
        }

        return $c_link;
    }

    private static function _encryptMegaFolderLink($link, array $options=[], $app_finfo=false) {

        list(, $folder_id, $folder_key) = explode('!', $link);
        
        $mega_links = self::_getFolderMegaLinks($folder_id, $folder_key);

        if (!empty($mega_links)) {
            
            if ($app_finfo) {

                $clinks=[];

                foreach($mega_links as $mlink) {

                    $clinks[] = "{$mlink['name']} [" . Utils_MiscTools::formatBytes($mlink['size']) . "] ". self::_encryptLink(Utils_MegaApi::MEGA_HOST . "/#!{$mlink['node_id']}*{$folder_id}!{$folder_key}", $options);
                }

            } else {
                
                $urls = [];
                
                foreach($mega_links as $mlink) {
                    
                    $urls[] = Utils_MegaApi::MEGA_HOST . "/#!{$mlink['node_id']}*{$folder_id}!{$folder_key}";
                }
                            
                $clinks = self::encryptLinkList($urls, $options, false, false);
            }

            $c_link = $clinks;
            
        } else {
            $c_link = "[EMPTY-FOLDER]{$link}";
        }

        return $c_link;
    }

    private static function _reEncryptMegaCrypterLink($link, array $options=[]) {

        try {
            
            $dec_link = self::decryptLink($link);
            
            if(!empty($dec_link['zombie'])) {
            
                throw new Exception_MegaCrypterLinkException(self::LINK_ERROR);
            
            }
        
        } catch (Exception_MegaCrypterLinkException $exception) {

            if ($exception->getCode() == self::BLACKLISTED_LINK && ZOMBIE_LINKS) {

                $dec_link = array_merge(self::decryptLink($link, null, true), ['EXPIRE' => time() + self::ZOMBIE_LINK_TTL, 'EXTRAINFO' => 'Zombie link!', 'hide_name' => true, 'REFERER' => null, 'EMAIL' => null, 'ZOMBIE' => $_SERVER['REMOTE_ADDR']]);
                
            } else {
                
                throw $exception;
            }
        }
        
        return self::_encryptLink("!{$dec_link['file_id']}!{$dec_link['file_key']}", array_merge($options, $dec_link));
    }

    private static function _getFolderMegaLinks($folder_id, $folder_key) {

        $ma = new Utils_MegaApi(MEGA_API_KEY, false);

        $child_nodes = $ma->getFolderChildFileNodes($folder_id, $folder_key);
        
        $mega_links = [];
        
        foreach ($child_nodes as $node) {
            $mega_links[] = ['name' => $node['name'], 'size' => $node['size'], 'node_id' => $node['id']];
        }

        return $mega_links;
    }
    
    private static function _cookOptionsArray(array $options) {
        
        $EXPIRE_SECS = [600, 3600, 86400, 604800, 1209600, 2592000, 7776000, 15552000, 31536000];
        
        $cooked_options = array_merge(['tiny_url' => false, 'PASSWORD' => null, 'EXTRAINFO' => null, 'HIDENAME' => false, 'EXPIRE' => false, 'NOEXPIRETOKEN' => true, 'REFERER' => null, 'EMAIL' => null, 'ZOMBIE' => null], $options);
                
        $cooked_options['EXPIRE'] = (!is_numeric($options['EXPIRE']) || !isset($EXPIRE_SECS[(int)$options['EXPIRE'] - 1])) ? false : time() + $EXPIRE_SECS[(int) $options['EXPIRE'] - 1];
        
        $cooked_options['REFERER'] = !empty($options['REFERER']) ? Utils_MiscTools::extractHostFromUrl(filter_var($options['REFERER'], FILTER_SANITIZE_STRING), true) : null;

        return $cooked_options;
    }

    /**
     * 
     * @param string $id MC/MEGA link id
     * @return type
     */
    public static function isBlacklistedLink($id) {

        if(($cached=Utils_MemcacheTon::getInstance()->get(BLACKLIST_MEMCACHE_PREFIX.$id)) !== false) {
            
            $isblacklisted=$cached;
            
        } else {
            
            $res = Utils_PDOTon::getInstance()->prepare("SELECT count(*) FROM blacklist WHERE id = ?");

            $res->execute([$id]);
            
            $isblacklisted=(int)$res->fetchColumn();
        }

        if(Utils_MemcacheTon::getInstance()->replace(BLACKLIST_MEMCACHE_PREFIX.$id, $isblacklisted, MEMCACHE_COMPRESSED, self::CACHE_BLACKLISTED_TTL) === false) {

            Utils_MemcacheTon::getInstance()->set(BLACKLIST_MEMCACHE_PREFIX.$id, $isblacklisted, MEMCACHE_COMPRESSED, self::CACHE_BLACKLISTED_TTL);
        }

        return (boolean)$isblacklisted;
    }

    public static function blacklistLink($id, $reporter = null, $ip = null) {

        $res = Utils_PDOTon::getInstance()->prepare("INSERT IGNORE INTO blacklist (id, reporter, ip) VALUES (?,?,?)");

        $res->execute([$id, $reporter, $ip]);

        if(Utils_MemcacheTon::getInstance()->replace(BLACKLIST_MEMCACHE_PREFIX.$id, 1, MEMCACHE_COMPRESSED, self::CACHE_BLACKLISTED_TTL)) {

            Utils_MemcacheTon::getInstance()->set(BLACKLIST_MEMCACHE_PREFIX.$id, 1, MEMCACHE_COMPRESSED, self::CACHE_BLACKLISTED_TTL);
        }
    }

    private static function _decryptLegacyLink($data, $mc_key, $no_expire=null, $ignore_blacklist=false) {

        if (!$ignore_blacklist && BLACKLIST_LEVEL >= self::BLACKLIST_LEVEL_MC && self::isBlacklistedLink($data)) {

            throw new Exception_MegaCrypterLinkException(self::BLACKLISTED_LINK);

        } else {

            list($secret, $file_id, $file_key, $pass, $extra, $auth) = explode('@', gzinflate(Utils_CryptTools::aesCbcDecrypt(Utils_MiscTools::urlBase64Decode($data), hex2bin($mc_key), md5($mc_key, true))));

            if (!$ignore_blacklist && BLACKLIST_LEVEL == self::BLACKLIST_LEVEL_MEGA && self::isBlacklistedLink($file_id)) {

                throw new Exception_MegaCrypterLinkException(self::BLACKLISTED_LINK);

            } else {

                if ($extra) {

                    list($extra_info, $hide_name, $expire, $referer, $email, $zombie, $no_expire_token) = explode('#', $extra);

                    if(!empty($no_expire_token)) {

                        $no_expire_token = hash_hmac('sha256', base64_decode($secret), GENERIC_PASSWORD, true);
                    }

                    if (!empty($expire) && time() >= $expire && (empty($no_expire_token) || is_null($no_expire) || !Utils_CryptTools::hash_equals(base64_decode($no_expire), $no_expire_token))) {

                            throw new Exception_MegaCrypterLinkException(self::EXPIRED_LINK);
                    }

                    if (!empty($zombie) && $zombie != $_SERVER['REMOTE_ADDR']) {

                        throw new Exception_MegaCrypterLinkException(self::LINK_ERROR);
                    }
                }

                return [
                    'file_id' => $file_id,
                    'file_key' => $file_key,
                    'extra_info' => !empty($extra_info) ? base64_decode($extra_info) : false,
                    'pass' => !empty($pass) ? $pass : false,
                    'auth' => !empty($auth) ? base64_decode($auth) : false,
                    'hide_name' => !empty($hide_name),
                    'expire' => !empty($expire) ? $expire : false,
                    'no_expire_token' => !empty($no_expire_token)?base64_encode($no_expire_token):false,
                    'referer' => !empty($referer) ? base64_decode($referer) : false,
                    'email' => !empty($email) ? base64_decode($email) : false,
                    'zombie' => !empty($zombie) ? $zombie : false
                ];
            }
        }
    }

    private static function _checkLinkHmac($data, $hash) {

        $hex_keys = [MASTER_KEY];

        if(!empty($hex_keys)) {

            $data = Utils_MiscTools::urlBase64Decode($data);

            foreach($hex_keys as $key) {

                if( Utils_CryptTools::hash_equals(hash_hmac('crc32', $data, md5(hex2bin($key), true), true), hex2bin($hash) ) ) {

                    return $key;
                }
            }
        }

        return false;
    }

    private static function _checkLegacyLinkHmac($data, $hash) {

        $legacy_hex_keys = [MASTER_KEY];

        if(!empty($legacy_hex_keys)) {

            foreach($legacy_hex_keys as $key) {

                if( Utils_CryptTools::hash_equals(hash_hmac('crc32', $data, md5($key), true), hex2bin($hash)) ) {

                    return $key;
                }
            }
        }

        return false;
    }
}

