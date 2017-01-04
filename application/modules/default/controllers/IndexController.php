<?php

class Default_Form_Index extends Zend_Form {
    
    public function init() {
        // инициализируем форму
        $this->setAction('/')
             ->setMethod('post');
        
        // создаем текстовое поле для ввода названия
        $family = new Zend_Form_Element_Text('family');
        $family -> setLabel('Фамилия:')
              -> setOptions(array('size' => '35'))
              -> setRequired(true)  
              -> addValidator('NotEmpty', true)
              -> addFilter('HtmlEntities')
              -> addFilter('StringTrim');
        
        // создаем текстовое поле для ввода названия
        $name = new Zend_Form_Element_Text('name');
        $name -> setLabel('Имя:')
              -> setOptions(array('size' => '35'))
              -> setRequired(true)  
              -> addValidator('NotEmpty', true)
              -> addFilter('HtmlEntities')
              -> addFilter('StringTrim');
        
        // создаем текстовое поле для ввода названия
        $name2 = new Zend_Form_Element_Text('name2');
        $name2 -> setLabel('Отчество:')
              -> setOptions(array('size' => '35'))
              -> setRequired(true)  
              -> addValidator('NotEmpty', true)
              -> addFilter('HtmlEntities')
              -> addFilter('StringTrim');
        
        // создаем текстовое поле для ввода названия
        $phone = new Zend_Form_Element_Text('phone');
        $phone -> setLabel('Телефон:')
              -> setOptions(array('size' => '50'))
              -> setRequired(true)  
              -> addValidator('NotEmpty', true)
              -> addFilter('HtmlEntities')
              -> addFilter('StringTrim');
        
        // создаем текстовое поле для ввода адреса электронной почты
        $email = new Zend_Form_Element_Text('email');
        $email -> setLabel('Электропочта:')
               -> setOptions(array('size' => '50'))
               -> setRequired(true)  
               -> addValidator('NotEmpty', true)
               -> addValidator('EmailAddress', true)
               -> addFilter('HtmlEntities')
               -> addFilter('StringToLower')
               -> addFilter('StringTrim');
        
        $image = new Zend_Form_Element_File('image');
        $image->setLabel('Скан:')
               ->addValidator('Size', false, 1024000)
               ->addValidator('Extension', false, 'jpg,png,gif');
        
        
        // создаем кнопку отправки
        $submit = new Zend_Form_Element_Submit('submit');
        $submit -> setLabel('Подать заявку')
                -> setOptions(array('class' => 'btn btn-primary'));
        
        // добавляем элементы к форме
        $this -> addElement($family)
                -> addElement($name)
                -> addElement($name2)
                -> addElement($phone)
                -> addElement($email)
                -> addElement($image);
        
        $this->addDisplayGroup(array('family', 'name', 'name2', 'phone', 'email', 'image'), 'zayavka');
        $this->getDisplayGroup('zayavka')
             ->setLegend('Заявка на вступление');
        $this->addElement($submit);
    }
}

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        // генерируем форму ввода       
        $form = new Default_Form_Index();
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {               
                $file = $form->image->getFileInfo();
//                $ext = split("[/\\.]", $file['image']['name']);
//                $newName = 'newname.'.$ext[count($ext)-1];
//
//                $form->image->addFilter('Rename', realpath(dirname('.')).
//                      DIRECTORY_SEPARATOR.
//                      'upload'.
//                      DIRECTORY_SEPARATOR.
//                      $newName);
//                $form->image->receive();
                
                $newName = $file['image']['name'];
                $ext = split("[/\\.]", $newName);
               
                $handle = fopen($newName, "r");
                $contents = fread($handle, filesize($newName));
                fclose($handle);
                
                $at = new Zend_Mime_Part(base64_encode($contents));
//                $at = new Zend_Mime_Part(file_get_contents($newName));
                $at->type        = $newName;
                $at->disposition = Zend_Mime::DISPOSITION_INLINE;
                $at->encoding    = Zend_Mime::ENCODING_8BIT;
                $at->filename    = 'attach1.'.$ext[count($ext)-1];
                
                $values = $form->getValues();
                
                $config = array(
                    'ssl' => 'ssl',
                    'port' => 465,
                    'auth' => 'login',
                    'username' => 'grebenvictor',
                    'password' => '21pnds73rdit');

                $transport = new Zend_Mail_Transport_Smtp('smtp.yandex.ru', $config);

                $mail = new Zend_Mail();
                $mail->setBodyText('Фамилия: '.$values['family'].' Имя: '.$values['name']);
                $mail->setFrom('grebenvictor@yandex.ru', 'Система регистрации участников');
                $mail->addTo('grebenvictor@yandex.ru', 'Администратор кооператива');
                $mail->setSubject('Заявка на вступление');
                $mail->addAttachment($at);
                $mail->send($transport);

                $this->_redirect('/default/index/posted');
            }
        }
    }
    
    public function postedAction()
    {        
                
    }


}

