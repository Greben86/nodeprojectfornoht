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
            'host'     => 'localhost',
            'username' => 'webapp',
            'password' => 'ros1nf0rm',
            'dbname'   => 'webshop'
        ));

        $result = $db->fetchAll('SELECT * FROM statements ORDER BY date_doc DESC');

        $this->view->records = $result;
    }

}

