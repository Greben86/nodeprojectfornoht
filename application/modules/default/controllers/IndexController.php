<?php

class Default_Form_Index extends Zend_Form {

  public function init() {
    // инициализируем форму
    $this->setAction('/')->setMethod('post');

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

                $values = $form->getValues();

                $config = array(
                'ssl' => 'ssl',
                'port' => 465,
                'auth' => 'login',
                'username' => 'grebenvictor',
                'password' => '21pnds73rdit');

                $transport = new Zend_Mail_Transport_Smtp('smtp.yandex.ru', $config);

                $mail = new Zend_Mail();

                /*if (is_uploaded_file($file['userfile']['tmp_name'])) {
                echo "Файл ". $_FILES['userfile']['name'] ." успешно загружен.\n";
                echo "Отображаем содержимое\n";
                readfile($_FILES['userfile']['tmp_name']);
                } else {
                echo "Возможная атака с участием загрузки файла: ";
                echo "файл '". $_FILES['userfile']['tmp_name'] . "'.";
                }*/

                $mail->setBodyHtml(
                '<h2>' . trim(trim($values['family']. ' ' . $values['name']) . ' ' . $values['name2']) . '</h2><br>' .
                'Тел.: <b>'.$values['phone'] . '</b><br>' .
                'Email: <b>'.$values['email'] . '</b>');
                $mail->setFrom('grebenvictor@yandex.ru', 'Система регистрации участников');
                $mail->addTo('grebenvictor@yandex.ru', 'Администратор кооператива');
                $mail->setSubject('Заявка на вступление');

                //if ($tmpFilePath != ""){
                //$newFilePath = realpath(dirname('.'))."./upload/" . $_FILES['image']['name'][$i];
                $newFilePath = APPLICATION_PATH . '/../public/upload/' . $file['image']['name'];

                //if(move_uploaded_file($tmpFilePath, $newFilePath)) {
                $fname = $_FILES['image']['name'];
                $ftempname = $_FILES['image']['tmp_name'];
                $at = new Zend_Mime_Part(file_get_contents($newFilePath));
                $at->disposition = Zend_Mime::DISPOSITION_INLINE;
                $at->encoding = Zend_Mime::ENCODING_BASE64;
                $at->filename = $fname;

                $mail->addAttachment($at);
                //}
                //}*/
                $mail->send($transport);

                $this->_redirect('/default/index/posted');
            }
        }
    }

    public function postedAction()
    {
    // Заглушка
    }

}

