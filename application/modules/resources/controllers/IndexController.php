<?php

class Resources_IndexController extends Zend_Controller_Action
{

    public function init()
    {
    /* Initialize action controller here */
    }

    public function showAction()
    {
        $this->view->resource = '123';
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
                'host'     => 'localhost',
                'username' => 'webapp',
                'password' => 'ros1nf0rm',
                'dbname'   => 'webshop'
            ));

            $result = $db->fetchAll('SELECT body FROM resources WHERE id='.$input->id);

            $this->view->resource = $result[0]['body'];
        }
    }
}

