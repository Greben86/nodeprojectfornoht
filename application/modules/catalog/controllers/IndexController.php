<?php

class Catalog_IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        // Делаем запрос к API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:8080/shop/goods/catalog');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'sodeystvie');
        $result = curl_exec($ch);
        curl_close($ch);

        $this->view->goods = json_decode($result, true);
    }
}