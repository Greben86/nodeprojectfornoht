<?php

class My_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
    private $username;
    private $password;
    public $configs;
    
    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
    }
    
    public function authenticate() {
        $localConfig = new Zend_Config_Ini($this->configs['localConfigPath']);
        
        $pass = md5($this->password);
        if ($this->username == $localConfig->admin->login && 
                $pass == $localConfig->admin->pass)
        {
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->username, array());
        } else {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, null, array('Авторизация не пройдена '));
        }
    }

}

class Auth_Form_Login extends Zend_Form 
{
    //put your code here
    public function init() {
        // инициализируем форму
        $this->setAction('/admin/login')
            ->setMethod('post')
            ->setAttribs(array(
                'class' => 'form-signin',
            ));

        // создаем текстовое поле для ввода имени
        $username = new Zend_Form_Element_Text('username');
        $username -> setOptions(array('size' => '35'))
              ->setAttribs(array(
                    'class' => 'form-control',
                    'placeholder'  => 'Логин',
              ))
              -> setRequired(true)
              -> addValidator('NotEmpty', true, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'Логин не может быть пустым'
                    )))
              -> addFilter('HtmlEntities')
              -> addFilter('StringTrim');
        
        // создаем текстовое поле для ввода адреса электронной почты
        $password = new Zend_Form_Element_Password('password');
        $password -> setOptions(array('size' => '35'))
               ->setAttribs(array(
                    'class' => 'form-control',
                    'placeholder'  => 'Пароль',
               ))
               -> setRequired(true)  
               -> addValidator('NotEmpty', true, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'Пароль не может быть пустым'
                    )))
               -> addFilter('HtmlEntities')
               -> addFilter('StringTrim');
        
        // создаем кнопку отправки
        $submit = new Zend_Form_Element_Submit('submit');
        $submit -> setLabel('Войти')
                -> setOptions(array('class' => 'btn btn-lg btn-primary btn-block'));
        
        // добавляем элементы к форме
        $this -> addElement($username)
              -> addElement($password)
              -> addElement($submit);
    }
}

class Admin_LoginController extends Zend_Controller_Action
{

    public function init()
    {
        // отключение макета для данного действия
        $this->_helper->layout->disableLayout();
    }
    
    public function preDispatch() 
    {          
        // Проверяем аутентификацию
        if (Zend_Auth::getInstance()->hasIdentity())
        {
            //$this->_redirect('/admin');
        }
        
        return parent::preDispatch();
    }

    public function loginAction()
    {
        // генерируем форму ввода
        $form = new Auth_Form_Login();
        $this->view->form = $form;

        if ($this->getRequest()->isPost())
        {
            if ($form->isValid($this->getRequest()->getPost()))
            {
                $values = $form->getValues();
                $adapter = new My_Auth_Adapter($values['username'], $values['password']);
                $adapter->configs = $this->getInvokeArg('bootstrap')->getOption('configs');
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($adapter);
                if ($result->isValid())
                {
                    $this->_redirect('/admin/login/success');
                } else {
                    $this->view->message = '';
                }
            }
        }
    }
    
    public function successAction()
    {
        // Проверяем аутентификацию
        if (!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('/admin/login');
        } else {
            $this->_redirect('/admin');
        }
    }
    
    public function logoutAction()
    {
        // Проверяем аутентификацию
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        $this->_redirect('/home');
    }

}

