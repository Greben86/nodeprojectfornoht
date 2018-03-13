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
        curl_setopt($ch, CURLOPT_URL, $this->_config->api->host.'/goods/list/-1'); 
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie'); 
        $result = curl_exec($ch);        
        $goods = json_decode($result, true);        
        $config = $this->getInvokeArg('bootstrap')->getOption('indexes');
        Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive());
        $index = Zend_Search_Lucene::create($config['indexPath']);
        if (count($goods)) {
            foreach ($goods as $g) {
                if (!$g['folder']||empty($g['folder'])) {
                    $doc = new Zend_Search_Lucene_Document();
                    $doc->addField(Zend_Search_Lucene_Field::keyword('RecordID', $g['id'], 'UTF-8'));
                    $doc->addField(Zend_Search_Lucene_Field::text('name', $g['name'], 'UTF-8'));
                    $doc->addField(Zend_Search_Lucene_Field::text('description', $g['description'], 'UTF-8'));
                    $doc->addField(Zend_Search_Lucene_Field::text('article', $g['article'], 'UTF-8'));
                    $doc->addField(Zend_Search_Lucene_Field::unIndexed('instock', $g['instock'], 'UTF-8'));
                    $doc->addField(Zend_Search_Lucene_Field::unIndexed('price', $g['price'], 'UTF-8'));
                    $index->addDocument($doc);
                }
            }
        }
        $index->commit();
        $this->view->count = $index->count();     
        curl_close($ch);
    }
}
