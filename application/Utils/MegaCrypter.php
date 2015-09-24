<?php

class Utils_MegaCrypter
{
    const MAX_EXTRA_BYTES = 64;
    const MAX_REFERER_BYTES = 64;
    const MAX_EMAIL_BYTES = 64;
    const SECRET_BYTE_LENGTH = 16;
    const PASS_SALT_BYTE_LENGTH = 8;
    const PASS_HASH_ITERATIONS_LOG2 = 14;
    const ZOMBIE_LINK_TTL = 86400;
    const MAX_FILE_NAME_BYTES = 255;
    const CACHE_BLACKLISTED_TTL = 3600;
    const BLACKLIST_LEVEL_OFF = 0;
    const BLACKLIST_LEVEL_MC = 1;
    const BLACKLIST_LEVEL_MEGA = 2;

    /* Inicio constantes "peligrosas" (si se modifican, los links antiguos dejarán de funcionar) */
    const SEPARATOR = '@'; //Distinto a los caracteres en BASE64
    const SEPARATOR_EXTRA = '#'; //Distinto a los caracteres en BASE64 y distinto al separador normal
    const EXTRA_TRUE_CHAR = '*'; //Distinto a los caracteres en BASE64 y distinto de los separadores
    const HMAC_ALGO = 'crc32';
    /* Fin constantes peligrosas */

    /* Inicio códigos de error (los códigos positivos por debajo del 21 están reservados para errores del APIController) */
    const INTERNAL_ERROR = 21;
    const LINK_ERROR = 22;
    const BLACKLISTED_LINK = 23;
    const EXPIRED_LINK = 24;
    /* Fin códigos de error */

    private static function _encryptLink($link, array $options=[]) {
        if (preg_match('/^.*?!(?P<file_id>[^!]+)!(?P<file_key>.+)$/', trim($link), $match)) {
            
            $secret = base64_encode(openssl_random_pseudo_bytes(self::SECRET_BYTE_LENGTH));

            /* ¡OJO! -> NO SE PUEDE CAMBIAR EL ORDEN NI ELIMINAR NINGUNO */
            $extra = implode(self::SEPARATOR_EXTRA, [!empty($options['extra_info']) ? base64_encode(substr($options['extra_info'], 0, self::MAX_EXTRA_BYTES)) : null,
                $options['hide_name'] ? self::EXTRA_TRUE_CHAR : null,
                (is_numeric($options['expire']) && time() < (int) $options['expire']) ? (int) $options['expire'] : null,
                !empty($options['referer']) ? base64_encode(substr($options['referer'], 0, self::MAX_REFERER_BYTES)) : null,
                !empty($options['email']) ? base64_encode(substr($options['email'], 0, self::MAX_EMAIL_BYTES)) : null,
                !empty($options['zombie']) ? $options['zombie'] : null]
            );

            $data = Utils_MiscTools::urlBase64Encode(Utils_CryptTools::aesCbcEncrypt(gzdeflate(implode(self::SEPARATOR, [$secret, $match['file_id'], $match['file_key'], !empty($options['pass']) ? self::PASS_HASH_ITERATIONS_LOG2.'#'.  base64_encode(Utils_CryptTools::passHMAC('sha256', $options['pass'], ($salt=openssl_random_pseudo_bytes(self::PASS_SALT_BYTE_LENGTH)), pow(2, self::PASS_HASH_ITERATIONS_LOG2))) . '#' . base64_encode($salt) : null, $extra, !empty($options['auth'])?$options['auth']:null]), 9), Utils_MiscTools::hex2bin(MASTER_KEY), md5(MASTER_KEY, true)));

            $hash = hash_hmac(self::HMAC_ALGO, $data, md5(MASTER_KEY));

            $url_path = preg_replace('/.{' . self::MAX_FILE_NAME_BYTES . '}(?!$)/', '\0/', "!$data!$hash");
            
            $c_link = URL_BASE . "/$url_path";

            return ['link' => $options['tiny_url'] ? Utils_MiscTools::deflateUrl($c_link) : $c_link, 'secret' => $secret];
        } else {
            throw new Exception_MegaCrypterLinkException(self::LINK_ERROR);
        }
    }

