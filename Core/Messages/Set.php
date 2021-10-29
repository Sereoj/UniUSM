<?php
namespace UniUSM\Core\Messages
{
    class Set
    {
        public static function Memo($message)
        {
            if($message != null)
            {
                c("Form1->memo1")->text .= $message."\n";
            }
        }
        public static function Editor($message)
        {
            c("Form1->synedit1")->text .= $message."\n";
        }
        public static function Action($message)
        {
            eval($message);
        }
        public static function Clear()
        {
            c("Form1->memo1")->text = null;
            c("Form1->synedit1")->text = null;
        }

        public static function RemoveAt($model)
        {
            if(is_object($model))
            {
                $model->text = null;
            }else
            {
                c($model)->text = null;
            }
        }
    }
}
