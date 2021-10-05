<?php

use UniUSM\Starter;

$files = array(
    "UniUSM/Core/Data/Property.php",
    "UniUSM/Core/Data/Settings.php",
    "UniUSM/Core/Data/Project.php",
    "UniUSM/Core/Data/Backup.php",
    "UniUSM/Core/Data/mainPre.php",
    "UniUSM/Core/Data/testData.php",
);

Starter::Run($files);