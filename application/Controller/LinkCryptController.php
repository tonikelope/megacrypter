<?php

class Controller_LinkCryptController extends Controller_DefaultController
{
    const CRYPT_TEXTAREA_COLS = 145;
    
    protected function action() {
        
        if ($this->isValidReferer()) {
            
            $mc_links = Utils_MegaCrypter::encryptLinkList(Utils_MiscTools::extractLinks(Utils_CryptTools::decryptMegaDownloaderLinks($this->request->getPostVar('links'))), ['tiny_url' => $this->request->getPostVar('tiny_url'), 'PASSWORD' => $this->request->getPostVar('pass'), 'EXTRAINFO' => $this->request->getPostVar('extra_info'), 'HIDENAME' => $this->request->getPostVar('hide_name'), 'EXPIRE' => $this->request->getPostVar('expire'), 'NOEXPIRETOKEN' => $this->request->getPostVar('no_expire_token'), 'REFERER' => $this->request->getPostVar('referer'), 'EMAIL' => $this->request->getPostVar('email')], $this->request->getPostVar('app_finfo'));
                
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
