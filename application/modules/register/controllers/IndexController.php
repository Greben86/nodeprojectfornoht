<?php

class Validate_EmailExists extends Zend_Validate_Abstract {

    const ERROR_RECORD_FOUND = 'recordFound';

    protected $_messageTemplates = array(
        self::ERROR_RECORD_FOUND => "Email '%value%' уже зарегистрирован в системе",
    );
    private $host;

    public function __construct($host) {
        $this->host = $host;
    }

    public function isValid($value) {
        $valid = true;
        $this->_setValue($value);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->host . '/customers/list?key=052b15e78378eb6c82164b63c27be0b1');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie');
        $result = curl_exec($ch);
        curl_close($ch);

        $customers = json_decode($result, true);
        foreach ($customers as $customer) {
            if ($customer['email'] == $value) {
                $valid = false;
                $this->_error(self::ERROR_RECORD_FOUND);
                break;
            }
        }

        return $valid;
    }

}

class Register_Form_Index extends Zend_Form {

    private $sitekey;
    private $secretkey;
    private $host;

    public function __construct($options = null) {
        $this->sitekey = $options['sitekey'];
        $this->secretkey = $options['secretkey'];
//        $localConfig = new Zend_Config_Ini($options['localConfigPath']);
//        $this->host = $localConfig->api->host;
        parent::__construct(null);
    }

    public function init() {
        // инициализируем форму
        $this->setAction('/register')->setMethod('post');

        // создаем текстовое поле для ввода названия
        $family = new Zend_Form_Element_Text('family');
        $family->setLabel('Фамилия')
                ->setAttribs(array(
                    'class' => 'form-control input-sm',
                    'placeholder' => 'Укажите фамилию',
                ))
                ->setOptions(array('size' => '35'))
                ->setRequired(true)
                ->addValidator('NotEmpty', false, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'Поле не может быть пустым'
            )))
                ->addFilter('HtmlEntities')
                ->addFilter('StringTrim');

        // создаем текстовое поле для ввода названия
        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Имя')
                ->setAttribs(array(
                    'class' => 'form-control input-sm',
                    'placeholder' => 'Укажите имя',
                ))
                ->setOptions(array('size' => '35'))
                ->setRequired(true)
                ->addValidator('NotEmpty', false, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'Поле не может быть пустым'
            )))
                ->addFilter('HtmlEntities')
                ->addFilter('StringTrim');

        // создаем текстовое поле для ввода названия
        $name2 = new Zend_Form_Element_Text('name2');
        $name2->setLabel('Отчество')
                ->setAttribs(array(
                    'class' => 'form-control input-sm',
                    'placeholder' => 'Укажите отчество',
                ))
                ->setOptions(array('size' => '35'))
                ->setRequired(true)
                ->addValidator('NotEmpty', false, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'Поле не может быть пустым'
            )))
                ->addFilter('HtmlEntities')
                ->addFilter('StringTrim');

        // создаем текстовое поле для ввода названия
        $phone = new Zend_Form_Element_Text('phone');
        $phone->setLabel('Телефон')
                ->setAttribs(array(
                    'class' => 'form-control input-sm',
                    'placeholder' => 'Укажите контактный телефон',
                ))
                ->setOptions(array('size' => '50'))
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'Поле не может быть пустым'
            )))
                ->addFilter('HtmlEntities')
                ->addFilter('StringTrim');

        // создаем текстовое поле для ввода адреса электронной почты
        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Электронная почта')
                ->setAttribs(array(
                    'class' => 'form-control input-sm',
                    'placeholder' => 'Укажите адрес email',
                ))
                ->setOptions(array('size' => '100'))
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'Поле не может быть пустым'
            )))
                ->addValidator('EmailAddress', true, array(
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
                ->addValidator(new Validate_EmailExists('http://194.67.215.76:8080/shop/'))
                ->addFilter('HtmlEntities')
                ->addFilter('StringToLower')
                ->addFilter('StringTrim');

        $note = new Zend_Form_Element_Textarea('note');
        $note->setLabel('Дополнительная информация')
                ->setAttribs(array(
                    'class' => 'form-control',
                    'rows' => '3',
                    'placeholder' => 'Раскажите о себе',
        ));

        $pass0 = new Zend_Form_Element_Password('password');
        $pass0->setLabel('Придумайте пароль')
                ->setOptions(array('size' => '35'))
                ->setAttribs(array(
                    'class' => 'form-control',
                    'placeholder' => 'Пароль',
                ))
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'Поле не может быть пустым'
            )))
                ->addFilter('HtmlEntities')
                ->addFilter('StringTrim');

        $pass1 = new Zend_Form_Element_Password('pass1');
        $pass1->setLabel('Введите пароль повторно')
                ->setOptions(array('size' => '35'))
                ->setAttribs(array(
                    'class' => 'form-control',
                    'placeholder' => 'Пароль',
                ))
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'Поле не может быть пустым'
            )))
                ->addValidator('Identical', true, array(
                    'token' => 'password',
                    'messages' => array(
                        Zend_Validate_Identical::NOT_SAME => 'Пароли должны совпадать'
            )))
                ->addFilter('HtmlEntities')
                ->addFilter('StringTrim');

        // добавляем элементы к форме
        $this->addElementPrefixPath(
                'Ghost', 'Ghost'
        );

        $this->addElementPrefixPath(
                'Ghost_Form_Decorator', 'Ghost/Form/Decorator', self::DECORATOR // 'decorator'
        );

        $recaptcha = new Ghost_Captcha_ReCaptcha2();
        $recaptcha->setOption('theme', 'light');
        $recaptcha->setOption('hl', 'ru');
        $recaptcha->setPubkey($this->sitekey);
        $recaptcha->setPrivkey($this->secretkey);
        $captcha = new Zend_Form_Element_Captcha('captcha', array('captcha' => $recaptcha));
        $captcha->setRequired('true');

        $this->addElement($family)
                ->addElement($name)
                ->addElement($name2)
                ->addElement($phone)
                ->addElement($email)
                ->addElement($note)
                ->addElement($pass0)
                ->addElement($pass1)
                ->addElement($captcha);

        $this->addDisplayGroup(array('family', 'name', 'name2', 'phone', 'email', 'note', 'password', 'pass1', 'captcha'), 'zayavka');
        $this->getDisplayGroup('zayavka')
                ->setLegend('Заявление на вступление');

        // создаем кнопку отправки
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Подать заявку')
                ->setAttribs(array('class' => 'btn btn-success'));

        $this->addElement($submit);
    }

}

