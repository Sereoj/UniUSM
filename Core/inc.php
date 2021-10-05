<?php
namespace UniUSM\Core
{

    use UniUSM\Starter;

    $files = array(
        "UniUSM/Core/Env/Env.php",
        "UniUSM/Core/Activation/Activation.php",
        "UniUSM/Core/Version/Version.php",
        "UniUSM/Core/Data/inc.php",
        "UniUSM/Core/Size/Size.php",
        "UniUSM/Core/Size/Font.php",
        "UniUSM/Core/Messages/Set.php",
        "UniUSM/Core/Component/Component.php",
        "UniUSM/Core/Component/LowerPanel.php",
        "UniUSM/Core/Component/Triggle.php",
        "UniUSM/Core/Generator/Generator.php",
        "UniUSM/Core/Json/Json.php"
    );

    if(Starter::CheckFramework())
    {
        Starter::Run($files);
    }


}