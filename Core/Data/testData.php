<?php


namespace UniUSM\Data\testData
{
    class testData extends Property
    {
        private static $name = "test.dat";

        public static function Set($value)
        {
            $val = null;
            switch ($value)
            {
                case "update":
                case "false":
                case "true":
                $val = $value;
                    break;
            }

            file_put_contents(self::$name, $val);
        }

        public static function Get()
        {
            if(file_exists(self::$name))
            {
                return file_get_contents(self::$name);
            }
        }
    }
}