<?php

class First_Input_Form extends Zend_Form 
{
    //put your code here
    public function init() 
    {
        // инициализируем форму
        $this->setAction('/home')->setMethod('post');

        // создаем текстовое поле для ввода названия
        $family = new Zend_Form_Element_Text('family');
        $family -> setLabel('Фамилия')
            ->setAttribs(array(
                'class' => 'form-control input-sm',
                'placeholder'  => 'Укажите фамилию',
            ))
            -> setOptions(array('size' => '35'))
            -> setRequired(true)
            -> addValidator('NotEmpty', false, array(
                'messages' => array(
                    Zend_Validate_NotEmpty::IS_EMPTY => 'Поле не может быть пустым'
                )))
            -> addFilter('HtmlEntities')
            -> addFilter('StringTrim');

        // создаем текстовое поле для ввода названия
        $name = new Zend_Form_Element_Text('name');
        $name -> setLabel('Имя')
            ->setAttribs(array(
                'class' => 'form-control input-sm',
                'placeholder'  => 'Укажите имя',
            ))
            -> setOptions(array('size' => '35'))
            -> setRequired(true)
            -> addValidator('NotEmpty', false, array(
                'messages' => array(
                    Zend_Validate_NotEmpty::IS_EMPTY => 'Поле не может быть пустым'
                )))
            -> addFilter('HtmlEntities')
            -> addFilter('StringTrim');

        // создаем текстовое поле для ввода названия
        $name2 = new Zend_Form_Element_Text('name2');
        $name2 -> setLabel('Отчество')
            ->setAttribs(array(
                'class' => 'form-control input-sm',
                'placeholder'  => 'Укажите отчество',
            ))
            -> setOptions(array('size' => '35'))
            -> setRequired(true)
            -> addValidator('NotEmpty', false, array(
                'messages' => array(
                    Zend_Validate_NotEmpty::IS_EMPTY => 'Поле не может быть пустым'
                )))
            -> addFilter('HtmlEntities')
            -> addFilter('StringTrim');

        // создаем текстовое поле для ввода названия
        $phone = new Zend_Form_Element_Text('phone');
        $phone -> setLabel('Телефон')
            ->setAttribs(array(
                'class' => 'form-control input-sm',
                'placeholder'  => 'Укажите контактный телефон',
            ))
            -> setOptions(array('size' => '50'))
            -> setRequired(true)
            -> addValidator('NotEmpty', true, array(
                'messages' => array(
                    Zend_Validate_NotEmpty::IS_EMPTY => 'Поле не может быть пустым'
                )))
            -> addFilter('HtmlEntities')
            -> addFilter('StringTrim');

        // создаем текстовое поле для ввода адреса электронной почты
        $email = new Zend_Form_Element_Text('email');
        $email -> setLabel('Электронная почта')
            ->setAttribs(array(
                'class' => 'form-control input-sm',
                'placeholder'  => 'Укажите адрес email',
            ))
            -> setOptions(array('size' => '100'))
            -> setRequired(true)
            -> addValidator('NotEmpty', true, array(
                'messages' => array(
                    Zend_Validate_NotEmpty::IS_EMPTY => 'Поле не может быть пустым'
                )))
            -> addValidator('EmailAddress', true, array(
                'messages' => array(
                    Zend_Validate_EmailAddress::DOT_ATOM => "'%localPart% не соответствует формату dot-atom",
                    Zend_Validate_EmailAddress::INVALID => "'%value%' неправильный адрес электронной почты. Введите его в формате имя@домен",
                    Zend_Validate_EmailAddress::INVALID_FORMAT => "'%value%' неправильный адрес электронной почты. Введите его в формате имя@домен",
                    Zend_Validate_EmailAddress::INVALID_HOSTNAME => "'%hostname%' недопустимое имя хоста для адреса '%value%'",
                    Zend_Validate_EmailAddress::INVALID_LOCAL_PART => "'%localPart%' недопустимое имя для адреса '%value%'",
                    Zend_Validate_EmailAddress::INVALID_MX_RECORD => "'%hostname%' не имеет корректной MX-записи об адресе '%value%'",
                    Zend_Validate_EmailAddress::INVALID_SEGMENT => "'%hostname%' не является маршрутизируемым сегментом сети. Адрес электронной почты '%value%' не может быть получен из публичной сети.",
                    Zend_Validate_EmailAddress::LENGTH_EXCEEDED => "'%value%' превышает допустимую длину",
                    Zend_Validate_EmailAddress::QUOTED_STRING => "'%localPart%' не соответствует формату quoted-string",
                )))
            -> addFilter('HtmlEntities')
            -> addFilter('StringToLower')
            -> addFilter('StringTrim');

        // создаем кнопку отправки
        $submit = new Zend_Form_Element_Submit('submit');
        $submit -> setLabel('Далее >>')
            -> setAttribs(array('class' => 'btn btn-success btn-xs'));

        // добавляем элементы к форме
        $this -> addElement($family)
            -> addElement($name)
            -> addElement($name2)
            -> addElement($phone)
            -> addElement($email);

        $this->addDisplayGroup(array('family', 'name', 'name2', 'phone', 'email'), 'zayavka');
        $this->getDisplayGroup('zayavka')
            ->setLegend('Регистрация');
        $this->addElement($submit);
    }
}

class Register_Form_Index extends Zend_Form {

