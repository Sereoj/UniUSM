<?php
namespace UniUSM\Forms\NewForm;

use UniUSM\Core\Activation\Activation;
use UniUSM\Core\Component\Component;
use UniUSM\Core\Version\Version;
use UniUSM\Forms\Customer;

class CustomerNew extends Customer
{

    function create()
    {
        c("New")->onCreate = function($self){};
        c("New")->onShow = function($self){};

        if(Version::Get() > "020")
        {
            c("New->version")->onExecute = function ($self) {};
        }
    }

    function update()
    {
        Component::Hide('New->label12', false);
        Component::Hide('New->label13', false);
        Component::Hide('New->UserPC', false);

        Component::Text('New->label14', 'P H P  M O D E');

        Component::Text("New->label5", "UniUSM");
        Component::Text("New->label7", "Framework: Fainex Framework");
        Component::Text("New->link1", "UniUSM: Version 1.0");
        Component::Text("New->link2", "Активировать");
        Component::Visible("New->link3", false);
    }

    function delete()
    {
        // TODO: Implement delete() method.
    }

    public function Run()
    {
        $this->create();
        $this->update();
        $this->delete();
    }
}