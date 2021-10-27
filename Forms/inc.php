<?php

namespace UniUSM\Forms
{

    use Settings;
    use UniUSM\Core\Component\Component;
    use UniUSM\Core\Messages\Logger;
    use UniUSM\Core\Version\Version;
    use UniUSM\Data\testData\mainPre;
    use UniUSM\Data\testData\testData;
    use UniUSM\Env\Env\Env;
    use UniUSM\Forms\Main\CustomerMain;
    use UniUSM\Forms\NewForm\CustomerNew;
    use UniUSM\Forms\ProjectManager\CustomerManager;
    use UniUSM\Forms\Settings\CustomerSettings;
    use UniUSM\Forms\UpdateForm\CustomerUpdateForm;
    use UniUSM\Starter;
    use UniUSM\Core\Activation\Activation;

    $files = array(
        "UniUSM/Forms/Customer.php",
        "UniUSM/Forms/New/Run.php",
        "UniUSM/Forms/Activation/Run.php",
        "UniUSM/Forms/Main/Run.php",
        "UniUSM/Forms/ProjectManager/Run.php",
        "UniUSM/Forms/Settings/Run.php",
        "UniUSM/Forms/UpdateForm/Run.php",
        "UniUSM/Forms/UniUSM/Run.php",
    );

    if(Starter::CheckFramework())
    {
        Starter::Run($files);
        if(Activation::IsValidate() && Version::Get() == "020")
        {
            if(Env::Get("UseStable") == "true")
            {
                $GLOBALS['updateForm'] = 'true';
                testData::Set("update");

                Component::Enable("form1->clear", false);
                Logger::Send("Clear:false", "set",1);
            }

            if(Env::Get("mainPre") == "true")
            {
                mainPre::Set(Version::Get());
            }
            $customers = Env::Get("Customer");

            //Customer=[New;Activation;Main;Settings;ProjectManager;UpdateForm]
            if(in_array("New", $customers))
            {
                $newForm = new CustomerNew();
                $newForm->Run();
            }
            if(in_array("Main", $customers))
            {
                $main = new CustomerMain();
                $main->Run();
            }
            if(in_array("Settings", $customers))
            {
                $settings = new CustomerSettings();
                $settings->Run();
            }
            if(in_array("ProjectManager", $customers))
            {
                $manager = new CustomerManager();
                $manager->Run();
            }
            if(in_array("UpdateForm", $customers))
            {
                $updateForm = new CustomerUpdateForm();
                $updateForm->Run();
            }
        }

        if(Activation::IsValidate() && Version::Get() > "020")
        {
            Logger::Send('Version:False', 'get', 0);
            \app::close();
        }
    }
}
