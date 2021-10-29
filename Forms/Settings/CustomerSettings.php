<?php


namespace UniUSM\Forms\Settings;

use UniUSM\Core\Component\Component;
use UniUSM\Core\Messages\Set;
use UniUSM\Core\Size\Size;
use UniUSM\Core\Version\Version;
use UniUSM\Env\Env\Env;
use UniUSM\Forms\Customer;

class CustomerSettings extends Customer
{
    function create()
    {
        Component::Visible("Form6->scrollBox1",true);

        c("form6")->onShow = function($self){
            global $data432;
            $data432 = "info";
        };

        c("Form6->label2")->onClick = function($self){


            if(Component::isVisible("Form6->scrollBox1"))
            {
                Component::Visible("Form6->scrollBox1",false);
            }else
            {
                Component::Visible("Form6->scrollBox1",true);
            }

            if(file_exists("syntax.json"))
            {
                $syn = json_decode(file_get_contents ("syntax.json"), true);

                c("Form6->syn_1")->text = $syn->Comment->foreground;
                c("Form6->syn_2")->text = $syn->Identifier->foreground;
                c("Form6->syn_3")->text = $syn->Key->foreground;
                c("Form6->syn_4")->text = $syn->Number->foreground;
                c("Form6->syn_5")->text = $syn->Space->foreground;
                c("Form6->syn_6")->text = $syn->String->foreground;
                c("Form6->syn_7")->text = $syn->Symbol->foreground;
                c("Form6->syn_8")->text = $syn->Variable->foreground;
                c("Form6->syn_9")->text = $syn->main->color;

                for ($i = 1; $i < 9; $i++){
                    c("Form6->shape_syn" . $i)->brushColor = c("Form6->syn_" . $i)->text;
                }

                c("Form6->shape_Main")->brushColor = c("Form6->syn_9")->text;
            }
        };
        c("Form6->label5")->onClick = function ($self)
        {
            if(Component::isVisible("Form6->UniUSMPanel"))
            {
                Component::Visible("Form6->UniUSMPanel", false);
            }else
            {
                Component::Visible("Form6->UniUSMPanel", true);
            }
        };
        c("Form6->label7")->onClick = function($self) {
            $data = array();

            $data['Comment']['style'] = "fsItalic";
            $data['Comment']['background'] = 1776411;
            $data['Comment']['foreground'] = c("Form6->syn_1")->text;

            $data['Identifier']['style'] = "";
            $data['Identifier']['background'] = 536870911;
            $data['Identifier']['foreground'] = c("Form6->syn_2")->text;

            $data['Key']['style'] = "";
            $data['Key']['background'] = 536870911;
            $data['Key']['foreground'] = c("Form6->syn_3")->text;

            $data['Number']['style'] = "";
            $data['Number']['background'] = 536870911;
            $data['Number']['foreground'] = c("Form6->syn_4")->text;

            $data['Space']['style'] = "";
            $data['Space']['background'] = 536870911;
            $data['Space']['foreground'] = c("Form6->syn_5")->text;

            $data['String']['style'] = "";
            $data['String']['background'] = 536870911;
            $data['String']['foreground'] = c("Form6->syn_6")->text;

            $data['Symbol']['style'] = "";
            $data['Symbol']['background'] = 536870911;
            $data['Symbol']['foreground'] = c("Form6->syn_7")->text;

            $data['Variable']['style'] = "";
            $data['Variable']['background'] = 536870911;
            $data['Variable']['foreground'] = c("Form6->syn_8")->text;

            $data['main']['color'] = c("Form6->syn_9")->text;
            file_put_contents("syntax.json",json_encode($data));

            TSynT("syntax.json", c("form1->synedit1"));
        };
    }

    function update()
    {
        Component::Text("Form6->label1", "Настройки");

        Component::Text('Form6->label5', 'USM Config');

        Size::Move("Form6->shape1", array('x' =>8, 'y' =>24), array('w' => 605, 'h'=> 368));
        Size::Move("Form6->label5", array('x' => 8, 'y' => c("Form6->label5")->y), array('w' =>192 , 'h' =>32 ));
        Size::Move("Form6->label6", array('x' => 8, 'y' => c("Form6->label6")->y), array('w' =>192 , 'h' =>32 ));
        Size::Move("Form6->label2", array('x' => 8, 'y' => c("Form6->label2")->y), array('w' =>192 , 'h' =>32 ));
        Size::Move("Form6->label13", null, array('w' =>192 , 'h' =>32 ));
        Size::Move("Form6->label14", null, array('w' =>192 , 'h' =>32 ));
        Size::Move("Form6->label15", null, array('w' =>192 , 'h' =>32 ));

        Size::Move("Form6->label11", null, array('w' =>54 , 'h' =>24 ));
        Size::Move("Form6->label8", null, array('w' =>54 , 'h' =>24 ));
        Size::Move("Form6->exit", array('x' =>584 , 'y' =>0 ) , array('w' =>40 , 'h' =>24 ));

        Component::Text("Form6->label2", "Редактор");

        Component::Color("Form6->label5", 0x242424);
        Component::Color("Form6->label6", 0x242424);
        Component::Color("Form6->label5", 0x242424);
        Component::Color("Form6->label2", 0x242424);
        Component::Color("Form6->label13", 0x242424);
        Component::Color("Form6->label14", 0x242424);
        Component::Color("Form6->label15", 0x242424);


        Component::Hide('Form6->shape1', false);
        Component::Visible("Form6->label8", false);
        Component::Visible("Form6->label11", false);

    }

    function delete()
    {
        if(file_exists("data/phpsyn.xml"))
        {
            unlink("data/phpsyn.xml");
        }
    }

    public function Run()
    {
        $this->create();
        $this->update();
        $this->delete();
    }
}