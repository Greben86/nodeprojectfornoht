<?php

class Admin_IndexController extends Zend_Controller_Action
{
    public function init()
    {
        // Установим свой макет
        $this->_helper->layout->setLayout('admin');
    }
    
    public function preDispatch() 
    {       
        // Проверяем аутентификацию
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('/admin/login');
        }
        
        return parent::preDispatch();
    }

    public function indexAction()
    {       
        // Подключаемся к БД
        $db = Zend_Db::factory('Pdo_Mysql', array(
            'host'     => '127.0.0.1',
            'username' => 'root',
            'password' => '123',
            'dbname'   => 'webshop'
        ));

        $result = $db->fetchAll('SELECT * FROM statements ORDER BY date_doc DESC');

        $this->view->records = $result;
    }
    
    public function resourcesAction() 
    {      
        // Подключаемся к БД
        $db = Zend_Db::factory('Pdo_Mysql', array(
            'host'     => '127.0.0.1',
            'username' => 'root',
            'password' => '123',
            'dbname'   => 'webshop'
        ));

        $result = $db->fetchAll('SELECT * FROM resources ORDER BY id DESC');

        $this->view->records = $result;
    }

}

