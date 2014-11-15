<?php

require_once VENDOR_PATH . 'autoload.php';

function zendStyleAutoload($className) {
    $className = ltrim($className, '\\');

    $fileName = '';

    $namespace = '';

    if (($lastNsPos = strripos($className, '\\'))) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }

    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    if (is_readable(stream_resolve_include_path($fileName))) {
        include_once $fileName;
    }
}

spl_autoload_register('zendStyleAutoload');