class Register_IndexController extends Zend_Controller_Action {

    const SALT = 'ros1nf0rm';

    public function indexAction() {
        $configs = $this->getInvokeArg('bootstrap')->getOption('recaptcha');
        $form = new Register_Form_Index($configs);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $values = $form->getValues();
                $this->saveStatement($values);
                $this->saveCustomer($values);
                $this->sendMailConfirm($values);
                $this->_redirect('/register/send');
            }
        }
    }

    public function sendAction() {
        //
    }

    public function confirmAction() {
        // Устанавливаем фильтры и валидаторы для данных, полученных из POST
        $filters = array(
            'hash' => array('HtmlEntities', 'StripTags', 'StringTrim')
        );
        $validators = array(
            'hash' => array('NotEmpty')
        );

        $input = new Zend_Filter_Input($filters, $validators);
        $input->setData($this->getRequest()->getParams());

        // Подключаемся к БД
        $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
        $localConfig = new Zend_Config_Ini($configs['localConfigPath']);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $localConfig->api->host . '/customers/list?key=052b15e78378eb6c82164b63c27be0b1');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie');
        $result = curl_exec($ch);
        curl_close($ch);

        $customers = json_decode($result, true);
        foreach ($customers as $customer) {
            if ($input->hash == md5($customer['email'] . self::SALT)) {
                if ($customer['email_confirmed_date'] === null) {
                    $this->confirmCustomer($customer);
                    $this->sendMail('Заявка на вступление', $this->buildBody($customer));
                }
                $this->_redirect('/register/success');
                return;
            }
        }
        throw new Zend_Exception('Ошибка подтверждения адреса электронной почты');
    }

    public function successAction() {
        //
    }

    public function inputformAction() {
        $this->_helper->layout->disableLayout();
        $form = new First_Input_Form();
        $this->view->form = $form;
    }

    private function sendMailConfirm($values) {
        $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
        $localConfig = new Zend_Config_Ini($configs['localConfigPath']);
        $config = array(
            'ssl' => 'ssl',
            'port' => $localConfig->email->port,
            'auth' => 'login',
            'username' => $localConfig->email->user,
            'password' => $localConfig->email->pass
        );

        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyHtml(
                '<h2>Здравствуйте.</h2>'
                . '<br>'
                . '<p>Благодарим Вас за проявленный интерес к нашему ресурсу - нпо-содействие.рф</p>'
                . '<p>Для активации учетной записи перейдите, пожалуйста, по ссылке:</p>'
                . "<a href='http://нпо-содействие.рф/register/confirm/" . md5($values['email'] . self::SALT) . "'>"
                . "нпо-содействие.рф/register/confirm/" . md5($values['email'] . self::SALT)
                . '</a>'
                . '<br>'
                . "<p>Если вы не проходили процедуру регистрации, то можете проигнорировать это письмо.</p>
                <p>Данное письмо было отправлено автоматически. Отвечать на него не нужно.</p>
                <p>С уважением, администрация сайта <a href='http://нпо-содействие.рф/'>нпо-содействие.рф</a></p>"
        );
        $mail->setFrom($localConfig->email->address, 'Система регистрации нпо-содействие.рф');
        $mail->addTo($values['email']);
        $mail->setSubject('Регистрация аккаунта нпо-содействие.рф');

        $transport = new Zend_Mail_Transport_Smtp($localConfig->email->host, $config);

        $mail->send($transport);
    }

    private function sendMail($subject, $body) {
        $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
        $localConfig = new Zend_Config_Ini($configs['localConfigPath']);
        $config = array(
            'ssl' => 'ssl',
            'port' => $localConfig->email->port,
            'auth' => 'login',
            'username' => $localConfig->email->user,
            'password' => $localConfig->email->pass
        );

        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyHtml($body);
        $mail->setFrom($localConfig->email->address, 'Система регистрации участников');
        $mail->addTo('vygodno.vmeste@yandex.ru', 'Администратор кооператива');
        $mail->addTo('grebenvictor@yandex.ru', 'Разработчик');
        $mail->setSubject($subject);

        $transport = new Zend_Mail_Transport_Smtp($localConfig->email->host, $config);

        $mail->send($transport);
    }

    private function buildBody($values) {
        $domDoc = new DOMDocument();
        $title = $domDoc->createElement('h2', $values['name']);
        $person = $domDoc->createElement('h3', $values['fullname']);
        $email = $domDoc->createElement('p', 'Email: ');
        $email->appendChild(new DOMElement('b', $values['email']));

        $domDoc->appendChild($title);
        $domDoc->appendChild($person);
        $domDoc->appendChild($email);

        return $domDoc->saveHTML();
    }

