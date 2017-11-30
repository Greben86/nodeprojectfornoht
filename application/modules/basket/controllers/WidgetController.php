<?php

class Basket_WidgetController extends Zend_Controller_Action
{    
    public function indexAction()
    {
        // отключение макета для данного действия
        $this->_helper->layout->disableLayout(true);
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

        if (Zend_Auth::getInstance()->hasIdentity()) {
            $result = $db->fetchAll(
                    "SELECT SUM(b.count) AS Count, SUM(g.price * b.count) AS Summa  ".
                    "FROM `basket` b ".
                    "   INNER JOIN `goods` g ON (g.id=b.good) ".
                    "WHERE `session`='".Zend_Session::getId()."'");

            $this->view->records = $result;
            $this->view->count = $result[0]['Count'];
            $this->view->summa = money_format('%i', $result[0]['Summa']);
        } else {
            $this->view->records = array();
            $this->view->count = 0;
            $this->view->summa = '';
        }
    }
}