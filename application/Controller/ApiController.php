<?php

class Controller_ApiController extends Controller_DefaultController
{
    const MAX_LINKS_LIST = 250;
    const EMETHOD = 1;
    const EREQ = 2;

    protected function preDispatch() {
        
        $this->setContentType(self::CTYPE_JSON);
        
        if (STOP_IT_ALL) {

            throw new Exception_PreDispatchException(function(Controller_DefaultController $controller) {

                $controller->setViewData(['data' => json_encode(['error' => Utils_MegaCrypter::INTERNAL_MC_ERROR])]);
            });
        }
    }

    protected function action() {

        if (strtoupper($this->request->getServerVar('REQUEST_METHOD')) == 'POST') {
            
            $post_data = json_decode(file_get_contents('php://input'));
            
            if(!empty($post_data->m) && method_exists($this, ($action_method='_action'.ucfirst(strtolower($post_data->m))))) {
                
                try {
                    
                    $data = $this->$action_method($post_data);
                
                } catch (Exception $exception) {
                    
                    throw ($exception instanceof Exception_MegaCrypterAPIException)?$exception:new Exception_MegaCrypterAPIException($exception->getCode());
                }
                    
            } else {
                
                throw new Exception_MegaCrypterAPIException(self::EMETHOD);
            }
            
        } else {
            
            throw new Exception_MegaCrypterAPIException(self::EREQ);
        }

        $this->setViewData(['data' => Utils_MiscTools::unescapeUnicodeChars(json_encode($data))]);
    }
    
    private function _actionInfo($post_data) {
        
        $dec_link = $this->_decryptLink($post_data->link);
        
        $ma = new Utils_MegaApi(MEGA_API_KEY);
        
        $file_info = $ma->getFileInfo($dec_link['file_id'], $dec_link['file_key']);

        $data = [
            'name' => $dec_link['hide_name'] ? Utils_MiscTools::hideFileName($file_info['name'], ($dec_link['zombie'] ? $dec_link['zombie'] : null) . base64_decode(GENERIC_PASSWORD)) : $file_info['name'],
            'size' => $file_info['size'],
            'key' => isset($file_info['key']) ? $file_info['key'] : $dec_link['file_key'],
            'extra' => $dec_link['extra_info'],
            'expire' => $dec_link['expire'],
            'pass' => false
        ];

        if ($dec_link['pass']) {

            list($iterations, $pass, $pass_salt) = explode('#', $dec_link['pass']);

            $data['name'] = $this->_encryptApiField($data['name'], $pass);
            $data['key'] = $this->_encryptApiField(Utils_MiscTools::urlBase64Decode($data['key']), $pass);
            $data['pass'] = $iterations . '#'. base64_encode(hash('sha256', base64_decode($pass), true)) . '#' . $pass_salt;

            if (!empty($data['extra'])) {

                $data['extra'] = $this->_encryptApiField($data['extra'], $pass);
            }
        }
        
        return $data;
    }
    
    private function _actionDl($post_data) {
        
        $dec_link = $this->_decryptLink($post_data->link);
        
        $ma = new Utils_MegaApi(MEGA_API_KEY);
        
        try {
            
            $data = ['url' => $ma->getFileDownloadUrl($dec_link['file_id'], is_bool($post_data->ssl) ? $post_data->ssl : false)];
           
        } catch (Exception $exception) {
            
            Utils_MemcacheTon::getInstance()->delete($dec_link['file_id'] . $dec_link['file_key']);
            
            throw $exception;
        }
        
        return $data;
    }
    
    private function _actionCrypt($post_data) {
        
         if (is_array($post_data->links) && !empty($post_data->links) && (!self::MAX_LINKS_LIST || count($post_data->links) <= self::MAX_LINKS_LIST)) {
                        
            $data = ['links' => Utils_MegaCrypter::encryptLinkList(Utils_CryptTools::decryptMegaDownloaderLinks($post_data->links), ['tiny_url' => $post_data->tiny_url, 'pass' => $post_data->pass, 'extra_info' => $post_data->extra_info, 'hide_name' => $post_data->hide_name, 'expire' => $post_data->expire, 'referer' => $post_data->referer, 'email' => $post_data->email], $post_data->app_finfo)];
                        
        } else {

            throw new Exception_MegaCrypterAPIException(self::EMETHOD);
        }
        
        return $data;
    }
    
    private function _decryptLink($link) {
        
        if(preg_match('/^(?:https?\:\/\/)?mega(?:\.co)?\.nz/i', ($link = trim($link)))) {
                           
            if(preg_match('/^.*?\/#!(?P<file_id>[^!]+)!(?P<file_key>.+)$/i', $link, $match)) {

                $dec_link=['file_id' => $match['file_id'], 'file_key' => $match['file_key']];

            } else {

                throw new Exception_MegaCrypterAPIException(Utils_MegaCrypter::LINK_ERROR);
            }

        } else {

            $dec_link = Utils_MegaCrypter::decryptLink($link);
        }
        
        return $dec_link;
    }
    
    private function _encryptApiField($field_value, $pass_sha256_b64) {
        
        return base64_encode(Utils_CryptTools::aesCbcEncrypt($field_value, base64_decode($pass_sha256_b64), null, true));
    }

}
