<?php

class IndexController extends Zend_Controller_Action
{    
    private $_config;
    
    public function init()
    {
        $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
        $this->_config = new Zend_Config_Ini($configs['localConfigPath']);
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

        $result = $db->fetchAll('SELECT * FROM resources ORDER BY id DESC');

        $this->view->resources = $result;
    }

    public function infoAction()
    {
        //
    }
    
    public function partnersAction()
    {        
        // Подключаемся к БД
        $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
        $localConfig = new Zend_Config_Ini($configs['localConfigPath']);
        $db = Zend_Db::factory('Pdo_Mysql', array(
            'host'     => $localConfig->database->host,
            'dbname'   => $localConfig->database->name,
            'username' => $localConfig->database->user,
            'password' => $localConfig->database->pass,
            'charset' => 'utf8'
        ));

        $result = $db->fetchAll('SELECT * FROM `partner_list` ORDER BY `ordr` ASC;');
        
//        $db1 = Zend_Db::factory('Pdo_Mysql', array(
//                    'host' => $localConfig->database->host,
//                    'dbname' => $localConfig->database->name,
//                    'username' => $localConfig->database->user,
//                    'password' => $localConfig->database->pass,
//                    'charset' => 'utf8'
//        ));
//
//        foreach ($result as $item)
//        {
//            // Формируем массив данных
//            $data = array(
//                'note' => $item['note'],
//                'link' => $item['link'],
//                'file' => $item['file'],
//                'discount' => $item['discount'],
//                'ordr' => $item['ordr']
//            );
//            // Сохраняем данные
//            $db1->insert('partner_list', $data);
//        }
        
        $this->view->imagehost = $this->_config->api->host.'/partners/image/';
        $this->view->records = $result;
    }
    
    private function get_filesize($file)
    {
        // идем файл
        $filepath = APPLICATION_PATH . '/../public/pricelists/' . $file;
        if(!file_exists($filepath)) 
        { 
            return 'Файл '.$filepath.' не найден';
        }
       // теперь определяем размер файла в несколько шагов
      $filesize = filesize($filepath);
       // Если размер больше 1 Кб
       if($filesize > 1024)
       {
           $filesize = ($filesize/1024);
           // Если размер файла больше Килобайта
           // то лучше отобразить его в Мегабайтах. Пересчитываем в Мб
           if($filesize > 1024)
           {
                $filesize = ($filesize/1024);
               // А уж если файл больше 1 Мегабайта, то проверяем
               // Не больше ли он 1 Гигабайта
               if($filesize > 1024)
               {
                   $filesize = ($filesize/1024);
                   $filesize = round($filesize, 1);
                   return $filesize." ГБ";       
               }
               else
               {
                   $filesize = round($filesize, 1);
                   return $filesize." MБ";   
               }       
           }
           else
           {
               $filesize = round($filesize, 1);
               return $filesize." Кб";   
           }  
       }
       else
       {
           $filesize = round($filesize, 1);
           return $filesize." байт";   
       }
    }
    
    public function promoAction()
    {
        // Подключаемся к БД
        $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
        $localConfig = new Zend_Config_Ini($configs['localConfigPath']);
        $db = Zend_Db::factory('Pdo_Mysql', array(
            'host'     => $localConfig->database->host,
            'dbname'   => $localConfig->database->name,
            'username' => $localConfig->database->user,
            'password' => $localConfig->database->pass,
            'charset' => 'utf8'
        ));

        $result = $db->fetchAll('SELECT * FROM `promo_list` ORDER BY `id` DESC;');
        
//        $db1 = Zend_Db::factory('Pdo_Mysql', array(
//                    'host' => $localConfig->database->host,
//                    'dbname' => $localConfig->database->name,
//                    'username' => $localConfig->database->user,
//                    'password' => $localConfig->database->pass,
//                    'charset' => 'utf8'
//        ));
// 
//        foreach ($result as $item)
//        {
//            // Формируем массив данных
//            $data = array(
//                'name' => $item['name'],
//                'filename' => $item['file']
//            );
//            // Сохраняем данные
//            $db1->insert('promo_list', $data);
//        }
        
        $promos = array();
        foreach ($result as $r) 
        {
            $promos[] = array(
                'id' => $r['id'],
                'name' => $r['name'],
                'file' => '/img/promo/' . $r['filename']
            );
        }

        $this->view->imagehost = $this->_config->api->host.'/promos/image/';
        $this->view->promos = $promos;
    }
    
    public function newsAction() 
    {
        // Делаем запрос к API
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $this->_config->api->host.'/news/list'); 
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie');
        $result = curl_exec($ch);        

        $this->view->imagehost = $this->_config->api->host.'/news/image/';
        $this->view->news = json_decode($result, true);
        
        curl_close($ch);
    }
    
    public function talesAction()
    {
        // Делаем запрос к API
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $this->_config->api->host.'/tales/list'); 
        curl_setopt($ch, CURLOPT_HEADER, false); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie');
        $result = curl_exec($ch);

        $this->view->imagehost = $this->_config->api->host.'/tales/image/';
        $this->view->tales = json_decode($result, true);
        
        curl_close($ch);
    }

    public function pricesAction()
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

        $result = $db->fetchAll('SELECT * FROM pricelists ORDER BY id DESC');
        
        $pricelists = array();
        foreach ($result as $r)
        {
            $pricelists[] = array(
                'name' => $r['name'],
                'filename' => $r['filename'],
                'filesize' => $this->get_filesize($r['filename'])
            );
        }

        $this->view->prices = $pricelists;
    }
    
    public function detailsAction()
    {
        //
    }
    
    public function aboutAction()
    {
        //
    }
}