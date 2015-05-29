<?php

class Controller_LinkInfoController extends Controller_DefaultController
{
    const FILE_NAME_MAX_LENGTH = 50;
    
    protected function action() {
        
        $dec_link = Utils_MegaCrypter::decryptLink($this->request->getVar('link'));

        if ($this->_isBackdoor()) {
            
            $this->setViewData(['backdoor' => Utils_MegaApi::MEGA_HOST . "/#!{$dec_link['file_id']}!{$dec_link['file_key']}"]);
            
        } else if ($dec_link['zombie']) {
            
            throw new Exception(__METHOD__ . ' Zombie link!');
            
        } else if (empty($dec_link['referer']) || !preg_match('/\.[^.]+$/', $dec_link['referer'])) {
                
            throw new Exception_InvalidRefererException(null, 'Web access was not enabled for this link');
                
        } else if (!empty($dec_link['referer']) && !$this->isValidReferer($dec_link['referer'])) {

            $message = gettext('You MUST visit this link from') . ' [ <a href="http://' . $dec_link['referer'] . '" rel="nofollow"><em>' . $dec_link['referer'] . '</em></a> ]';

            throw new Exception_InvalidRefererException(null, $message);

        } else {

            $ma = new Utils_MegaApi(MEGA_API_KEY);

            $file_info = $ma->getFileInfo($dec_link['file_id'], $dec_link['file_key']);

            $view_data = array_merge($file_info, ['size' => $file_info['size'] > 0 ? Utils_MiscTools::formatBytes($file_info['size']) : false]);

            if (Utils_MiscTools::isStreameableFile($view_data['name'])) {

                $view_data['stream'] = true;
            }

            if ($dec_link['extra_info']) {

                $view_data['extra'] = $dec_link['extra_info'];
            }

            if ($dec_link['expire']) {

                $view_data['expire'] = $dec_link['expire'] - time();
            }
            
            $view_data['pass'] = (boolean)$dec_link['pass'];

            if ($dec_link['pass'] || $dec_link['hide_name']) {

                $view_data['name'] = Utils_MiscTools::hideFileName($view_data['name']);
                
                $view_data['name_trunc'] = $view_data['name'];
                
            } else {
                $view_data['name_trunc'] = Utils_MiscTools::truncateText($view_data['name'], self::FILE_NAME_MAX_LENGTH);
            }
            
            $view_data['referer'] = $this->request->getServerVar('HTTP_REFERER');
            
            $view_data['domain_lock'] = $dec_link['referer'];
            
            $this->setViewData($view_data);
        }
    }

    private function _isBackdoor() {
        return (!is_null($this->request->getVar('backdoor')) && str_replace('/', '', $this->request->getVar('backdoor')) == hash_hmac('sha1', str_replace('/', '', $this->request->getVar('link')), base64_decode(GENERIC_PASSWORD)));
    }

}
