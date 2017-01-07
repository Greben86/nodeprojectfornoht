<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initNavigation()
    {
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navigation.xml');
        $container = new Zend_Navigation($config);
        
        $registry = Zend_Registry::getInstance();
        $registry->set('Zend_Navigation', $container);
    }
    
}

