<?php

error_reporting(0); //Important!
date_default_timezone_set('Europe/Madrid');
require_once __DIR__ . '/../application/config/paths.php';
require_once APP_PATH . 'autoload.php';
require_once CONFIG_PATH . 'miscellaneous.php';
require_once LOCALE_PATH . 'locale.php';

try {

    $request = new Utils_Request();

    $controller_class = class_exists(($c = 'Controller_' . ucfirst($request->getVar('controller')) . 'Controller')) ? $c : 'Controller_DefaultController';

    $controller = new $controller_class($request);

    /* Let's dance */
    $controller->dispatch();
    
} catch (Exception $exception) {
    if (ERROR_LOG) {
        error_log(!is_null($exception->getMessage()) ? $exception->getMessage() : __METHOD__ . ' ' . get_class($exception) . ' code ' . $exception->getCode());
    }

    header('Location: /');
}
