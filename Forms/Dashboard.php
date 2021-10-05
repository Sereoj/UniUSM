<?php


namespace UniUSM\Forms;


abstract class Dashboard
{
    abstract protected function createUI();
    abstract protected function show();
    abstract protected function close();
    abstract protected function Run();
}