  public function init() {
    // инициализируем форму
    $this->setAction('/register')->setMethod('post');

    // создаем текстовое поле для организации
    $customer = new Zend_Form_Element_Text('customer');
    $customer -> setLabel('Организация')
        ->setAttribs(array(
            'class' => 'form-control',
            'placeholder'  => 'Укажите название организации',
        ))
        -> setOptions(array('size' => '35'))
        -> setRequired(true)
        -> addValidator('NotEmpty', false, array(
            'messages' => array(
                Zend_Validate_NotEmpty::IS_EMPTY => 'Поле не может быть пустым'
            )))
        -> addFilter('HtmlEntities')
        -> addFilter('StringTrim');

    $note = new Zend_Form_Element_Textarea('note');
    $note->setLabel('Дополнительная информация')
        ->setAttribs(array(
            'class' => 'form-control',
            'rows'  => '3',
            'placeholder'  => 'Раскажите о себе',
        ));
    
    $image = new Zend_Form_Element_File('image');
    $image->setLabel('Отсканированный бланк заявления')
        ->setDestination(APPLICATION_PATH . '/../public/upload')
        ->setAttrib('multiple', false)
        ->addValidator('Size', false, 2e+7)
        ->addValidator('Extension', false, 'jpg,png,gif,bmp,tiff,pdf,doc,docx,odt');
    
    // Капча
    $captcha = new Zend_Form_Element_Captcha('captcha', array(
        'captcha' => 'Figlet',
        'captchaOptions' => array(
            'captcha' => 'Figlet',
            'wordLen' => 6,
            'timeout' => 300,
        ),
    ));
    $captcha->setLabel('Пожалуйста подтвердите что вы человек')
        ->setAttribs(array(
            'class' => 'form-control',
            'placeholder'  => 'Введите текст с картинки',
        ));

    // создаем кнопку отправки
    $submit = new Zend_Form_Element_Submit('submit');
    $submit -> setLabel('Подать заявку')
        -> setAttribs(array('class' => 'btn btn-success'));

    // добавляем элементы к форме
    $this -> addElement($customer)
        -> addElement($note)
        -> addElement($image);

    $this->addDisplayGroup(array('customer', 'note', 'image'), 'zayavka');
    $this->getDisplayGroup('zayavka')
        ->setLegend('Заявление на вступление');
    $this->addElement($captcha);
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
        // генерируем форму ввода
        $form = new Register_Form_Index();
        $this->view->form = $form;
        
        $session = new Zend_Session_Namespace('first.input');
        $this->view->firstinput = $session->values;

        if ($this->getRequest()->isPost()) 
        {
            if ($form->isValid($this->getRequest()->getPost())) 
            {                   
                // Переименуем файл
                $originalFilename = pathinfo($form->image->getFileName());
                $newFilename = 'skan-' . uniqid() . '.' . $originalFilename['extension'];
                $form->image->addFilter('Rename', $newFilename);
                $form->image->receive();
                // Отправляем письмо
                $values = array_merge($form->getValues(), $session->values);
                $this->sendMail(
                        'Заявка на вступление', 
                        $this->buildBody($values), 
                        'Скан' . '.' . $originalFilename['extension'],
                        APPLICATION_PATH . '/../public/upload/' . $newFilename);
                // Сохраняем в базу
                $this->saveStatement($values, $newFilename);
                // Перенаправляем
                $this->_redirect('/register/success');
            }
        }
    }
    
    public function successAction()
    {
        // Убиваем сессию
//        Zend_Session::destroy();
    }
    
    public function inputformAction()
    {
        $this->_helper->layout->disableLayout();
        // генерируем форму ввода
        $form = new First_Input_Form();
        $this->view->form = $form;
    }
    
    private function sendMail($subject, $body, $filename, $filepath)
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
        $mail->setFrom($localConfig->email->address, 'Система регистрации участников');
        $mail->addTo('vygodno.vmeste@yandex.ru', 'Администратор кооператива');
        $mail->addTo('grebenvictor@yandex.ru', 'Разработчик');
        $mail->setSubject($subject);

        $at = new Zend_Mime_Part(file_get_contents($filepath));
        $at->disposition = Zend_Mime::DISPOSITION_INLINE;
        $at->encoding = Zend_Mime::ENCODING_BASE64;
        $at->filename = $filename;

        $mail->addAttachment($at);

        $transport = new Zend_Mail_Transport_Smtp($localConfig->email->host, $config);
        
        $mail->send($transport);
    }
    
    private function buildBody($values)
    {
        $domDoc = new DOMDocument();
        $title = $domDoc->createElement( 'h2', $values['customer'] );
        $person = $domDoc->createElement( 'h3', trim(trim($values['family']. ' ' . $values['name']) . ' ' . $values['name2']) );
        $phone = $domDoc->createElement( 'p', 'Тел.: ' );
        $phone->appendChild( new DOMElement( 'b', $values['phone'] ) );
        $email = $domDoc->createElement( 'p', 'Email: ' );
        $email->appendChild( new DOMElement( 'b', $values['email'] ) );
        $note = $domDoc->createElement( 'p', $values['note'] );

        $domDoc->appendChild($title);
        $domDoc->appendChild($person);
        $domDoc->appendChild($phone);
        $domDoc->appendChild($email);
        $domDoc->appendChild( new DOMElement( 'br' ) );
        $domDoc->appendChild($note);

        return $domDoc->saveHTML();
    }
    
    private function saveStatement($values, $file)
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
        
        // Формируем массив данных
        $data = array(
            'email'     => $values['email'],
            'fullname'  => $values['customer'],
            'family'    => $values['family'],
            'name'      => $values['name'],
            'name2'     => $values['name2'],
            'phone'     => $values['phone'],
            'note'      => $values['note'],
            'filename'  => $file
        );
        // Сохраняем данные
        $db->insert('statements', $data);
    }

}

