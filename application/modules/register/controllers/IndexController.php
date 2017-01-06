<?php

class Register_Form_Index extends Zend_Form {

  public function init() {
    // инициализируем форму
    $this->setAction('/register')->setMethod('post');

    // создаем текстовое поле для ввода названия
    $family = new Zend_Form_Element_Text('family');
    $family -> setLabel('Фамилия')
        -> setOptions(array('size' => '35'))
        -> setRequired(true)
        -> addValidator('NotEmpty', true)
        -> addFilter('HtmlEntities')
        -> addFilter('StringTrim');

    // создаем текстовое поле для ввода названия
    $name = new Zend_Form_Element_Text('name');
    $name -> setLabel('Имя')
        -> setOptions(array('size' => '35'))
        -> setRequired(true)
        -> addValidator('NotEmpty', true)
        -> addFilter('HtmlEntities')
        -> addFilter('StringTrim');

    // создаем текстовое поле для ввода названия
    $name2 = new Zend_Form_Element_Text('name2');
    $name2 -> setLabel('Отчество')
        -> setOptions(array('size' => '35'))
        -> setRequired(true)
        -> addValidator('NotEmpty', true)
        -> addFilter('HtmlEntities')
        -> addFilter('StringTrim');

    // создаем текстовое поле для ввода названия
    $phone = new Zend_Form_Element_Text('phone');
    $phone -> setLabel('Телефон')
        -> setOptions(array('size' => '50'))
        -> setRequired(true)
        -> addValidator('NotEmpty', true)
        -> addFilter('HtmlEntities')
        -> addFilter('StringTrim');

    // создаем текстовое поле для ввода адреса электронной почты
    $email = new Zend_Form_Element_Text('email');
    $email -> setLabel('Электропочта')
        -> setOptions(array('size' => '50'))
        -> setRequired(true)
        -> addValidator('NotEmpty', true)
        -> addValidator('EmailAddress', true)
        -> addFilter('HtmlEntities')
        -> addFilter('StringToLower')
        -> addFilter('StringTrim');

    $image = new Zend_Form_Element_File('image');
    $image->setLabel('Скан')
        ->setDestination(APPLICATION_PATH . '/../public/upload')
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

class Register_IndexController extends Zend_Controller_Action
{

    public function init()
    {
    /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        // генерируем форму ввода
        $form = new Register_Form_Index();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) 
        {
            if ($form->isValid($this->getRequest()->getPost())) 
            {   
                // Переименуем файл
                $originalFilename = pathinfo($form->image->getFileName());
                $newFilename = 'file-' . uniqid() . '.' . $originalFilename['extension'];
                $form->image->addFilter('Rename', $newFilename);
                $form->image->receive();
                // Отправляем письмо
                $this->sendMail(
                        'Заявка на вступление', 
                        $this->buildBody($form->getValues()), 
                        'Скан' . '.' . $originalFilename['extension'], 
                        APPLICATION_PATH . '/../public/upload/' . $newFilename);
                // Перенаправляем
                $this->_redirect('/register/success');
            }
        }
    }
    
    public function successAction()
    {
        //
    }
    
    private function sendMail($subject, $body, $filename, $filepath)
    {
        $config = array(
            'ssl' => 'ssl',
            'port' => 465,
            'auth' => 'login', 
            'username' => 'grebenvictor',
            'password' => '21pnds73rdit'
        );       

        $mail = new Zend_Mail();
        $mail->setBodyHtml($body);
        $mail->setFrom('grebenvictor@yandex.ru', 'Система регистрации участников');
        $mail->addTo('grebenvictor@yandex.ru', 'Администратор кооператива');
        $mail->setSubject($subject);

        $at = new Zend_Mime_Part(file_get_contents($filepath));
        $at->disposition = Zend_Mime::DISPOSITION_INLINE;
        $at->encoding = Zend_Mime::ENCODING_BASE64;
        $at->filename = $filename;

        $mail->addAttachment($at);

        $transport = new Zend_Mail_Transport_Smtp('smtp.yandex.ru', $config);
        
        $mail->send($transport);
    }
    
    private function buildBody($values)
    {
        $domDoc = new DOMDocument();
        $title = $domDoc->createElement( 'h2', trim(trim($values['family']. ' ' . $values['name']) . ' ' . $values['name2'])  );
        $phone = $domDoc->createElement( 'p', 'Тел.: ' );
        $phone->appendChild( new DOMElement( 'b', $values['phone'] ) );
        $email = $domDoc->createElement( 'p', 'Email: ' );
        $email->appendChild( new DOMElement( 'b', $values['email'] ) );

        $domDoc->appendChild($title);
        $domDoc->appendChild($phone);
        $domDoc->appendChild($email);

        return $domDoc->saveHTML();
    }

}

