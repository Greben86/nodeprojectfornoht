<?php

class IndexController extends Zend_Controller_Action
{
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
            'password' => $localConfig->database->pass
        ));

        $result = $db->fetchAll('SELECT * FROM partners ORDER BY ordr ASC');
        
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
            'password' => $localConfig->database->pass
        ));

        $result = $db->fetchAll('SELECT * FROM promos ORDER BY id DESC');
        
        $promos = array();
        foreach ($result as $r) 
        {
            $promos[] = array(
                'name' => $r['name'],
                'file' => '/img/promo/' . $r['file']
            );
        }

        $this->view->promos = $promos;
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