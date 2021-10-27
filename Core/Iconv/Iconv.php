<?php

namespace UniUSM\Iconv\Iconv {

    class Iconv
    {
        public static function Set($text)
        {
            return iconv( 'UTF-8','windows-1251',$text);
        }
    }
}