<?php

class Bootstrap extends Yaf_Bootstrap_Abstract
{
        public function _initConfig() 
        {
                $config = Yaf_Application::app()->getConfig();
                Yaf_Registry::set("config", $config);
        }

        public function _initDefaultName(Yaf_Dispatcher $dispatcher) 
        {
        	$dispatcher->setDefaultModule("Index")->setDefaultController("Index")->setDefaultAction("index");
        }
        
        public function _initView(Yaf_Dispatcher $dispatcher)
        {
        	$dispatcher->setView(new Layout(Yaf_Application::app()->getConfig()->application->layout->directory));
        }
        
        public function _initRoute(Yaf_Dispatcher $dispatcher) {
        	$router = Yaf_Dispatcher::getInstance()->getRouter();
        }
        
        public function _initPlugin(Yaf_Dispatcher $dispatcher) {
        	/* register a plugin */
        	$dispatcher->registerPlugin(new UserPermission());
        }
}