<?php

class Resource_Form_Add extends Zend_Dojo_Form {
    
    public function init() {
        // инициализируем форму
        $this->setAction('/admin/resources/add')
             ->setMethod('post');
        
        // создаем текстовое поле для ввода названия
        $name = new Zend_Form_Element_Text('name');
        $name -> setLabel('Название')
              ->setAttribs(array(
                'class' => 'form-control',
                'placeholder'  => 'Укажите название программы',
              ))
              -> setOptions(array('size' => '35'))
              -> setRequired(true)  
              -> addValidator('NotEmpty', true)
              -> addFilter('HtmlEntities')
              -> addFilter('StringTrim');
        
        // создаем текстовое поле для ввода        
        $note = new Zend_Form_Element_Textarea('note');
        $note -> setLabel('Краткое описание')
              ->setAttribs(array(
                'class' => 'form-control',
                'placeholder'  => 'Описание программы',
              ))
              -> setOptions(array('rows' => '2', 'cols' => '40'))
              -> setRequired(true)  
              -> addValidator('NotEmpty', true, array(
                  Zend_Validate_NotEmpty::IS_EMPTY => 'Заполните описание программы'
              ))
              -> addFilter('HtmlEntities')
              -> addFilter('StringTrim');
        
        $body = new Zend_Dojo_Form_Element_Editor('body');
        $body->setLabel('Полное описание');
        
        // создаем кнопку отправки
        $submit = new Zend_Form_Element_Submit('submit');
        $submit -> setLabel('Добавить')
                -> setOptions(array('class' => 'btn btn-primary'))
                -> setDecorators(array(
                    array('ViewHelper'),
                    array('HtmlTag', array('tag' => 'div', 'class' => 'form-actions'))
                ));
        
        // добавляем элементы к форме
        $this -> addElement($name)
              -> addElement($note)
              -> addElement($body);
        
        $this->addDisplayGroup(array('name', 'note', 'body'), 'resource');
        $this->getDisplayGroup('resource')
             ->setLegend('Новая программа');
        $this->addElement($submit);
    }
}

class Resource_Form_Update extends Resource_Form_Add {
    
    public function init() {
        parent::init();
        
        // инициализируем форму
        $this->setAction('/admin/resources/edit')
             ->setMethod('post');

        $this->removeElement('submit');
        $this->removeDisplayGroup('resource');
        
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
        
        $this->addDisplayGroup(array('id', 'name', 'note', 'body'), 'resource');
        $this->getDisplayGroup('resource')
             ->setLegend('Программа');
        $this->addElement($submit);
    }
}

class Admin_ResourcesController extends Zend_Controller_Action
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
        $db = Zend_Db::factory('Pdo_Mysql', array(
            'host'     => '127.0.0.1',
            'username' => 'root',
            'password' => '123',
            'dbname'   => 'webshop'
        ));

        $result = $db->fetchAll('SELECT * FROM resources ORDER BY id DESC');

        $this->view->records = $result;
    }
    
    public function addAction() 
    {      
        // генерируем форму ввода       
        $form = new Resource_Form_Add();
        $this->view->form = $form;
        
        // проверяем корректность введенных данных
        // если они корректны заполняем модель
        // присваиваем некоторым из полей значения по умолчанию
        // сохраняем в БД
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                
                // Подключаемся к БД
                $db = Zend_Db::factory('Pdo_Mysql', array(
                    'host'     => '127.0.0.1',
                    'username' => 'root',
                    'password' => '123',
                    'dbname'   => 'webshop'
                ));
                // Формируем массив данных
                $data = array(
                    'name'      => $values['name'],
                    'note'      => $values['note'],
                    'body'      => $values['body']
                );
                // Сохраняем данные
                $db->insert('resources', $data);
                
                $this->_helper->getHelper('FlashMessenger')->addMessage('Программа сохранена');
                $this->_redirect('/admin/resources');
            }
        }
    }
    
    public function editAction()
    {
        // генерируем форму ввода       
        $form = new Resource_Form_Update();
        $this->view->form = $form;
        
        // проверяем корректность введенных данных
        // если они корректны заполняем модель
        // присваиваем некоторым из полей значения по умолчанию
        // сохраняем в БД
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                
                // Подключаемся к БД
                $db = Zend_Db::factory('Pdo_Mysql', array(
                    'host'     => '127.0.0.1',
                    'username' => 'root',
                    'password' => '123',
                    'dbname'   => 'webshop'
                ));
                // Формируем массив данных
                $data = array(
                    'name'      => $values['name'],
                    'note'      => $values['note'],
                    'body'      => $values['body']
                );
                // Сохраняем данные
                $db->update('resources', $data, $values['id']);
                
                $this->_helper->getHelper('FlashMessenger')->addMessage('Программа сохранена');
                $this->_redirect('/admin/resources');                
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
                $db = Zend_Db::factory('Pdo_Mysql', array(
                    'host'     => '127.0.0.1',
                    'username' => 'root',
                    'password' => '123',
                    'dbname'   => 'webshop'
                ));

                $result = $db->fetchAll('SELECT * FROM resources WHERE id='.$input->id);
                
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
            $db = Zend_Db::factory('Pdo_Mysql', array(
                'host'     => '127.0.0.1',
                'username' => 'root',
                'password' => '123',
                'dbname'   => 'webshop'
            ));
            // Удаляем данные
            $db->delete('resources', 'id='.$input->id);
            
            $this->_helper->getHelper('FlashMessenger')->addMessage('Программа удалена');
            $this->_redirect('/admin/resources');
        }
    }

}

