<?php


namespace UniUSM\Core\Component;

class LowerPanel
{
    public static function Set($arg = 1){

        if( self::close_panel($arg) )
        {
            c("Form1->shape3")->hide();
            c("Form1->memo1")->hide();
            c("Form1->label2")->hide();
            c("Form1->label6")->hide();
            c("Form1->label4")->hide();
        }else{
            c("Form1->shape3")->show();
            c("Form1->memo1")->show();
            c("Form1->label2")->show();
            c("Form1->label6")->show();
            c("Form1->label4")->show();

        }
    }

    private static function close_panel($arg = 0){
        return $arg == 1;
    }
}