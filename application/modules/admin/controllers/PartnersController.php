<?php

class Partner_Form_Add extends Zend_Form {
    
    public function init() {
        // инициализируем форму
        $this->setAction('/admin/partners/add')
             ->setMethod('post');
        
        // создаем текстовое поле для ввода описания        
        $note = new Zend_Form_Element_Textarea('note');
        $note -> setLabel('Краткое описание')
              ->setAttribs(array(
                'class' => 'form-control',
                'placeholder'  => 'Описание партнера',
              ))
              -> setOptions(array('rows' => '5', 'cols' => '40'))
              -> setRequired(true)  
              -> addValidator('NotEmpty', true, array(
                  Zend_Validate_NotEmpty::IS_EMPTY => 'Заполните описание партнера'
              ))
              -> addFilter('StringTrim')
              -> addFilter('StripTags');
        
        // создаем текстовое поле для ввода названия
        $link = new Zend_Form_Element_Text('link');
        $link -> setLabel('Ссылка')
              ->setAttribs(array(
                'class' => 'form-control',
                'placeholder'  => 'Ссылка на сайт',
              ))
              -> setOptions(array('size' => '100'))
              -> setRequired(true)  
              -> addValidator('NotEmpty', true)
              -> addFilter('HtmlEntities')
              -> addFilter('StringTrim');
        
        $discount = new Zend_Form_Element_Text('discount');
        $discount -> setLabel('Скидка')
              ->setAttribs(array(
                'class' => 'form-control',
                'placeholder'  => 'Укажите максимальную скидку',
              ))
              -> setOptions(array('size' => '100'))
              -> setRequired(true)  
              -> addValidator('NotEmpty', true)
              -> addFilter('HtmlEntities')
              -> addFilter('StringTrim');
        
        // создаем текстовое поле для ввода названия
        $file = new Zend_Form_Element_File('file');
        $file->setLabel('Файл логотипа')
            ->setDestination(APPLICATION_PATH . '/../public/img/partners')
            ->setAttrib('multiple', false)
            ->addValidator('Size', false, 2e+7)
            ->addValidator('Extension', false, 'jpg,png,gif,bmp,tiff');
        
        
        
        // создаем кнопку отправки
        $submit = new Zend_Form_Element_Submit('submit');
        $submit -> setLabel('Добавить')
                -> setOptions(array('class' => 'btn btn-primary'))
                -> setDecorators(array(
                    array('ViewHelper'),
                    array('HtmlTag', array('tag' => 'div', 'class' => 'form-actions'))
                ));
        
        // добавляем элементы к форме
        $this -> addElement($note)
              -> addElement($link)
              -> addElement($discount)
              -> addElement($file);
        
        $this->addDisplayGroup(array('note', 'link', 'discount', 'file'), 'partner');
        $this->getDisplayGroup('partner')
             ->setLegend('Новый партнер');
        $this->addElement($submit);
    }
}

class Partner_Form_Update extends Partner_Form_Add {
    
    public function init() {
        parent::init();
        
        // инициализируем форму
        $this->setAction('/admin/partners/edit')
             ->setMethod('post');

        $this->removeElement('submit');
        $this->removeDisplayGroup('partner');
        
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
        
        $this->addDisplayGroup(array('id', 'note', 'link', 'discount', 'file'), 'partner');
        $this->getDisplayGroup('partner')
             ->setLegend('Партнер');
        $this->addElement($submit);
    }
}

class Admin_PartnersController extends Zend_Controller_Action
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

        $result = $db->fetchAll('SELECT * FROM partners ORDER BY id DESC');

        $this->view->records = $result;
    }
    
    public function addAction() 
    {      
        // генерируем форму ввода       
        $form = new Partner_Form_Add();
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
                    'note'      => $values['note'],
                    'link'      => $values['link'],
                    'file'      => $values['file'],
                    'discount'  => $values['discount']
                );
                // Сохраняем данные
                $db->insert('partners', $data);
                
                $this->_helper->getHelper('FlashMessenger')->addMessage('Партнер сохранен');
                $this->_redirect('/admin/partners');
            }
        }
        $this->render('edit');
    }
    
    public function editAction()
    {
        // генерируем форму ввода       
        $form = new Partner_Form_Update();
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
                    'note'      => $values['note'],
                    'link'      => $values['link'],
                    'file'      => $values['file'],
                    'discount'  => $values['discount']
                );
                // Сохраняем данные
                $db->update('partners', $data, 'id='.$values['id']);
                
                $this->_helper->getHelper('FlashMessenger')->addMessage('Программа сохранена');
                $this->_redirect('/admin/partners');                
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

                $result = $db->fetchAll('SELECT * FROM partners WHERE id='.$input->id);
                
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
            $db->delete('partners', 'id='.$input->id);
            
            $this->_helper->getHelper('FlashMessenger')->addMessage('Партнер удален');
            $this->_redirect('/admin/partners');
        }
    }

}

