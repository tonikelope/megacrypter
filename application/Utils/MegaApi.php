<?php

class Utils_MegaApi
{
    const MEGA_HOST = 'https://mega.co.nz';
    const MEGA_API_HOST = 'https://g.api.mega.co.nz';
    const CONNECT_TIMEOUT = 15;
    const FILE_KEY_BYTE_LENGTH = 32;
    const FOLDER_KEY_BYTE_LENGTH = 16;
    const CACHE_FILEINFO_TTL = 3600;

    /* MEGA API ERRORS (negative) */
    const EINTERNAL = -1;
    const EARGS = -2;
    const EAGAIN = -3;
    const ERATELIMIT = -4;
    const EFAILED = -5;
    const ETOOMANY = -6;
    const ERANGE = -7;
    const EEXPIRED = -8;
    const ENOENT = -9;
    const ECIRCULAR = -10;
    const EACCESS = -11;
    const EEXIST = -12;
    const EINCOMPLETE = -13;
    const EKEY = -14;
    const ESID = -15;
    const EBLOCKED = -16;
    const EOVERQUOTA = -17;
    const ETEMPUNAVAIL = -18;
    const ETOOMANYCONNECTIONS = -19;
    const EWRITE = -20;
    const EREAD = -21;
    const EAPPKEY = -22;
    const EDLURL = -101;

    private $_seqno;
    private $_api_key;
    private $_cache = null;
    private $_tor = null;

    public function getSeqno() {

        return $this->_seqno;
    }

    public function __construct($api_key, $use_cache = true, $use_tor=false) {

        $this->_seqno = mt_rand();
        $this->_api_key = $api_key;
        $this->_cache = $use_cache;
        
        /* Comprobar que TOR está instalado y corriendo antes! */
        $this->_tor = $use_tor;
    }

    public function rawAPIRequest(array $request, $folder_id = null) {

        $ch = curl_init(self::MEGA_API_HOST . '/cs?id=' . ($this->_seqno++) . "&ak={$this->_api_key}" . ($folder_id ? "&n={$folder_id}" : ''));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([$request]));
        curl_setopt($ch, CURLOPT_USERAGENT, CURL_USERAGENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        if($this->_tor) {
            
            curl_setopt($ch, CURLOPT_PROXY, TOR_PROXY_SOCKS);
            curl_setopt($ch, CURLOPT_PROXYTYPE, _CURLPROXY_SOCKS5_HOSTNAME);
        }

        $resp = json_decode(curl_exec($ch));
        
        $curl_error = curl_errno($ch);

        curl_close($ch);

        if (!$curl_error && isset($resp[0])) {
            if (is_int($resp[0])) {
                throw new Exception_MegaLinkException($resp[0]);
            } else {
                return $resp[0];
            }
        } else {
            throw new Exception_MegaLinkException(self::EINTERNAL);
        }
    }

    /**
     * Decrypts node attribute
     * 
     * @param string $at Base64 node encrypted attributes
     * @param string $key Base64 node key
     * @return string Json object
     * @throws Exception_MegaLinkException
     */
    private function _decryptAt($at, $key) {

        if (preg_match('/^.*MEGA.*?(?P<at>\{.+\}).*$/is', Utils_CryptTools::aesCbcDecrypt(Utils_MiscTools::urlBase64Decode($at), $this->_urlBase64KeyDecode($key)), $match)) {
            return $this->_sanitizeAt(json_decode($match['at']));
        } else {
            throw new Exception_MegaLinkException(self::EKEY);
        }
    }

    /**
     * Decrypts node key
     * 
     * @param string $node_key Base64 encrypted node key
     * @param string $folder_key Base64 master key
     * @return string Base64 node key
     */
    private function _decryptB64NodeKey($node_key, $folder_key) {
        return Utils_MiscTools::urlBase64Encode(Utils_CryptTools::aesEcbDecrypt(Utils_MiscTools::urlBase64Decode($node_key), $this->_urlBase64KeyDecode($folder_key)));
    }

    public function getFileInfo($fid, $fkey) {

        if (strpos($fid, '*') !== false) {
            list($file_id, $folder_id) = explode('*', $fid);

            if (empty($file_id) || empty($folder_id)) {
                throw new Exception_MegaLinkException(self::ENOENT);
            }
        }

        if ($this->_cache) {
            $cached_file_info = Utils_MemcacheTon::getInstance()->get((isset($file_id) ? $file_id : $fid) . $fkey);

            if (is_int($cached_file_info)) {
                throw new Exception_MegaLinkException($cached_file_info);
            }
        }

        try {
            if (!$this->_cache || $cached_file_info === false) {
                if (isset($folder_id)) {
                    $child_node = $this->getFolderChildFileNodes($folder_id, $fkey, $file_id);

                    $file_info = ['name' => $child_node['name'], 'path'=> $child_node['path'], 'size' => $child_node['size'], 'key' => $child_node['key']];
                } else {
                    $response = $this->rawAPIRequest(['a' => 'g', 'p' => $fid]);

                    $at = $this->_decryptAt($response->at, $fkey);

                    $file_info = ['name' => $at->n, 'size' => $response->s];
                }
                
                if(empty($file_info['name'])) {
                    $file_info['name'] = md5((isset($file_id) ? $file_id . $file_info['key'] : $fid . $fkey) . base64_decode(GENERIC_PASSWORD));
                }
                
            } else {
                $file_info = $cached_file_info;
            }

            if ($this->_cache && $file_info['size'] > 0 ) {
                Utils_MemcacheTon::getInstance()->set((isset($file_id) ? $file_id : $fid) . $fkey, $file_info, MEMCACHE_COMPRESSED, self::CACHE_FILEINFO_TTL);
            }
        } catch (Exception_MegaLinkException $exception) {
           
            if ($this->_cache && Utils_MiscTools::isCacheableError($exception->getCode())) {
                Utils_MemcacheTon::getInstance()->set((isset($file_id) ? $file_id : $fid) . $fkey, $exception->getCode(), MEMCACHE_COMPRESSED, self::CACHE_FILEINFO_TTL);
            }
            
            throw $exception->getCode() == self::EINTERNAL?new Exception_MegaLinkException(self::ETEMPUNAVAIL):$exception;
        }

        return $file_info;
    }

