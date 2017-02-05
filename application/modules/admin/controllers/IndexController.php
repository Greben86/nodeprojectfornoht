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
        $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
        $localConfig = new Zend_Config_Ini($configs['localConfigPath']);
        $db = Zend_Db::factory('Pdo_Mysql', array(
            'host'     => $localConfig->database->host,
            'dbname'   => $localConfig->database->name,
            'username' => $localConfig->database->user,
            'password' => $localConfig->database->pass
        ));

        $result = $db->fetchAll('SELECT * FROM statements ORDER BY date_doc DESC');

        $this->view->records = $result;
    }

}

