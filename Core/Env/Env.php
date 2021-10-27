<?php
namespace UniUSM\Env\Env
{
    class Env
    {
        public static function Set($key,$value)
        {
            global $env;
            if($key != null && $value != null)
                $env[$key] = $value;
        }

        public static function Get($key)
        {
            global $env;

            if($env != null && $key != null)
            {
                return $env[$key];
            }
        }

        public static function isValue($value)
        {
            global $env;
            if($env != null && in_array($env, $value))
            {
                return true;
            }
            return false;
        }

        public static function GetAll()
        {
            global $env;
            if($env != null)
            {
                return $env;
            }
        }
    }
}