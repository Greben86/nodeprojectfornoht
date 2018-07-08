<?php

class Catalog_AdminController extends Zend_Controller_Action
{    
    private $_config;
    
    public function init()
    {
        $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
        $this->_config = new Zend_Config_Ini($configs['localConfigPath']);
    }
    
    public function createFulltextIndexAction()
    {
        // отключение макета для данного действия
        $this->_helper->layout->disableLayout(true);
        // Делаем запрос к API
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $this->_config->api->host.'/search/createindex'); 
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie'); 
        $this->view->result = curl_exec($ch);    
        curl_close($ch);
    }
}
