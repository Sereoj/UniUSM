<?php

use UniUSM\Env\Env\Env;

class Settings
{
    private static $path;
    private static $data = array();

    public static function OpenSettings($path)
    {
        self::Init($path);
        self::DeleteComments($path);

        $data = array();

        if($path != null)
        {
            foreach (self::$data as $line)
            {
                if( strpos($line, "[") !== false &&
                    strpos($line, "]") !== false &&
                    strpos($line, "=") == false)
                {
                    continue;
                }else
                {
                    $text = explode("=", $line);
                    $key = $text[0];

                    if( strpos($text[1], "[") !== false &&
                        strpos($text[1], "]") !== false &&
                        strpos($text[1], ";") !== false)
                    {
                        $clear_text = str_replace(array('[',']'),null, $text[1]);
                        $strings = explode(";", $clear_text);
                        Env::Set($key, $strings);
                    }else{
                        $clear_text = str_replace(array('[',']'),null, $text[1]);
                        Env::Set($key, $clear_text);
                    }
                }
            }
        }

    }

    private static function Init($path)
    {
        if(file_exists($path))
        {
            switch (fileExt($path))
            {
                case "pre":
                case "sv":
                    self::$path = $path;
                break;
            }
        }
    }

    private static function DeleteComments($path)
    {
        $lines = file($path);
        $count = 0;
        $comment_line = 0;
        foreach ($lines as $line)
        {
            $count ++;
            $line = trim($line);

            if($line != null)
            {
                if(strpos($line, "/*") !== false)
                {
                    //Logger::Send("Нашел /* ".$comment_line,"set",1);

                    if (strpos($line, "*/") !== false)
                    {
                        //Logger::Send("Нашел /* */". $line,"set",1);
                        $comment_line = 0;
                        continue;
                    }

                    $comment_line ++;
                    continue;
                }else if($comment_line > 0)
                {
                    $comment_line ++;
                    //Logger::Send("Нашел /* и продолжение". $line,"set",1);

                    if (strpos($line, "*/") !== false)
                    {
                        //Logger::Send("Нашел */". $line,"set",1);
                        $comment_line = 0;
                        continue;
                    }
                    continue;
                }
                if(strpos($line, "//") !== false)
                {
                    //Logger::Send("Нашел //","set",1);
                    continue;
                }
                self::$data[] = $line;
            }
        }
    }
}