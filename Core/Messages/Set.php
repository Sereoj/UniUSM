<?php
namespace UniUSM\Core\Messages
{
    class Set
    {
        public static function Memo($message)
        {
            if($message != null)
            {
                c("Form1->memo1")->text = $message;
            }
        }
        public static function Editor($message)
        {
            if($message != null)
            {
                c("Form1->synedit1")->text = $message;
            }
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
            if(isset($model) && $model->name != null)
            {
                $model->text = null;
            }
        }
    }
}
