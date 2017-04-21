<?php

class Pricelist_Form_Add extends Zend_Form {
    
    public function init() {
        // инициализируем форму
        $this->setAction('/admin/pricelists/add')
             ->setMethod('post')
             ->setEnctype('application/x-www-form-urlencoded');
        
        // создаем текстовое поле для ввода названия
        $name = new Zend_Form_Element_Text('name');
        $name -> setLabel('Название')
              ->setAttribs(array(
                'class' => 'form-control',
                'placeholder'  => 'Укажите название прайс-листа',
              ))
              -> setOptions(array('size' => '30'))
              -> setRequired(true)  
              -> addValidator('NotEmpty', true)
              -> addFilter('StringTrim')
              -> addFilter('StripTags');
        
        $filename = new Zend_Form_Element_File('filename');
        $filename->setLabel('Файл прайс-листа')
            ->setDestination(APPLICATION_PATH . '/../public/pricelists')
            ->setAttrib('multiple', false)
            ->addValidator('Size', false, 5e+7);
        
        // создаем кнопку добавления
        $submit = new Zend_Form_Element_Submit('submit');
        $submit -> setLabel('Добавить')
                -> setOptions(array('class' => 'btn btn-primary'))
                -> setDecorators(array(
                    array('ViewHelper'),
                    array('HtmlTag', array('tag' => 'div', 'class' => 'form-actions'))
                ));
        
        // добавляем элементы к форме
        $this -> addElement($name)
              -> addElement($filename);
        
        $this->addDisplayGroup(array('name', 'filename'), 'pricelist');
        $this->getDisplayGroup('pricelist')
             ->setLegend('Новый прайс-лист');
        $this->addElement($submit);
    }
}

class Pricelist_Form_Update extends Pricelist_Form_Add {
    
    public function init() {
        parent::init();
        
        // инициализируем форму
        $this->setAction('/admin/pricelists/edit')
             ->setMethod('post');

        $this->removeElement('filename');
        $this->removeElement('submit');
        $this->removeDisplayGroup('pricelist');
        
        // создаем скрытое поле для идентификатора элемента
        $id = new Zend_Form_Element_Hidden('id');
        $id -> addValidator('Int')
            -> addFilter('StringTrim')
            -> addFilter('StripTags');
        
        // создаем кнопку отправки
        $submit = new Zend_Form_Element_Submit('submit');
        $submit -> setLabel('Сохранить')
                -> setOptions(array('class' => 'btn btn-primary'))
                -> setDecorators(array(
                    array('ViewHelper'),
                    array('HtmlTag', array('tag' => 'div', 'class' => 'form-actions'))
                ));
        
        // добавляем элементы к форме
        $this -> addElement($id);
        
        $this->addDisplayGroup(array('id', 'name', 'filename'), 'pricelist');
        $this->getDisplayGroup('pricelist')
             ->setLegend('Прайс-лист');
        $this->addElement($submit);
    }
}

class Admin_PricelistsController extends Zend_Controller_Action
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

        $result = $db->fetchAll('SELECT * FROM pricelists ORDER BY id DESC');

        $this->view->records = $result;
    }
    
    public function addAction() 
    {      
        // генерируем форму ввода       
        $form = new Pricelist_Form_Add();
        $this->view->form = $form;
        
        // проверяем корректность введенных данных
        // если они корректны заполняем модель
        // присваиваем некоторым из полей значения по умолчанию
        // сохраняем в БД
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                
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
                $data = array(
                    'name'      => $values['name'],
                    'filename'  => $values['filename']
                );
                // Сохраняем данные
                $db->insert('pricelists', $data);
                
                $this->_helper->getHelper('FlashMessenger')->addMessage('Прайс-лист сохранен');
                $this->_redirect('/admin/pricelists');
            }
        }
        $this->render('edit');
    }
    
    public function editAction()
    {
        // генерируем форму ввода       
        $form = new Pricelist_Form_Update();
        $this->view->form = $form;
        
        // проверяем корректность введенных данных
        // если они корректны заполняем модель
        // присваиваем некоторым из полей значения по умолчанию
        // сохраняем в БД
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                
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
                $data = array(
                    'name'      => $values['name']
                );
                // Сохраняем данные
                $db->update('pricelists', $data, 'id='.$values['id']);
                
                $this->_helper->getHelper('FlashMessenger')->addMessage('Прайс-лист сохранен');
                $this->_redirect('/admin/pricelists');                
            }
        } else {
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

                $result = $db->fetchAll('SELECT * FROM pricelists WHERE id='.$input->id);
                
                $this->view->form->populate($result[0]);
            }
        }
    }
    
    public function deleteAction() 
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
        
        // проверяем корректность данных
        // читаем массив идентификаторов
        // закрываем задачи
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
            // Удаляем файл
            $row = $db->fetchRow('SELECT * FROM pricelists WHERE id='.$input->id);
            unlink(APPLICATION_PATH . '/../public/pricelists/' . $row['filename']);
            
            // Удаляем данные
            $db->delete('pricelists', 'id='.$input->id);
            
            $this->_helper->getHelper('FlashMessenger')->addMessage('Прайс-лист удален');
            $this->_redirect('/admin/pricelists');
        }
    }

}

