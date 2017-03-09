<?php

class Program_IndexController extends Zend_Controller_Action
{

    public function init()
    {
    /* Initialize action controller here */
    }

    public function showAction()
    {
        // устанавливаем фильтры и валидаторы для входных данных
        // полученных в запросе
        $filters = array(
            'page' => array('HtmlEntities', 'StripTags', 'StringTrim')
        );
        $validators = array(
            'page' => array('NotEmpty')
        ); 

        // проверяем корректность входных данных
        // получаем запрошенную запись
        // добавляем ее к представлению
        $input = new Zend_Filter_Input($filters, $validators);
        $input->setData($this->getRequest()->getParams());
        if ($input->isValid()) {            
            $this->render($input->page);
        }
    }
}

