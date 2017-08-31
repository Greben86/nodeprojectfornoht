<?php

class Customer_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
    private $username;
    private $password;
    private $url;
    public $configs;
    
    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;
        $this->url = 'http://127.0.0.1:8080/shop/customers/checkpass?in='.$username.'&pass='.md5($password);
    }
    
    public function authenticate() {
        $result = file_get_contents($this->url);
        
        if ($result == 'Ok')
        {
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->username, array());
        } else {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, null, array($result));
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
                    'placeholder'  => 'IN',
              ))
              -> setRequired(true)
              -> addValidator('NotEmpty', true, array(
                    'messages' => array(
                        Zend_Validate_NotEmpty::IS_EMPTY => 'IN не может быть пустым'
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

class Account_LoginController extends Zend_Controller_Action
{               
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
                $adapter = new Customer_Auth_Adapter($values['username'], $values['password']);
                $adapter->configs = $this->getInvokeArg('bootstrap')->getOption('configs');
                $auth = Zend_Auth::getInstance();                
                $result = $auth->authenticate($adapter);
                if ($result->isValid())
                {
                    $this->_redirect('/account');
                } else {
                    $this->view->message = $result->getMessages()[0];
                }
            }
        }
    }
    
    public function logoutAction()
    {
        // Аннулируем аутентификацию
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        $this->_redirect('/home');
    }

}

