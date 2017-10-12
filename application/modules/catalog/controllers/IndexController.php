<?php

class Catalog_IndexController extends Zend_Controller_Action
{    
    private $_config;
    
    public function init()
    {
        $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
        $this->_config = new Zend_Config_Ini($configs['localConfigPath']);
    }
    
    private function buildBreadCrumps($id, $active)
    {
        if (($id!=='0')&&!empty($id))
        {
            // Делаем запрос к API
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $this->_config->api->host.'/goods/get/'.$id); 
            curl_setopt($ch, CURLOPT_HEADER, false); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); 
            curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie'); 
            $result = curl_exec($ch); 
            curl_close($ch);

            $folder = json_decode($result, true);
            if ($active)
            {
                return $this->buildBreadCrumps($folder['owner'], false) . 
                    '<li class="active">' . $folder['name'] . '</li>';
            } else {
                return $this->buildBreadCrumps($folder['owner'], false) . 
                    '<li><a href="/catalog/folder/' . $folder['id'] . '">' . $folder['name'] . '</a></li>';
            }
        } else {
            if ($active)
            {
                return '<li class="active"><span class="glyphicon glyphicon-home"></span></li>';
            } else {
                return '<li><a href="/catalog/folder/0"><span class="glyphicon glyphicon-home"></span></a></li>';
            }
        }
    }
    
    public function indexAction()
    {
        // Делаем запрос к API
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $this->_config->api->host.'/goods/list/0'); 
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie'); 
        $result = curl_exec($ch);
        curl_close($ch);

        $this->view->goods = json_decode($result, true);
        $this->view->crumps = $this->buildBreadCrumps(0, true);
    }
    
    public function menuAction()
    {        
        // отключение макета для данного действия
        $this->_helper->layout->disableLayout(true);
        // Делаем запрос к API
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $this->_config->api->host.'/goods/folders/0'); 
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie'); 
        $result = curl_exec($ch);
        curl_close($ch);

        $this->view->folders = json_decode($result, true);
    }
    
    public function menunodeAction()
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
        
        // отключение макета для данного действия
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout(true);
        $this->getResponse()->setHeader('Content-type', 'application/json;charset=UTF-8');
        
        // Делаем запрос к API
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $this->_config->api->host.'/goods/folders/'.$input->id); 
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie'); 
        $this->getResponse()->setHttpResponseCode(200);
        $this->getResponse()->setBody(curl_exec($ch));
        curl_close($ch);
    }
    
    public function folderAction()
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
        
        if (empty($input->id)||($input->id==='0')) {
            $this->redirect('/catalog');
        } else {
            // Делаем запрос к API
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $this->_config->api->host.'/goods/get/'.$input->id); 
            curl_setopt($ch, CURLOPT_HEADER, false); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); 
            curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie'); 
            $result = curl_exec($ch); 
            curl_close($ch);

            $this->view->folder = json_decode($result, true);
            $this->view->crumps = $this->buildBreadCrumps($input->id, true);
        }

        // Делаем запрос к API
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $this->_config->api->host.'/goods/list/'.$input->id); 
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
        curl_setopt($ch, CURLOPT_URL, $this->_config->api->host.'/goods/get/'.$input->id); 
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie'); 
        $result = curl_exec($ch); 
        curl_close($ch);

        $this->view->item = json_decode($result, true);
        $this->view->crumps = $this->buildBreadCrumps($this->view->item['owner'], false);
    }
}