<?php


namespace UniUSM\Forms\ProjectManager;


use UniUSM\Core\Component\Component;
use UniUSM\Forms\Customer;

class CustomerManager extends Customer
{

    function create()
    {
        // TODO: Implement create() method.
    }

    function update()
    {
        Component::Hide('NewForm->label2',false);
        Component::Hide('NewForm->label1', false);
        Component::Text('NewForm','Project Manager');
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