<?php

/**
 * @param $class
 */
function autoload($class)
{
    $basePath = DIR_APP . DS;
    $class = $basePath . 'App' . DS . str_replace('\\', DS, $class) . '.php';
    if (file_exists($class) && !is_dir($class)) {
        include $class;
    }
}

spl_autoload_register('autoload');