<?php
/**
 * Created by PhpStorm.
 * User: liugaoyun
 * Date: 2018/12/1
 * Time: 下午1:15
 */

defined('ROOT') or define('ROOT', __DIR__);

function autoload($className){
    $file = ROOT. $className.".php";
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
    if (file_exists($file)) {
        require_once $file;
        return;
    }
}


spl_autoload_register('autoload');