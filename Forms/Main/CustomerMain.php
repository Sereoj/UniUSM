<?php


namespace UniUSM\Forms\Main;

use UniUSM\Core\Component\Component;
use UniUSM\Core\Component\LowerPanel;
use UniUSM\Core\Component\Triggle;
use UniUSM\Core\Size\Size;
use UniUSM\Data\testData\testData;
use UniUSM\Env\Env\Env;
use UniUSM\Forms\Customer;

class CustomerMain extends Customer
{
    public function create()
    {
        TSynT("syntax.json", c("form1->synedit1"));

        gui_propSet(c("form1->synedit1")->gutter, 'Color', 2434598);
        gui_propSet(c("form1->synedit1")->gutter, 'BorderColor', 1855151);
        gui_propSet(c("form1->synedit1")->gutter, 'LeftOffset', 6);
        gui_propSet(c("form1->synedit1")->gutter, 'RightOffset', 6);

        gui_propSet(gui_propGet(c("form1->synedit1")->gutter, 'Font'), 'Color', 15132390);
        gui_propSet(gui_propGet(c("form1->synedit1")->gutter, 'Font'), 'Size', 10);


        c("form1->synedit1")->autoSize = false;
        c("form1->synedit1")->borderStyle = 'bsNone';
        c("form1->synedit1")->wordWrap = true;
        c("form1->synedit1")->showLineNumbers = true;
        c("form1->synedit1")->alwaysShowCaret = true;
        c("form1->synedit1")->scrollBars = 'ssVertical';
        c("form1->synedit1")->wantReturns = true;

        c("form1")->onShow = function($self){};

        c("form1->synedit1")->onchange = function ($self) {};
        c("form1->synedit1")->onMouseDown = function ($self) {};

        c("form1->label7")->onClick = function($self){
            if( c("Form1")->clientWidth >= 550 && c("Form1")->clientHeight >= 450 )
            {
                testData::Set("true");
                c("form1->Ford")->enable = true;
            }
        };

        c("form1->label4")->onClick = function($self){

        };


        $button = new \FQuickButton (c("Form1"));
        $button->x = 736;
        $button->y = 8;
        $button->w = 30;
        $button->h = 22;
        $button->caption = '<>';
        $button->name = "lablPl";
        $button->anchors = 'akRight,akTop';
        $button->onClick = function ($self)
        {
            if( c("Form1->memo1")->visible == true){
                c("Form1->SynEdit1")->h += 100;
                LowerPanel::Set(1);
            }else{
                c("Form1->SynEdit1")->h -= 100;
                LowerPanel::Set(0);
            }
        };
    }

    public function update()
    {
        if(Env::Get('SynEdit.Hide') == 'true')
        {
            Component::Hide("form1->synedit1", false);
        }

        Component::Hide("form1->edit2", false);
        Component::Hide("form1->label10", false);
        Component::Hide("form1->image2", false);
        Component::Hide("form1->label13", false);
        Component::Hide("form1->label9", false);

        Component::Transparent("form1->label12", true);

        Size::Move("form1->shape4", array("x" => 792, "y" => 40), array("h" => 552, "w" => 70));
        Size::Move("form1->shape1", array("x" => 8, "y" => 40), array("h" => 552, "w" => 776));
        Size::Move("form1->shape3", array("x" => 8, "y" => 496), array("h" => 96, "w" => 776));
        Size::Move("form1->memo1", array("x" => 16, "y" => 512), array("h" => 64, "w" => 766-8));
        Size::Move("form1->label4", array("x" => 632+60, "y" => 497), null);
        Size::Move("form1->synEdit1", array("x" => 9, "y" => 41), array("h" => 450, "w" => 773));

        Size::Move("form1->TForm8", array("x" => 800, "y" => 45), array("h" => 24, "w" => 56));
        Size::Move("form1->label14", array("x" => 800, "y" => 75), array("h" => 24, "w" => 56));
        Size::Move("form1->label7", array("x" => 800, "y" => 510), array("h" => 24, "w" => 56));
        Size::Move("form1->label11", array("x" => 800, "y" => 535), array("h" => 24, "w" => 56));


        Component::Text("form1->TForm8", "Выпол.");
        Component::Text("form1->label14", "Доп.");
        Component::Text("form1->label7", "Настр.");
        Component::Text("form1->label11", "Обнов.");


        if(Env::Get('Triggle') == 'true')
            Triggle::Show();

        if(Env::Get('LowerPanel') == 'true'){
            LowerPanel::Set(1);
            c("Form1->SynEdit1")->h += 100;
        }
    }

    public function delete()
    {
        c("Form1->image4")->name ? c("Form1->image4")->free() : false;
        c("Form1->image3")->name ? c("Form1->image3")->free() : false;
    }

    public function Run()
    {
        $this->create();
        $this->update();
        $this->delete();
    }
}