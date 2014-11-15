<?php

class Controller_HttpErrorController extends Controller_DefaultController
{
    protected function action() {
        
        $this->setViewData(['error' => $this->request->getServerVar('REDIRECT_STATUS')]);
        
    }
}