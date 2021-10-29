<?php


namespace UniUSM\Forms\UniUSM;


use UniUSM\Core\Component\Component;

use UniUSM\Env\Env\Env;
use UniUSM\Forms\Customer;

class CustomerUniUSM extends Customer
{
    public static function createScrollBox()
    {
        Component::Create(new \TScrollBox(c("Form6")), c("Form6"), "UniUSMPanel");
        Component::Border("Form6->UniUSMPanel", 'bsNone');
        Component::Color("Form6->UniUSMPanel", 0x292929);
        Component::Move("Form6->UniUSMPanel", array("x" => 208, "y" => 32), array("w" =>405, "h" =>248));
        Component::Visible("Form6->UniUSMPanel", false);
    }

    public static function createText()
    {
        $x = 20;
        $w = 150;
        $h = 30;

        $status = Component::Create(new \FLabel(c("Form6->UniUSMPanel")), c("Form6->UniUSMPanel"), "statusUSMLabel");
        Component::Text($status, 'Управление UniUSM');
        Component::setMove($status, array('x' => $x, 'y' => 20), array('w' => $w, 'h' => $h));

        $logger = Component::Create(new \FLabel(c("Form6->UniUSMPanel")), c("Form6->UniUSMPanel"), "loggerUSMLabel");
        Component::Text($logger, 'Логирование: '. Env::Get('Logger'));
        Component::setMove($logger, array('x' => $x, 'y' => 80), array('w' => $w, 'h' => $h));

        $UseStable = Component::Create(new \FLabel(c("Form6->UniUSMPanel")), c("Form6->UniUSMPanel"), "useStableUSMLabel");
        Component::Text($UseStable, 'UseStable: '. Env::Get('UseStable'));
        Component::setMove($UseStable, array('x' => $x, 'y' => 100), array('w' => $w, 'h' => $h));

        $triggle = Component::Create(new \FLabel(c("Form6->UniUSMPanel")), c("Form6->UniUSMPanel"), "triggleUSMLabel");
        Component::Text($triggle, 'Triggle: '. Env::Get('Triggle'));
        Component::setMove($triggle, array('x' => $x, 'y' => 120), array('w' => $w, 'h' => $h));

        $mainPre = Component::Create(new \FLabel(c("Form6->UniUSMPanel")), c("Form6->UniUSMPanel"), "mainPreUSMLabel");
        Component::Text($mainPre, 'MainPre: '. Env::Get('mainPre'));
        Component::setMove($mainPre, array('x' => $x, 'y' => 140), array('w' => $w, 'h' => $h));

        $LowerPanel = Component::Create(new \FLabel(c("Form6->UniUSMPanel")), c("Form6->UniUSMPanel"), "LowerPanelUSMLabel");
        Component::Text($LowerPanel, 'LowerPanel: '. Env::Get('LowerPanel'));
        Component::setMove($LowerPanel, array('x' => $x, 'y' => 160), array('w' => $w, 'h' => $h));
    }

    public static function createButtons()
    {
        $activate = Component::Create(new \FQuickButton(c("Form6->UniUSMPanel")), c("Form6->UniUSMPanel"), "onUSMButton");
        $deactivate = Component::Create(new \FQuickButton(c("Form6->UniUSMPanel")), c("Form6->UniUSMPanel"), "offUSMButton");
        Component::Text($activate, 'Включить');
        Component::Text($deactivate, 'Выключить');

        Component::setMove($activate, array('x' => 20, 'y' => 40), array('w' => 180, 'h' => 30));
        Component::setMove($deactivate, array('x' => 205, 'y' => 40), array('w' => 180, 'h' => 30));

    }

    public function create()
    {
        self::createScrollBox();

        self::createText();
        self::createButtons();
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function delete()
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