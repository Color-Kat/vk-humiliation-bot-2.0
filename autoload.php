<?php

function autoload($class)
{
    // $className = ltrim($className, '\\');
    // $fileName  = '';
    // $namespace = '';
    // if ($lastNsPos = strrpos($className, '\\')) {
    //     $namespace = substr($className, 0, $lastNsPos);
    //     $className = substr($className, $lastNsPos + 1);
    //     $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    // }
    // $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    // require 'src' . DIRECTORY_SEPARATOR . $fileName;

    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    require_once $path;
}
spl_autoload_register('autoload');
