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

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
    /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->sidebar();
        
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
        $this->sidebar();
    }
    
    public function partnersAction()
    {
        $this->sidebar();
        
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
        $this->sidebar();
    }
    
    public function pricesAction()
    {
        $this->sidebar();
        
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
        $this->sidebar();
    }
    
    public function aboutAction()
    {
        $this->sidebar();
    }
    
    private function sidebar() 
    {
        // генерируем форму ввода
        $form = new First_Input_Form();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) 
        {
            if ($form->isValid($this->getRequest()->getPost())) 
            {
                $session = new Zend_Session_Namespace('first.input');
                $session->values = $form->getValues();
                // Перенаправляем
                $this->_redirect('/register');
            }
        }
    }
}