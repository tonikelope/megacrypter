<?php

class Controller_DefaultController
{
    private $_view_data;
    private $_twig_env;
    private $_template_file;
    private $_content_type = null;
    private $_redirect_url = null;
    protected $request;

    const CTYPE_JSON = 'application/json; charset=utf-8';

    public function __construct(Utils_Request $request, $auto_populate_view = false) {
        $this->request = $request;
        $this->_twig_env = new Twig_Environment(new Twig_Loader_Filesystem(DEFAULT_TWIG_TEMPLATE_DIR));
        $this->_twig_env->addExtension(new Twig_Extensions_Extension_I18n());
        $this->_template_file = strtolower($request->getVar('controller')) . '.html';
        $this->_view_data = $auto_populate_view ? $this->_genViewData() : [];
    }

    protected function preDispatch() {
        if (STOP_IT_ALL) {

            throw new Exception_PreDispatchException(function(Controller_DefaultController $controller) {
                $controller->setViewData(['message' => 'EMERGENCY STOP']);
                $controller->setTemplateFile('linkerror.html');
            });
        } else if (WEB_MAINTENANCE !== false) {

            if(WEB_MAINTENANCE === $this->getRequest()->getServerVar('REMOTE_ADDR')) {
                
                $this->setViewData(['maint_warning' => true]);
                
            } else {
                    throw new Exception_PreDispatchException(function(Controller_DefaultController $controller) {
                    $controller->setViewData(['message' => 'We are undergoing maintenance. Please come back later.']);
                    $controller->setTemplateFile('linkwarning.html');
                });
            }
        }
    }
    
    public function dispatch() {
        try {
            $this->preDispatch();

            if (method_exists($this, 'action')) {
                $this->action();
            }
            
        } catch (Exception $exception) {
            if ($exception instanceof Exception_iControllerTractableException) {

                if (ERROR_LOG) {
                    error_log(!is_null($exception->getMessage()) ? $exception->getMessage() : __METHOD__ . ' ' . get_class($exception) . ' code ' . $exception->getCode());
                }

                $exception->handleIt($this);
                
            } else {

                throw $exception;
            }
        }

        if (!$this->_doRedirection()) {
            
            $this->_prepareHeaders();
            
            $this->_setCommonViewData();
            
            echo $this->_twig_env->render($this->_template_file, $this->_view_data);
        }
    }

    public function setViewData(array $data, $update = true) {
        $this->_view_data = $update ? array_merge($this->_view_data, $data) : $data;
    }

    public function setTemplateFile($filename) {
        $this->_template_file = $filename;
    }

    public function setContentType($ctype) {
        $this->_content_type = $ctype;
    }

    public function getContentType() {
        return $this->_content_type;
    }

    public function redirect($url) {
        $this->_redirect_url = is_string($url) ? $url : null;
    }

    public function getRedirect() {
        return $this->_redirect_url;
    }

    public function getRequest() {
        return $this->request;
    }

    private function _prepareHeaders() {
        if (!is_null($this->_content_type)) {
            switch ($this->_content_type) {
                case self::CTYPE_JSON:

                    header('Cache-Control: no-cache, must-revalidate');
                    header('Expires: Fri, 21 Dec 1984 17:00:00 GMT');

                    break;
            }

            header('Content-type: ' . $this->_content_type);
        }
    }

    private function _doRedirection() {
        if (!is_null($this->_redirect_url)) {
            header("Location: {$this->_redirect_url}");
            return TRUE;
        }
        else
            return FALSE;
    }

    private function _genViewData() {
        $view_data = [];

        foreach ($this->request->getVar() as $key => $value) {
            if (!empty($value))
                $view_data[$key] = $value;
        }

        foreach ($this->request->getPostVar() as $key => $value) {
            if (!empty($value))
                $view_data[array_key_exists($key, $view_data) ? 'post_' . $key : $key] = $value;
        }

        return $view_data;
    }
    
    private function _setCommonViewData() {
        
        list($quote, $quote_a) = Utils_QuoteGenerator::next();
            
        $this->setViewData(['sys_load' => sys_getloadavg(), 'quote' => $quote, 'quote_a' => $quote_a, 'quote_a_google' => preg_replace('/ +/', '+', strtolower($quote_a))]);
            
    }

    protected function isValidReferer($referer = null) {
        return preg_match(is_null($referer) ? '/^' . preg_quote(preg_replace('/^https?\:\/\//i', '', trim(URL_BASE)), '/') . '/i' : '/^.*?' . preg_quote(preg_replace('/^https?\:\/\//i', '', trim($referer)), '/') . '$/i', Utils_MiscTools::extractHostFromUrl($this->request->getServerVar('HTTP_REFERER'), true));
    }

}
