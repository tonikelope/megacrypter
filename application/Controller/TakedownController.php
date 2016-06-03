<?php

require_once LIB_PATH . 'recaptchalib.php';
require_once CONFIG_PATH . 'gmail.php';

class Controller_TakedownController extends Controller_DefaultController
{
    protected function action() {
        
        if(!TAKEDOWN_TOOL) {
            throw new Exception(__METHOD__ . ' TAKEDOWN TOOL is not enabled!');
        }
        
        $view_data = ['recaptcha' => recaptcha_get_html(RECAPTCHA_PUBLIC_KEY, null, true), 'links' => $this->request->getPostVar('links'), 'reporter_id' => $this->request->getPostVar('reporter_id')];

        if (!is_null($this->request->getPostVar('links'))) {

            if (recaptcha_check_answer(RECAPTCHA_PRIVATE_KEY, $this->request->getServerVar('REMOTE_ADDR'), $this->request->getPostVar('recaptcha_challenge_field'), $this->request->getPostVar('recaptcha_response_field'))->is_valid) {

                if (($reporter_data = $this->_isValidReporterId($this->request->getPostVar('reporter_id')))) {

                    $links_to_be_removed = $this->_genRemoveLinkList($this->request->getPostVar('links'));

                    if (!empty($links_to_be_removed)) {

                        $this->_removeLinks($links_to_be_removed, $reporter_data);

                        $view_data = ['recaptcha' => recaptcha_get_html(RECAPTCHA_PUBLIC_KEY, null, true), 'tot_removed_links' => count($links_to_be_removed)];

                    } else {

                        $view_data['error'] = 'No valid URLs to remove!';
                    }
                    
                } else {
                    
                    $view_data['error'] = 'Your reporter ID is not valid!';
                }
                
            } else {
                
                $view_data['error'] = 'Captcha code was not valid!';
            }
        }
        
        $this->setViewData($view_data);
    }
    
    private function _genRemoveLinkList($links) {
        
        if(preg_match_all('/(?:https?\:\/\/)?'.preg_quote(preg_replace('/^https?\:\/\//i', '', trim(URL_BASE, ' /')), '/').'\/![\da-z_,-\/]+?![\da-f\/]+?(?=https?\:\/\/|'.preg_quote(preg_replace('/^https?\:\/\//i', '', trim(URL_BASE, ' /')), '/').'|[^\da-z_,-\/]|$)/i', $links, $match) > 0) {
            
            $links_to_be_removed = [];
        
            foreach ($match[0] as $mc_link) {

                list(, $mc_link_id, ) = explode('!', $mc_link);

                $id = str_replace('/', '', $mc_link_id);

                if (!Utils_MegaCrypter::isBlacklistedLink($id)) {

                    try {
                        $links_to_be_removed[$mc_link] = Utils_MegaCrypter::decryptLink($mc_link);
                    } catch (Exception_MegaCrypterLinkException $exception) {

                    }
                }
            }
        }
        
        return $links_to_be_removed;
    }
    
    private function _removeLinks(array $links, $reporter_data, $blacklist_level=Utils_MegaCrypter::BLACKLIST_LEVEL_MC, $notify_admin=true, $notify_uploader=true) {
        
        $ma = new Utils_MegaApi(MEGA_API_KEY, false);
        
        $rem_links_by_email = [];

        foreach ($links as $mc_link => &$link_info) {
            
            if($blacklist_level == Utils_MegaCrypter::BLACKLIST_LEVEL_MEGA && $reporter_data['grants'] == $blacklist_level) {
                
                $id = $link_info['file_id'];
                
            } else {
                
                list(, $mc_link_id, ) = explode('!', $mc_link);

                $id = str_replace('/', '', $mc_link_id);
            }

            Utils_MegaCrypter::blacklistLink($id, $reporter_data['email'], $this->request->getServerVar('REMOTE_ADDR'));

            try {

                $link_info = array_merge($link_info, $ma->getFileInfo($link_info['file_id'], $link_info['file_key']));

                if (!is_null($link_info['email'])) {

                    $rem_links_by_email[$link_info['email']][$mc_link] = $link_info;
                }
                
            } catch (Exception_MegaLinkException $exception) {}
        }

        if($notify_admin) {
            
            $this->_notifyAdminRemovedLinks($links, $reporter_data['email']);
        }
        
        if ($notify_uploader && !empty($rem_links_by_email)) {

            $this->_notifyUploaderRemovedLinks($rem_links_by_email);
        }
    }

    private function _isValidReporterId($id) {
        
        list($email, $hmac, $grants) = explode('#', base64_decode($id));

        return hash_hmac('sha256', $email . $grants, GENERIC_PASSWORD) == $hmac ? ['email' => $email, 'grants' => $grants] : false;
    }

    private function _notifyUploaderRemovedLinks($rem_links_by_email) {
        
        $subject = '[Take down notice] Your links were removed';

        $gmails = [];

        foreach ($rem_links_by_email as $email => $links) {
            
            $body_links = [];

            foreach ($links as $mc_link => $link_info) {

                $body_link = [$mc_link];

                if (!empty($link_info['name'])) {

                    $body_link = array_merge($body_link, [$link_info['name'], "[" . Utils_MiscTools::formatBytes($link_info['size']) . "]"]);
                }

                $body_links[] = implode(' ', $body_link);
            }

            $body = "Following links were removed due to a violation of our Terms of Service:\n\n"
                    . implode("\n\n", $body_links) . "\n\n*** This is an automatically generated email, please do not reply ***";


            $gmails[$email] = ['subject' => $subject, 'body' => $body];
        }
        
        try {
            
            Utils_MiscTools::sendGmail(ABUSE_GMAIL, base64_decode(ABUSE_GMAIL_PASS), $gmails);
            
        }catch(Exception $exception){
            
            error_log($exception->getMessage());
        }
    }

    private function _notifyAdminRemovedLinks($removed_links, $reporter_email) {
        
        $body_links = [];

        foreach ($removed_links as $mc_link => $link_info) {

            $body_link = [$mc_link];

            if (!empty($link_info['name'])) {

                $body_link = array_merge($body_link, [$link_info['name'], "[" . Utils_MiscTools::formatBytes($link_info['size']) . "]"]);
            }

            $body_links[] = implode(' ', $body_link);
        }

        $email = ['subject' => "[TAKEDOWN TOOL]", 'body' => "{$reporter_email} (" . $this->getRequest()->getServerVar('REMOTE_ADDR') . ")\n\n" . implode("\n\n", $body_links)];
        
        try{
            
            Utils_MiscTools::sendGmail(ABUSE_GMAIL, base64_decode(ABUSE_GMAIL_PASS), [ADMIN_GMAIL => $email]);
            
        } catch (Exception $exception) {
            
            error_log($exception->getMessage());
        }
    }
}
