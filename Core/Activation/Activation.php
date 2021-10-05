<?php
namespace UniUSM\Core\Activation
{
    use UniUSM\Core\Messages\Logger;

    class Activation
    {
        public static $keyMask;

        protected static function CheckIsFile()
        {
            if (file_exists("key.lic"))
            {
                Logger::Send("file:key.lic","check", 1);
                return true;
            }else{
                Logger::Send("file:key.lic","check", 0);
                return false;
            }
        }

        public static function Get()
        {
            return file_get_contents("key.lic");
        }

        public static function IsValidate()
        {
            if(self::CheckIsFile())
            {
                $key = strtolower(self::Get());

                $exp = explode("-", $key);
                if(substr($exp[0], 0, 3) == "reg")
                {
                    if(substr($exp[1], 0, 3) == "asd")
                    {
                        if(is_numeric($exp[2]))
                        {
                            self::$keyMask = sprintf("%s-%s-******", $exp[0], $exp[1]);
                            Logger::Send("file:key.lic","validate", 1);
                            return true;
                        }
                    }
                }
            }
            Logger::Send("file:key.lic","validate", 0);
            return false;
        }
    }
}