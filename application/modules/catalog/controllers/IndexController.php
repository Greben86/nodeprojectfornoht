<?php

require_once 'PHPMorphy/PHPMorphy_TokenFilter.php';
require_once 'PHPMorphy/MyUtf8.php';


class Catalog_IndexController extends Zend_Controller_Action
{    
    private $_config;
    
    public function init()
    {
        $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
        $this->_config = new Zend_Config_Ini($configs['localConfigPath']);
    }
    
    private function buildBreadCrumps($ch, $id, $active)
    {
        if (($id!=='0')&&!empty($id))
        {
            // Делаем запрос к API
            curl_setopt($ch, CURLOPT_URL, $this->_config->api->host.'/goods/get/'.$id); 
            $result = curl_exec($ch); 

            $folder = json_decode($result, true);
            if ($active)
            {
                return $this->buildBreadCrumps($ch, $folder['owner'], false) . 
                    '<li class="active">' . $folder['name'] . '</li>';
            } else {
                return $this->buildBreadCrumps($ch, $folder['owner'], false) . 
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

        $this->view->imagehost = $this->_config->api->host.'/goods/image/';
        $this->view->goods = json_decode($result, true);
        $this->view->crumps = $this->buildBreadCrumps($ch, 0, true);
        
        curl_close($ch);
    }
    
    private function buildSubmenu($ch, $domDoc, $parent, $folder) {
        // Делаем запрос к API
        curl_setopt($ch, CURLOPT_URL, $this->_config->api->host.'/goods/folders/'.$folder); 
        $result = curl_exec($ch);
        
        $folders = json_decode($result, true);
        if (!empty($folders)) {
            foreach ($folders as $f) {
                $listitem = $domDoc->createElement( 'li' );
                $listitem->setAttribute( 'id', $f['id'] );
                $listitem->setAttribute( 'class', 'Node ExpandClosed' );

                $expand = $domDoc->createElement( 'div' );
                $expand->setAttribute('class', 'Expand');
                $expand->setAttribute('onclick', 'clickExpand()');

                $link = $domDoc->createElement( 'a', $f['name'] );
                $link->setAttribute( 'href', '/catalog/folder/'.$f['id'] );

                $submenu = $domDoc->createElement( 'ul' );
                $submenu->setAttribute( 'class', 'Container' );

                $listitem->appendChild($expand);
                $listitem->appendChild($link);
                $listitem->appendChild($submenu);

                $parent->appendChild($listitem);
            }
            return true;
        } else {
            return false;
        }        
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
        
        $this->view->folders = json_decode($result, true);
        
//        $domDoc = new DOMDocument();
//        $menu = $domDoc->createElement( 'ul' );
//        $menu->setAttribute( 'id', 'tree' );
//        $menu->setAttribute( 'class', 'Container' );
//        
//        $folders = json_decode($result, true);
//        foreach ($folders as $f) {
//            $listitem = $domDoc->createElement( 'li' );
//            $listitem->setAttribute( 'data-submenu-id', 'submenu-'.$f['id'] );
//            $listitem->setAttribute( 'class', 'catalog_sidebar_node' );
//            
//            $link = $domDoc->createElement( 'a', $f['name'] );
//            $link->setAttribute( 'href', '/catalog/folder/'.$f['id'] );
//            
//            $panel = $domDoc->createElement( 'div' );
//            $panel->setAttribute( 'id', 'submenu-'.$f['id'] );
//            $panel->setAttribute('class', 'popover');
//            
//            $titlepanel = $domDoc->createElement( 'h3', $f['name'] );
//            $titlepanel->setAttribute('class', 'popover-title');
//            
//            $submenu = $domDoc->createElement( 'ul' );
//            $submenu->setAttribute( 'class', 'Container' );
//            
//            if ($this->buildSubmenu($ch, $domDoc, $submenu, $f['id'])) {            
//                $panel->appendChild($titlepanel);
//                $panel->appendChild($submenu);                
//                $listitem->appendChild($panel);                
//            }
//            $listitem->appendChild($link);
//            $menu->appendChild($listitem);
//        }
//        $listitem = $domDoc->createElement( 'li', 'Еще' );
//        $listitem->setAttribute( 'class', 'catalog_sidebar_divider' );
//        $menu->appendChild($listitem);
//        $domDoc->appendChild( $menu );
//        
//        $this->view->menu = $domDoc->saveHTML();
        
        curl_close($ch);
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
            'id' => array('HtmlEntities', 'StripTags', 'StringTrim'),
            'page' => array('HtmlEntities', 'StripTags', 'StringTrim')
        );
        $validators = array(
            'id' => array('NotEmpty', 'Int'),
            'page' => array('Int')
        );
        
        $input = new Zend_Filter_Input($filters, $validators);
        $input -> setData($this->getRequest()->getParams());
        
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie'); 
        if (empty($input->id)||($input->id==='0')) {
            $this->redirect('/catalog');
        } else {
            // Делаем запрос к API            
            curl_setopt($ch, CURLOPT_URL, $this->_config->api->host.'/goods/get/'.$input->id);            
            $result = curl_exec($ch);            

            $this->view->folder = json_decode($result, true);
            $this->view->crumps = $this->buildBreadCrumps($ch, $input->id, true);
        }

        // Делаем запрос к API
        curl_setopt($ch, CURLOPT_URL, $this->_config->api->host.'/goods/list/'.$input->id);
        $result = curl_exec($ch); 
        curl_close($ch);

        $this->view->imagehost = $this->_config->api->host.'/goods/image/';
        $this->view->goods = json_decode($result, true);
        $paginator = Zend_Paginator::factory($this->view->goods);
        $paginator->setItemCountPerPage(15);
        $paginator->setDefaultPageRange(7);
        $paginator->setCurrentPageNumber($input->page);
        $this->view->paginator = $paginator;
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

        $this->view->imagehost = $this->_config->api->host.'/goods/image/';
        $this->view->item = json_decode($result, true);
        $this->view->crumps = $this->buildBreadCrumps($ch, $this->view->item['owner'], false);
        
        curl_close($ch);
    }
    
    public function searchAction() {
        // Устанавливаем фильтры и валидаторы для данных, полученных из POST
        $filters = array(
            'page' => array('HtmlEntities', 'StripTags', 'StringTrim')
        );
        $validators = array(
            'page' => array('Int')
        );
        
        $input = new Zend_Filter_Input($filters, $validators);
        $input -> setData($this->getRequest()->getParams());
        
        if ($this->getRequest()->isGet()) {
            $data = $this->getRequest()->getParams();
            if (!empty($data['query'])) {
                $config = $this->getInvokeArg('bootstrap')->getOption('indexes');
                $analyzer = new My_Analysis_Analyzer_Common_Utf8();
                $analyzer->addFilter(new Zend_Search_Lucene_Analysis_TokenFilter_LowerCase());
                $analyzer->addFilter(new Zend_Search_Lucene_Analysis_TokenFilter_ShortWords());
//                $analyzer->addFilter(new PHPMorphy_TokenFilter());
                Zend_Search_Lucene_Analysis_Analyzer::setDefault($analyzer);
                Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('UTF-8');
                $index = Zend_Search_Lucene::open($config['indexPath']);
                $query = Zend_Search_Lucene_Search_QueryParser::parse($data['query']);
                $result = $index->find($query, 'name', SORT_STRING);
                $this->view->query = $data['query'];
                $this->view->queryobj = $query;
                $this->view->imagehost = $this->_config->api->host.'/goods/image/';
                $paginator = Zend_Paginator::factory($result);
                $paginator->setItemCountPerPage(15);
                $paginator->setDefaultPageRange(7);
                $paginator->setCurrentPageNumber($input->page);
                $this->view->result = $result;
                $this->view->paginator = $paginator;
            }
        }
    }
}