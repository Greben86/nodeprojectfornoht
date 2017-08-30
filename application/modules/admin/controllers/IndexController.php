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
        if (Zend_Auth::getInstance()->getIdentity() != 'admin')
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
    
    public function updateAction()
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
        
        //$db->beginTransaction();
        // Создаем БД если нету
        $db->query('CREATE DATABASE IF NOT EXISTS `webshop` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;');
        // Создаем таблицу партнеров
        $db->query('DROP TABLE IF EXISTS `partners`;');
        $db->query('CREATE TABLE IF NOT EXISTS `partners` (
            `id` int(11) NOT NULL,
              `note` varchar(500) NOT NULL,
              `link` varchar(100) NOT NULL,
              `file` varchar(100) NOT NULL,
              `discount` varchar(100) NOT NULL,
              `ORDR` int(11) NOT NULL
            ); ');
        // Создаем таблицу прайс-листов
        $db->query('DROP TABLE IF EXISTS `pricelists`;');
        $db->query('CREATE TABLE IF NOT EXISTS `pricelists` (
            `id` int(11) NOT NULL,
              `name` varchar(100) NOT NULL,
              `filename` varchar(100) NOT NULL
            ); ');
        // Создаем таблицу целевых программ
        $db->query('DROP TABLE IF EXISTS `resources`;');
        $db->query('CREATE TABLE IF NOT EXISTS `resources` (
            `id` int(11) NOT NULL,
              `name` varchar(100) NOT NULL,
              `note` varchar(500) NOT NULL,
              `body` blob,
              `image` varchar(100) DEFAULT NULL,
              `page` varchar(50) NOT NULL
            ); ');
        // Создаем таблицу заявок
        $db->query('DROP TABLE IF EXISTS `statements`;');
        $db->query('CREATE TABLE IF NOT EXISTS `statements` (
            `id` int(11) NOT NULL,
              `date_doc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `email` varchar(100) NOT NULL,
              `fullname` varchar(255) NOT NULL,
              `family` varchar(100) NOT NULL,
              `name` varchar(100) NOT NULL,
              `name2` varchar(100) NOT NULL,
              `phone` varchar(100) NOT NULL,
              `note` varchar(255) NOT NULL,
              `filename` varchar(100) NOT NULL
            ); ');
        //$db->commit();
        $this->_redirect('/admin');
    }

}

