<?php

class Zend_View_Helper_FloatToNum extends Zend_View_Helper_Abstract {
        /* float to numeric
         * http://php.net/manual/en/language.types.float.php
        */
	public function FloatToNum($str) {
            if(strpos($str, '.') < strpos($str,',')){ 
                    $str = str_replace('.','',$str); 
                    $str = strtr($str,',','.');            
                } 
                else{ 
                    $str = str_replace(',','',$str);            
                } 
                return (float)$str;
	}

}

