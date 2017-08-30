<?php

class Promo_Form_Add extends Zend_Form {
    
    public function init() {
        // инициализируем форму
        $this->setAction('/admin/promo/add')
             ->setMethod('post');
        
        // создаем текстовое поле для ввода названия
        $name = new Zend_Form_Element_Text('name');
        $name -> setLabel('Название')
              ->setAttribs(array(
                'class' => 'form-control',
                'placeholder'  => 'Укажите название промо-акции',
              ))
              -> setOptions(array('size' => '35'))
              -> setRequired(true)  
              -> addValidator('NotEmpty', true)
              -> addFilter('StringTrim')
              -> addFilter('StripTags');
        
        $filename = new Zend_Form_Element_File('filename');
        $filename->setLabel('Изображение')
            ->setDestination(APPLICATION_PATH . '/../public/img/promo')
            ->setAttrib('multiple', false)
            ->addValidator('Size', false, 5e+7)
            ->addValidator('Extension', false, 'jpg,png,gif');
        
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
        
        $this->addDisplayGroup(array('name', 'filename'), 'promo');
        $this->getDisplayGroup('promo')
             ->setLegend('Новая промо-акция');
        $this->addElement($submit);
    }
}

class Promo_Form_Update extends Promo_Form_Add {
    
    public function init() {
        parent::init();
        
        // инициализируем форму
        $this->setAction('/admin/promo/edit')
             ->setMethod('post');

        $this->removeElement('filename');
        $this->removeElement('submit');
        $this->removeDisplayGroup('promo');
        
        // создаем скрытое поле для идентификатора элемента
        $id = new Zend_Form_Element_Hidden('id');
        $id -> addValidator('Int')
            -> addFilter('HtmlEntities')
            -> addFilter('StringTrim');
        
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
        
        $this->addDisplayGroup(array('id', 'name'), 'promo');
        $this->getDisplayGroup('promo')
             ->setLegend('Промо-акция');
        $this->addElement($submit);
    }
}

class Admin_PromoController extends Zend_Controller_Action
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
        if (Zend_Auth::getInstance()->getIdentity() != 'admin')
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

        $result = $db->fetchAll('SELECT * FROM promos ORDER BY id DESC');

        $this->view->records = $result;
    }
    
    public function addAction() 
    {      
        // генерируем форму ввода       
        $form = new Promo_Form_Add();
        $this->view->form = $form;
        
        // проверяем корректность введенных данных
        // если они корректны заполняем модель
        // присваиваем некоторым из полей значения по умолчанию
        // сохраняем в БД
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {                
                // Переименуем файл
                $originalFilename = pathinfo($form->filename->getFileName());
-               $newFilename = 'file-' . uniqid() . '.' . $originalFilename['extension'];
                $form->filename->addFilter('Rename', $newFilename);
                $form->filename->receive();
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
                    'file'      => $values['filename']
                );
                // Сохраняем данные
                $db->insert('promos', $data);
                
                $this->_helper->getHelper('FlashMessenger')->addMessage('Промо-акция сохранена');
                $this->_redirect('/admin/promo');
            }
        }
        $this->render('edit');
    }
    
    public function editAction()
    {
        // генерируем форму ввода       
        $form = new Promo_Form_Update();
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
                $db->update('promos', $data, 'id='.$values['id']);
                
                $this->_helper->getHelper('FlashMessenger')->addMessage('Промо-акция сохранена');
                $this->_redirect('/admin/promo');                
            }
        } else {
            // устанавливаем фильтры и валидаторы для входных данных
            // полученных в запросе
            $filters = array(
                'id' => array('HtmlEntities', 'StripTags', 'StringTrim')
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

                $result = $db->fetchAll('SELECT * FROM promos WHERE id='.$input->id);
                
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
            
            // Удаляем данные            
            $row = $db->delete('promos', 'id='.$input->id);
            unlink(APPLICATION_PATH . '/../public/img/promo/' . $row['file']);
            
            $this->_helper->getHelper('FlashMessenger')->addMessage('Промо-акция удалена');
            $this->_redirect('/admin/promo');
        }
    }
}

