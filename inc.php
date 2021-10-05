<?php
namespace UniUSM
{
    use UniUSM\Core\Messages\Logger;
    const USM_VERSION = '1.0';

    class Starter
    {
        public static function Run($array)
        {
            foreach ($array as $file)
            {
                if(file_exists($file))
                {
                    include_once "$file";
                    Logger::Send("file:". $file, "include", true);
                }else{
                    Logger::Send("file:". basename($file), "include", false);
                }
            }
        }

        public static function CheckFramework()
        {
            if(clDarkGray != null)
            {
                Logger::Send("Framework:true", "check", true);
                return true;
            }else
            {
                Logger::Send("Framework:false", "check", true);
                return false;
            }
        }
    }

    $files = array(
        "UniUSM/FainexFramework.php",
        "UniUSM/Core/inc.php",
        "UniUSM/Forms/inc.php"
    );

    Starter::Run($files);

    Logger::Send("#############{UniUSM.Close}#############","check",1);
}