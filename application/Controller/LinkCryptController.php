<?php

class Controller_LinkCryptController extends Controller_DefaultController
{
    const CRYPT_TEXTAREA_COLS = 145;
    
    protected function action() {
        
        if ($this->isValidReferer()) {
            
            $mc_links = Utils_MegaCrypter::encryptLinkList(Utils_MiscTools::extractLinks(Utils_CryptTools::decryptMegaDownloaderLinks($this->request->getPostVar('links'))), ['tiny_url' => $this->request->getPostVar('tiny_url'), 'pass' => $this->request->getPostVar('pass'), 'extra_info' => $this->request->getPostVar('extra_info'), 'hide_name' => $this->request->getPostVar('hide_name'), 'expire' => $this->request->getPostVar('expire'), 'no_expire_token' => $this->request->getPostVar('no_expire_token'), 'referer' => $this->request->getPostVar('referer'), 'email' => $this->request->getPostVar('email')], $this->request->getPostVar('app_finfo'));
                
            if (!empty($mc_links)) {

                $this->setViewData(['links' => Utils_MiscTools::rimplode("\r\n", $mc_links), 'cols' => min([Utils_MiscTools::getMaxStringLength($mc_links), self::CRYPT_TEXTAREA_COLS]), 'tot_links' => Utils_MiscTools::rCount($mc_links)]);

            } else {

                throw new Exception(__METHOD__ . ' No links could be crypted!');
            }
            
        } else {
            
            throw new Exception_InvalidRefererException(function(Controller_DefaultController $controller) { $controller->redirect('/');});
        }
    }
}
