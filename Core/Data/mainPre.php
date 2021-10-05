<?php


namespace UniUSM\Data\testData;

class mainPre extends Property
{
    private static $name = "Main.Pre";
    public static function Set($value)
    {
        $arr_data = array(
            "<?\n/*\nНе редактируйте этот файл\n",
            "Auto-generation by UniUSM\n",
        );

        $text = null;

        foreach ($arr_data as $data)
        {
            $text .= $data;
        }
        $text .= "//".$value;
        $text .= "\n*/";
        file_put_contents(self::$name, $text);
    }

    public static function Get()
    {
        if(file_exists(self::$name))
        {
            return file_get_contents(self::$name);
        }
    }
}