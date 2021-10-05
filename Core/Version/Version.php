<?php
namespace UniUSM\Core\Version
{
    class Version
    {
        public static function Get()
        {
            $str = str_replace(array("a","b","c","d","e","f","."),null,c("Form6->os")->text);

            return substr($str, 0, 3);
        }

        public static function Set($version)
        {
            c("Form6->os")->text = $version;
        }
    }
}