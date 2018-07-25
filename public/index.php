<?php
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.4.0','<'))  die('require PHP > 5.4.0 !');
date_default_timezone_set('PRC');

//APP_PATH =test
define("APP_PATH", realpath(dirname(__FILE__) . '/../')); /* 指向public的上一级 */
define('STORAGE_PATH',APP_PATH . '/storage');


$app  = new Yaf\Application(APP_PATH . "/config/application.ini");
$app->bootstrap()->run();