<?php

class Basket_IndexController extends Zend_Controller_Action
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
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            if (isset($data['remove'])) {
                foreach ($data['remove'] as $r) {
                    // Удаляем данные
                    $db->delete('basket', 'id='.$r);
                }
            }
            if (isset($data['count'])) {
                foreach ($data['count'] as $key => $value) {
                    $db->update('basket', array('count'=>$value), 'id='.$key);
                }
            }
        }
        
        $result = $db->fetchAll(
                "SELECT b.id, b.good, g.name, g.price, g.instock, b.count ".
                "FROM `basket` b ".
                "   INNER JOIN `goods` g ON (g.id=b.good) ".
                "WHERE `session`='".Zend_Session::getId()."'".
                "ORDER BY g.name DESC");

        $this->view->records = $result;
        
        $summa = 0.0;
        if (count($result)) {
            foreach ($result as $r) {
                $summa = $summa + ($r['count'] * $r['price']);
            }
        }
        $this->view->summa = money_format('%i', $summa);
    }
    
    public function addAction()
    {        
        // устанавливаем фильтры и валидаторы для входных данных
        // полученных в запросе
        $filters = array(
            'id' => array('StripTags', 'StringTrim'),
            'count' => array('StripTags', 'StringTrim')
        );
        $validators = array(
            'id' => array('NotEmpty', 'Int'),
            'count' => array('Digits')
        );

        // проверяем корректность входных данных
        // получаем запрошенную запись
        // добавляем ее к представлению
        $input = new Zend_Filter_Input($filters, $validators);
        $input->setData($this->getRequest()->getParams());
        if ($input->isValid()) {
            // Подключаемся к БД
            $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
            $localConfig = new Zend_Config_Ini($configs['localConfigPath']);
            $db = Zend_Db::factory('Pdo_Mysql', array(
                'host'     => $localConfig->database->host,
                'dbname'   => $localConfig->database->name,
                'username' => $localConfig->database->user,
                'password' => $localConfig->database->pass
            ));

            // Формируем массив данных
            if (Zend_Session::sessionExists()) {
                $result = $db->fetchAll(
                "SELECT * FROM `basket` WHERE `good`=".$input->id." and `session`='".Zend_Session::getId()."'");
                if (count($result)) {
                    $data = array(
                        'count'     => $result[0]['count'] + $input->count
                    );
                    $db->update('basket', $data, "`good`=".$input->id." and `session`='".Zend_Session::getId()."'");
                } else {
                    $data = array(
                        'good'      => $input->id,
                        'session'   => Zend_Session::getId(),
                        'count'     => $input->count
                    );
                    $db->insert('basket', $data);
                }
            }
        }
        $this->redirect('/basket/widget');
    }
    
    public function updateAction()
    {
        // устанавливаем фильтры и валидаторы для входных данных
        // полученных в запросе
        $filters = array(
            'id' => array('StripTags', 'StringTrim')
        );
        $validators = array(
            'id' => array('NotEmpty', 'Int')
        );

        // проверяем корректность входных данных
        // получаем запрошенную запись
        // добавляем ее к представлению
        $input = new Zend_Filter_Input($filters, $validators);
        $input->setData($this->getRequest()->getParams());
        if ($input->isValid()) {
            // Подключаемся к БД
            $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
            $localConfig = new Zend_Config_Ini($configs['localConfigPath']);
            $db = Zend_Db::factory('Pdo_Mysql', array(
                'host'     => $localConfig->database->host,
                'dbname'   => $localConfig->database->name,
                'username' => $localConfig->database->user,
                'password' => $localConfig->database->pass
            ));

            // Формируем массив данных
            if (Zend_Session::sessionExists()) {
                $result = $db->fetchAll(
                "SELECT * FROM `basket` WHERE `good`=".$input->id." and `session`='".Zend_Session::getId()."'");
                if (count($result)) {
                    if ($result[0]['count']>1) {
                        $data = array(
                            'count'     => $result[0]['count'] - 1
                        );
                        $db->update('basket', $data, "`good`=".$input->id." and `session`='".Zend_Session::getId()."'");
                    } else {
                        $db->delete('basket', "`good`=".$input->id." and `session`='".Zend_Session::getId()."'");
                    }
                }
            }
        }
        $this->redirect('/basket');
    }
    
    public function removeAction() 
    {
        // устанавливаем фильтры и валидаторы для входных данных
        // полученных в запросе
        $filters = array(
            'id' => array('StripTags', 'StringTrim')
        );
        $validators = array(
            'id' => array('NotEmpty', 'Int')
        );

        // проверяем корректность входных данных
        // получаем запрошенную запись
        // добавляем ее к представлению
        $input = new Zend_Filter_Input($filters, $validators);
        $input->setData($this->getRequest()->getParams());
        if ($input->isValid()) {
            // Подключаемся к БД
            $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
            $localConfig = new Zend_Config_Ini($configs['localConfigPath']);
            $db = Zend_Db::factory('Pdo_Mysql', array(
                'host'     => $localConfig->database->host,
                'dbname'   => $localConfig->database->name,
                'username' => $localConfig->database->user,
                'password' => $localConfig->database->pass
            ));

            // Удаляем данные
            $db->delete('basket', 'id='.$input->id);
        }
        $this->redirect('/basket');
    }
    
    public function sendAction()
    {
        $this->sendMail('Стол заказов', $this->buildBody());        
        $this->_redirect('/basket/success');
    }
    
    public function successAction()
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


        // Удаляем данные
        $db->delete('basket', "session='".Zend_Session::getId()."'");
    }
    
    private function buildBody()
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
        
        $result = $db->fetchAll("SELECT * FROM `customers` WHERE `number`='".Zend_Auth::getInstance()->getIdentity()."'");
        
        $domDoc = new DOMDocument();
        $title = $domDoc->createElement( 'h2', 'Заказ товаров' );
        $person = $domDoc->createElement( 'h3', $result[0]['name'] . ' (' . $result[0]['number'].')');

        $result = $db->fetchAll(
                "SELECT b.id, b.good, g.name, g.price, b.count ".
                "FROM `basket` b ".
                "   INNER JOIN `goods` g ON (g.id=b.good) ".
                "WHERE `session`='".Zend_Session::getId()."'".
                "ORDER BY g.name DESC");
        
        
        $domDoc->appendChild($title);
        $domDoc->appendChild($person);
        
        $table = $domDoc->createElement( 'table' );
        $table->setAttribute('border', '1');
        $thead = $domDoc->createElement( 'thead' );
        $tr = $domDoc->createElement( 'tr' );          
            
        $th1 = $domDoc->createElement( 'th', 'Код' );
        $th2 = $domDoc->createElement( 'th', 'Товар' );
        $th3 = $domDoc->createElement( 'th', 'Количество' );
        $th4 = $domDoc->createElement( 'th', 'Цена' );   
        $th5 = $domDoc->createElement( 'th', 'Сумма' );

        $tr->appendChild($th1);
        $tr->appendChild($th2);
        $tr->appendChild($th3);
        $tr->appendChild($th4);
        $tr->appendChild($th5);
        $thead->appendChild($tr);
        $table->appendChild($thead);
        $tbody = $domDoc->createElement( 'tbody' );
        $summa = 0.0;
        foreach ($result as $r) {
            $tr = $domDoc->createElement( 'tr' );           
            
            $td1 = $domDoc->createElement( 'td', $r['good'] );
            $td2 = $domDoc->createElement( 'td', $r['name'] );
            $td3 = $domDoc->createElement( 'td', $r['count'] );
            $td4 = $domDoc->createElement( 'td', money_format('%i', $r['price']) );   
            $td5 = $domDoc->createElement( 'td', money_format('%i', $r['count'] * $r['price']) );
            
            $tr->appendChild($td1);
            $tr->appendChild($td2);
            $tr->appendChild($td3);
            $tr->appendChild($td4);
            $tr->appendChild($td5);
            $tbody->appendChild($tr); 
            
            $summa = $summa + ($r['count'] * $r['price']);
        }
        $tr = $domDoc->createElement( 'tr' );
        $td1 = $domDoc->createElement( 'td', 'Итого' );
        $td1->setAttribute('colspan', '4');
        $td2 = $domDoc->createElement( 'td', money_format('%i', $summa) );
        $tr->appendChild($td1);
        $tr->appendChild($td2);
        $tbody->appendChild($tr);
        
        $table->appendChild($tbody);
        $domDoc->appendChild($table);
        return $domDoc->saveHTML();
    }
    
    private function sendMail($subject, $body)
    {
        $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
        $localConfig = new Zend_Config_Ini($configs['localConfigPath']);
        $config = array(
            'ssl' => 'ssl',             
            'port' => $localConfig->email->port,
            'auth' => 'login',
            'username' => $localConfig->email->user,
            'password' => $localConfig->email->pass
        );       

        $mail = new Zend_Mail();
        $mail->setBodyHtml($body);
        $mail->setFrom($localConfig->email->address, 'Система регистрации заказов');
        //$mail->addTo('vygodno.vmeste@yandex.ru', 'Администратор кооператива');
        $mail->addTo('grebenvictor@yandex.ru', 'Разработчик');
        $mail->setSubject($subject);

        $transport = new Zend_Mail_Transport_Smtp($localConfig->email->host, $config);
        
        $mail->send($transport);
    }
}