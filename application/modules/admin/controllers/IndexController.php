<?php

class Admin_IndexController extends Zend_Controller_Action
{

    public function init()
    {
    /* Initialize action controller here */
    }

    public function indexAction()
    {
        // отключение макета для данного действия
//        $this->_helper->layout->disableLayout();
        $this->_helper->layout->setLayout('admin');
        
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

}