//    private function buildBody($values) {
//        $domDoc = new DOMDocument();
//        $title = $domDoc->createElement('h2', $values['customer']);
//        $person = $domDoc->createElement('h3', trim(trim($values['family'] . ' ' . $values['name']) . ' ' . $values['name2']));
//        $phone = $domDoc->createElement('p', 'Тел.: ');
//        $phone->appendChild(new DOMElement('b', $values['phone']));
//        $email = $domDoc->createElement('p', 'Email: ');
//        $email->appendChild(new DOMElement('b', $values['email']));
//        $note = $domDoc->createElement('p', $values['note']);
//
//        $domDoc->appendChild($title);
//        $domDoc->appendChild($person);
//        $domDoc->appendChild($phone);
//        $domDoc->appendChild($email);
//        $domDoc->appendChild(new DOMElement('br'));
//        $domDoc->appendChild($note);
//
//        return $domDoc->saveHTML();
//    }

    private function saveCustomer($values) {
        // Подключаемся к БД
        $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
        $localConfig = new Zend_Config_Ini($configs['localConfigPath']);
        $db = Zend_Db::factory('Pdo_Mysql', array(
                    'host' => $localConfig->database->host,
                    'dbname' => $localConfig->database->name,
                    'username' => $localConfig->database->user,
                    'password' => $localConfig->database->pass,
                    'charset' => 'utf8'
        ));

        // Формируем массив данных
        $data = array(
            'number' => '',
            'name' => $values['name'],
            'fullname' => trim(trim($values['family'] . ' ' . $values['name']) . ' ' . $values['name2']),
            'email' => $values['email'],
            'pass' => md5($values['password'])
        );
        // Сохраняем данные
        $db->insert('customers', $data);
    }

    private function confirmCustomer($values) {
        // Подключаемся к БД
        $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
        $localConfig = new Zend_Config_Ini($configs['localConfigPath']);
        $db = Zend_Db::factory('Pdo_Mysql', array(
                    'host' => $localConfig->database->host,
                    'dbname' => $localConfig->database->name,
                    'username' => $localConfig->database->user,
                    'password' => $localConfig->database->pass,
                    'charset' => 'utf8'
        ));

        // Формируем массив данных
        $data = array(
            'email_confirmed_date' => 'T'
        );
        // Сохраняем данные
        $db->update('customers', $data, "`email`='" . $values['email'] . "'");
    }

    private function saveStatement($values) {
        // Подключаемся к БД
        $configs = $this->getInvokeArg('bootstrap')->getOption('configs');
        $localConfig = new Zend_Config_Ini($configs['localConfigPath']);
        $db = Zend_Db::factory('Pdo_Mysql', array(
                    'host' => $localConfig->database->host,
                    'dbname' => $localConfig->database->name,
                    'username' => $localConfig->database->user,
                    'password' => $localConfig->database->pass,
                    'charset' => 'utf8'
        ));

        // Формируем массив данных
        $data = array(
            'email' => $values['email'],
            'family' => $values['family'],
            'name' => $values['name'],
            'name2' => $values['name2'],
            'phone' => $values['phone'],
            'note' => $values['note']
        );
        // Сохраняем данные
        $db->insert('statements', $data);
    }

}