    public static function decryptLink($link, $ignore_exceptions = false) {

        if (preg_match('/^.*?!(?P<data>[0-9a-z_-]+)!(?P<hash>[0-9a-f]+)/i', trim(str_replace('/', '', $link)), $match)) {

            if (hash_hmac(self::HMAC_ALGO, $match['data'], md5(MASTER_KEY)) != $match['hash']) {
                throw new Exception_MegaCrypterLinkException(self::LINK_ERROR);
            } else if (!$ignore_exceptions && BLACKLIST_LEVEL >= self::BLACKLIST_LEVEL_MC && self::isBlacklistedLink($match['data'])) {
                throw new Exception_MegaCrypterLinkException(self::BLACKLISTED_LINK);
            } else {

                list($secret, $file_id, $file_key, $pass, $extra, $auth) = explode(self::SEPARATOR, gzinflate(Utils_CryptTools::aesCbcDecrypt(Utils_MiscTools::urlBase64Decode($match['data']), Utils_MiscTools::hex2bin(MASTER_KEY), md5(MASTER_KEY, true))));

                if (!$ignore_exceptions && BLACKLIST_LEVEL == self::BLACKLIST_LEVEL_MEGA && self::isBlacklistedLink($file_id)) {
                    throw new Exception_MegaCrypterLinkException(self::BLACKLISTED_LINK);
                } else {

                    if ($extra) {
                        list($extra_info, $hide_name, $expire, $referer, $email, $zombie) = explode(self::SEPARATOR_EXTRA, $extra);

                        if (!$ignore_exceptions && !empty($expire) && time() >= $expire) {
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
                        'referer' => !empty($referer) ? base64_decode($referer) : false, 
                        'email' => !empty($email) ? base64_decode($email) : false, 
                        'zombie' => !empty($zombie) ? $zombie : false, 
                        'secret' => $secret
                        ];
                }
            }
        } else {
            throw new Exception_MegaCrypterLinkException(self::LINK_ERROR);
        }
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
        }

        return $crypt_links;
    }

    private static function _encryptMegaSingleLink($link, array $options=[], $app_finfo=false) {

        if(stripos($link, '/#N') !== false) {
			
			$link = str_replace("!{$file_id}!", "!{$file_id}*!", $link);
		}
		
		list(, $file_id, $file_key) = explode('!', $link);

        Utils_MemcacheTon::getInstance()->delete($file_id . $file_key);

        $c_link = self::_encryptLink($link, $options)['link'];

        if ($app_finfo) {
            
            $ma = new Utils_MegaApi(MEGA_API_KEY);
            
            try {

                $file_info = $ma->getFileInfo($file_id, $file_key);
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
                    
                    Utils_MemcacheTon::getInstance()->delete($mlink['node_id'].$folder_key);
 
                    $clinks[] = "{$mlink['name']} [" . Utils_MiscTools::formatBytes($mlink['size']) . "] ". self::_encryptLink(Utils_MegaApi::MEGA_HOST . "/#!{$mlink['node_id']}*{$folder_id}!{$folder_key}", $options)['link'];
                }

            } else {
                
                $urls = [];
                
                foreach($mega_links as $mlink) {
                    
                    Utils_MemcacheTon::getInstance()->delete($mlink['node_id'].$folder_key);
                    
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

                $dec_link = array_merge(self::decryptLink($link, true), ['expire' => time() + self::ZOMBIE_LINK_TTL, 'extra_info' => 'Zombie link!', 'hide_name' => true, 'referer' => null, 'email' => null, 'zombie' => $_SERVER['REMOTE_ADDR']]);
                
            } else {
                
                throw $exception;
            }
        }
        
        return self::_encryptLink("!{$dec_link['file_id']}!{$dec_link['file_key']}", array_merge($options, $dec_link))['link'];
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
        
        $cooked_options = array_merge(['tiny_url' => false, 'pass' => null, 'extra_info' => null, 'hide_name' => false, 'expire' => false, 'referer' => null, 'email' => null, 'zombie' => null, 'auth' => null], array_change_key_case($options));
                
        $cooked_options['expire'] = (!is_numeric($options['expire']) || !isset($EXPIRE_SECS[(int)$options['expire'] - 1])) ? false : time() + $EXPIRE_SECS[(int) $options['expire'] - 1];
        
        $cooked_options['referer'] = !empty($options['referer']) ? Utils_MiscTools::extractHostFromUrl(filter_var($options['referer'], FILTER_SANITIZE_STRING), true) : null;
        
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
        
        Utils_MemcacheTon::getInstance()->set(BLACKLIST_MEMCACHE_PREFIX.$id, $isblacklisted, MEMCACHE_COMPRESSED, self::CACHE_BLACKLISTED_TTL);
        
        return (boolean)$isblacklisted;
    }

    public static function blacklistLink($id, $reporter = null, $ip = null) {

        $res = Utils_PDOTon::getInstance()->prepare("INSERT IGNORE INTO blacklist (id, reporter, ip) VALUES (?,?,?)");

        $res->execute([$id, $reporter, $ip]);

        Utils_MemcacheTon::getInstance()->set(BLACKLIST_MEMCACHE_PREFIX.$id, 1, MEMCACHE_COMPRESSED, self::CACHE_BLACKLISTED_TTL);
    }

}

