<?php

class My_Controller_Action_Helper_Navigation extends 
    Zend_Controller_Action_Helper_Abstract
{
    protected $_container;

    // Конструктор, устанавливает навигационный контейнер
    public function __construct(Zend_Navigation $container = null)
    {
        if (null !== $container) 
        {
            $this->_container = $container;
        }
    }

    // Прверяет текущий запрос и устанавливает активную страницу
    public function preDispatch()
    {
        // Получаем текущий путь
        $req_uri = $this->getRequest()->getRequestUri();
        // Обработка пустого занчения
        if ($req_uri === '/')
        {
            $req_uri = '/home';
        }
        // Ищем контейнер и делаем его активным
        $uri = $this->getContainer()
            ->findBy('uri', $req_uri);
        if (!is_null($uri))
        {
            $uri->active = true;
        }
    }
    
    // Получение навигационного контйнера
    public function getContainer()
    {
        if (null === $this->_container) 
        {
            $this->_container = Zend_Registry::get('Zend_Navigation');
        }
        if (null === $this->_container) 
        {
            throw new RuntimeException ('Navigation container unavailable');
        }
        return $this->_container;
    }
}

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{    
    
    protected function _initNavigation()
    {
        $config = new Zend_Config_Xml(
                APPLICATION_PATH . '/configs/navigation.xml');
        $container = new Zend_Navigation($config);
        
        $registry = Zend_Registry::getInstance();
        $registry->set('Zend_Navigation', $container);
        
        Zend_Controller_Action_HelperBroker::addHelper( 
            new My_Controller_Action_Helper_Navigation() 
        );
    }
    
}

