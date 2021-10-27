<?php


namespace UniUSM\Core\Messages;

class Logger
{

    public static $enable = false;

    public static function Send($message, $action, $code)
    {
        if(self::$enable)
            if($action != 'only')
                file_put_contents('usm.log', self::_env($message, $action, $code), FILE_APPEND);
            else
                file_put_contents('usm.log', $message, FILE_APPEND);
    }

    private static function _env($message, $action, $code)
    {
        $act = null;
        $cd = null;
        if($message !=null)
        {
            switch (strtolower($action))
            {
                case "move":
                case "include":
                case "get":
                case "set":
                case "update":
                case "use":
                case "read":
                case "activate":
                case "check":
                case "validate":
                case "write":
                    $act = "system:".$action;
                    break;
                default:
                    $act = "user:".$action;
                    break;
            }

            $cd = ($code ? "success_action" : "error_action");
        }

        return sprintf("UniUSM:$message;$act;$cd%s", PHP_EOL);
    }

}