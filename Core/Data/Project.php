<?php


namespace UniUSM\Data\testData;


class Project
{
    public static $path;

    public static function createDir($name)
    {
        self::$path = "data/save/".$name;
        dir_create(self::$path);
    }

    public static function Set($value)
    {
        if(self::$path)
        {
            file_put_contents(self::$path."/Main.php", $value);
        }
    }
    public static function Get()
    {
        if(file_exists(self::$path."/Main.php"))
        {
            return file_get_contents(self::$path."/Main.php");
        }
    }
}