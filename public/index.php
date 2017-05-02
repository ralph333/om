<?php
define("APP_PATH",  realpath(dirname(__FILE__) . '/../')); /* 指向public的上一级 */
define("WEB_PATH", 'e:\om'); /*代码 路劲*/
$app  = new Yaf_Application(APP_PATH . "/conf/application.ini" , "test");
$app->bootstrap()->run();
