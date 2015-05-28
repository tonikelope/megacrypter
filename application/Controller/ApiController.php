<?php

class Controller_ApiController extends Controller_DefaultController
{
    const MAX_LINKS_LIST = 250;
    const EMETHOD = 1;
    const EREQ = 2;

    protected function preDispatch() {
        
        $this->setContentType(self::CTYPE_JSON);
        
        if (STOP_IT_ALL) {

		throw new Exception_PreDispatchException(
			function(Controller_DefaultController $controller) {
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
		'expire' => $dec_link['expire']?$dec_link['expire'].'#'.base64_encode(hash('sha256', base64_decode($dec_link['secret']), true)):$dec_link['expire']
        ];

        if ($dec_link['pass']) {

			list($iterations, $pass, $pass_salt) = explode('#', $dec_link['pass']);
            
			$b64p = base64_decode($pass);
		
			$iv = openssl_random_pseudo_bytes(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

			$data['name'] = $this->_encryptApiField($data['name'], $b64p, $iv);
		
			$data['key'] = $this->_encryptApiField(Utils_MiscTools::urlBase64Decode($data['key']), $b64p, $iv);

			if (!empty($data['extra'])) {

				$data['extra'] = $this->_encryptApiField($data['extra'], $b64p, $iv);
			}
		
			$data['pass'] = $iterations . '#'. base64_encode(hash('sha256', $b64p, true)) . '#' . $pass_salt . '#' . base64_encode($iv);
        
        } else {
			
			$data['pass'] = false;
		}
        
        return $data;
    }
    
    private function _actionDl($post_data) {
        
        try
        {
			$dec_link = $this->_decryptLink($post_data->link);
			
		} catch(Exception_MegaCrypterLinkException $exception) {
			
			if($exception->getCode() == Utils_MegaCrypter::EXPIRED_LINK) {
				
				$dec_link = Utils_MegaCrypter::decryptLink($post_data->link, true);
					
				if($post_data->noexpire != base64_encode(hash('sha256', base64_decode($dec_link['secret']), true))) {
				
					throw $exception;
				}
					
			} else {
					
				throw $exception;
			}
		}

        $ma = new Utils_MegaApi(MEGA_API_KEY);
        
        try {
            
			$data = ['url' => $ma->getFileDownloadUrl($dec_link['file_id'], is_bool($post_data->ssl) ? $post_data->ssl : false)];
				
			if ($dec_link['pass']) {
					
				list($iterations, $pass, $pass_salt) = explode('#', $dec_link['pass']);
				
				$iv = openssl_random_pseudo_bytes(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

				$data['url'] = $this->_encryptApiField($data['url'], base64_decode($pass), $iv);
				
				$data['pass'] = $iterations . '#'. base64_encode(hash('sha256', $b64p, true)) . '#' . $pass_salt . '#' . base64_encode($iv);
				
			} else {
				
				$data['pass'] = false;
			}
            
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
        
        if(preg_match('/^(?:https?\:\/\/)?mega(?:\.co)?\.nz(?:\/#!(?P<file_id>[^!]+)!(?P<file_key>.+))?$/i', ($link = trim($link)), $match)) {
                           
			if(!empty($match['file_id']) && !empty($match['file_key'])) {

				$dec_link=['file_id' => $match['file_id'], 'file_key' => $match['file_key']];

			} else {
				
				throw new Exception_MegaCrypterAPIException(Utils_MegaCrypter::LINK_ERROR);
			}

        } else {

			$dec_link = Utils_MegaCrypter::decryptLink($link);
        }
        
        return $dec_link;
    }
    
    private function _encryptApiField($field_value, $pass_sha256, $iv) {
        
        return base64_encode(Utils_CryptTools::aesCbcEncrypt($field_value, $pass_sha256, $iv, true));
    }

}