    public function getFileDownloadUrl($fid, $ssl = false, $verify=true) {

        if (strpos($fid, '*') !== false) {
            list($file_id, $folder_id) = explode('*', $fid);

            if (empty($file_id) || empty($folder_id)) {
                throw new Exception_MegaLinkException(self::ENOENT);
            }

            $params = [['a' => 'g', 'g' => 1, 'n' => $file_id], $folder_id];
        } else {
            $params = [['a' => 'g', 'g' => 1, 'p' => $fid]];
        }

        if ($ssl) {
            $params[0]['ssl'] = 2;
        }
        
        try
        {
            $url = call_user_func_array([$this, 'rawAPIRequest'], $params)->g;
        
        } catch (Exception_MegaLinkException $exception) {
            
            throw $exception->getCode() == self::EINTERNAL?new Exception_MegaLinkException(self::ETEMPUNAVAIL):$exception;
        }

        return $verify?$this->_verifyDownloadUrl($url):$url;
    }

    public function getFolderChildFileNodes($folder_id, $folder_key, $node_id = null, $name_sorted = true) {

        $folder = $this->rawAPIRequest(['a' => 'f', 'c' => 1, 'r' => 1], $folder_id);

        $file_nodes = [];
        
        foreach ($folder->f as $node) {
            
            list(, $node_k) = explode(':', $node->k);

            $k = $this->_decryptB64NodeKey($node_k, $folder_key);

            $file_nodes[$node->h] = ['type' => $node->t, 'parent' => $node->p, 'key' => $k, 'size' => $node->s, 'name' => $this->_decryptAt($node->a, $k)->n];
        }
        
        $fnodes = [];
        
        $paths = [];

        foreach ($file_nodes as $id => $node) {
            
            if ($node['type'] == 0) {
                
                $aux_node = $node;
                
                $aux_node['id'] = $id;
                
                if(!isset($paths[$id])) {
                    
                    $paths[$id] = $this->_calculatePath($file_nodes, $id);
                }
                
                $aux_node['path'] = $paths[$id];
                
                unset($aux_node['type']);
                
                unset($aux_node['parent']);
                
                if ($id == $node_id) {
                    return $aux_node;
                } else {
                    $fnodes[] = $aux_node;
                }
            }
        }

        if ($name_sorted) {
            usort($fnodes, function($a, $b) {
                        return strnatcasecmp($a['path'].$a['name'], $b['path'].$b['name']);
                    });
        }
        
        return $fnodes;
    }
    
    private function _calculatePath($file_nodes, $id) {
        
        $path = '';
        
        $parent_id = $file_nodes[$id]['parent'];
        
        while(isset($file_nodes[$parent_id]))
        {
            $path="{$file_nodes[$parent_id]['name']}/$path";
            
            $parent_id = $file_nodes[$parent_id]['parent']; 
        }
        
        return $path;
    }
    
    private function _urlBase64KeyDecode($key) {

        $key_bin = Utils_MiscTools::urlBase64Decode($key);
        
        if(strlen($key_bin) < self::FILE_KEY_BYTE_LENGTH) {
            
            return substr($key_bin, 0, self::FOLDER_KEY_BYTE_LENGTH);
            
        } else {
            
            $key_i32a = Utils_MiscTools::bin2i32a(substr($key_bin, 0, self::FILE_KEY_BYTE_LENGTH));

            return Utils_MiscTools::i32a2Bin([$key_i32a[0] ^ $key_i32a[4], $key_i32a[1] ^ $key_i32a[5], $key_i32a[2] ^ $key_i32a[6], $key_i32a[3] ^ $key_i32a[7]]);
        }
    }
    
    private function _verifyDownloadUrl($url)
    {
        if (empty($url)) {
            throw new Exception_MegaLinkException(self::ETEMPUNAVAIL);
        }
        
        $ch = curl_init("{$url}/0-0");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
        curl_setopt($ch, CURLOPT_USERAGENT, CURL_USERAGENT);
        curl_exec($ch);
        
        $curl_error = curl_errno($ch);
        
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if($curl_error || $http_code != 200) {
            throw new Exception_MegaLinkException(self::EDLURL);
        }
        
        return $url;
    }
    
    private function _sanitizeAt($at) {
        
        $at->n = preg_replace('/[\x00-\x1F\x80-\x9F]/u', '', $at->n);
        
        return $at;
    }
}
