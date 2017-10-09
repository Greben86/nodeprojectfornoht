<?php

class Catalog_IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        // Делаем запрос к API
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, 'http://localhost:8080/shop/goods/catalog'); 
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie'); 
        $result = curl_exec($ch);
        curl_close($ch);

        $this->view->goods = json_decode($result, true);
    }
    
    public function folderAction()
    {
        // Устанавливаем фильтры и валидаторы для данных, полученных из POST
        $filters = array(
            'folder' => array('HtmlEntities', 'StripTags', 'StringTrim')
        );
        $validators = array(
            'folder' => array('NotEmpty', 'Int')
        );
        
        $input = new Zend_Filter_Input($filters, $validators);
        $input -> setData($this->getRequest()->getParams());
        
        if (empty($input->folder)||($input->folder!='0')) {
            // Делаем запрос к API
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:8080/shop/goods/get/'.$input->folder); 
            curl_setopt($ch, CURLOPT_HEADER, false); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); 
            curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie'); 
            $result = curl_exec($ch); 
            curl_close($ch);

            $this->view->folder = json_decode($result, true);
        }

        // Делаем запрос к API
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, 'http://localhost:8080/shop/goods/catalog/'.$input->folder); 
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie'); 
        $result = curl_exec($ch); 
        curl_close($ch);

        $this->view->goods = json_decode($result, true);
    }
    
    public function itemAction()
    {
        // Устанавливаем фильтры и валидаторы для данных, полученных из POST
        $filters = array(
            'id' => array('HtmlEntities', 'StripTags', 'StringTrim')
        );
        $validators = array(
            'id' => array('NotEmpty', 'Int')
        );
        
        $input = new Zend_Filter_Input($filters, $validators);
        $input -> setData($this->getRequest()->getParams());
        
        // Делаем запрос к API
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, 'http://localhost:8080/shop/goods/get/'.$input->id); 
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie'); 
        $result = curl_exec($ch); 
        curl_close($ch);

        $this->view->item = json_decode($result, true);
    }
}