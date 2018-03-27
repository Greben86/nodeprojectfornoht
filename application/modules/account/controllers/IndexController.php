<?php

class Account_IndexController extends Zend_Controller_Action
{
    public function preDispatch() {
        // Проверяем аутентификацию
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('/account/login');
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
            'charset'  => 'utf8',
            'username' => $localConfig->database->user,
            'password' => $localConfig->database->pass
        ));

        $result = $db->fetchAll("SELECT * FROM `customers` WHERE `email`='".Zend_Auth::getInstance()->getIdentity()."' or `number`='".Zend_Auth::getInstance()->getIdentity()."'");

        $this->view->resources = $result[0];
    }

}

