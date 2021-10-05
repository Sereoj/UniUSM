<?php


namespace UniUSM\Forms;


abstract class Customer
{
    abstract function create();
    abstract function update();
    abstract function delete();

    abstract public function Run();
}