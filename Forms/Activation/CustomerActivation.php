<?php


namespace UniUSM\Forms\Activation;


use UniUSM\Forms\Customer;

class CustomerActivation extends Customer
{

    public function create()
    {
        c("CheckForm")->onShow = function($self){};
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    function delete()
    {
        // TODO: Implement delete() method.
    }

    function Run()
    {
        $this->create();
        $this->update();
        $this->delete();
    }
}