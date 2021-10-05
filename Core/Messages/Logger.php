<?php


namespace UniUSM\Core\Messages;

class Logger
{
    protected static $title;
    public static $enable = false;

    public static function Send($message, $action, $code)
    {
        if(self::$enable)
            file_put_contents('usm.log', self::_env($message, $action, $code), FILE_APPEND);
    }

    public static function SetTitle($title)
    {
        self::$title = $title;
    }

    private static function _env($message, $action, $code)
    {
        $act = null;
        $cd = null;
        if($message !=null)
        {
            switch ($action)
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