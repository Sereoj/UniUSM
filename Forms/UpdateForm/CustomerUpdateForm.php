<?php


namespace UniUSM\Forms\UpdateForm;


use UniUSM\Core\Component\Component;
use UniUSM\Forms\Customer;

class CustomerUpdateForm extends Customer
{

    function create()
    {
        // TODO: Implement create() method.
    }

    function update()
    {
        Component::Visible("updateForm->label_update", true);
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