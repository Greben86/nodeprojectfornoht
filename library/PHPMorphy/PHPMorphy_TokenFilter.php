<?php

require(__DIR__ . '/src/common.php');

class PHPMorphy_TokenFilter extends Zend_Search_Lucene_Analysis_TokenFilter
{        
    public function __construct()
    {
        if (!function_exists('mb_strtoupper')||!function_exists('mb_strtolower')) {
            // mbstring extension is disabled
            require_once 'Zend/Search/Lucene/Exception.php';
            throw new Zend_Search_Lucene_Exception('Utf8 compatible upper/lower case filter needs mbstring extension to be enabled.');
        }
    }    
    
    public function normalize(Zend_Search_Lucene_Analysis_Token $srcToken)
    {        
        $opts = array(
            'storage' => PHPMORPHY_STORAGE_SHM,
        );

        // Path to directory where dictionaries located
        $dir = __DIR__ . '/dicts/utf-8';
        $lang = 'ru_RU';

        // Create phpMorphy instance
        try {
            $morphy = new phpMorphy($dir, $lang, $opts);
            $word = iconv('utf-8', $morphy->getEncoding(), $srcToken->getTermText());
            $result = $morphy->getBaseForm(mb_strtoupper($word));
            if (count($result)) {
                $word = mb_strtolower($result[0]);
                $srcToken->setTermText($word);
            }
        } catch(phpMorphy_Exception $e) {
            die('Error occured while creating phpMorphy instance: ' . PHP_EOL . $e);
        }
        
        return $srcToken;
    }
}

