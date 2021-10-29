<?php

namespace UniUSM\Core\Component
{

    use UniUSM\Core\Size\Size;

    class Component
    {

        public static function Create($control, $form, $name)
        {
            $template = $control;
            $template->name = $name;
            $template->parent = $form;
            //$template->show();

            return $template;

        }

        public static function Color($type, $value)
        {
            if(is_object($type))
            {
                $type->color = $value;
            }else
            {
                c($type)->color = $value;
            }
        }

        public static function Transparent($type, $transparent)
        {
            if(is_object($type))
            {
                if(is_bool($transparent))
                {
                    $type->transparent = $transparent;
                }
            }else{
                c($type)->transparent = $transparent;
            }
        }

        public static function Border($control, $type)
        {
            if($control != null && $type != null)
            {
                c($control)->borderStyle = $type;
            }
        }

        public static function getBorder($control)
        {
            return c($control)->borderStyle;
        }

        public static function Move($type, $xy, $wh)
        {
            Size::Move($type, $xy, $wh);
        }

        public static function setMove($type, $xy, $wh)
        {
            $type->x = $xy['x'];
            $type->y = $xy['y'];
            $type->w = $wh['w'];
            $type->h = $wh['h'];
        }

        public static function Text($type, $text)
        {
            if(is_object($type))
            {
                $type->caption = $text;
            }else{
                c($type)->caption = $text;
            }
        }

        public static function Visible($type, $visible)
        {
            if(is_object($type))
            {
                if(is_bool($visible))
                {
                    $type->visible = $visible;
                }
            }else{
                c($type)->visible = $visible;
            }
        }

        public static function isVisible($type)
        {
            if(is_object($type))
            {
                return $type->visible;
            }else{
                return c($type)->visible;
            }
        }

        public static function Enable($type, $enable)
        {
            if(is_object($type) && $type != null)
            {
                if(is_bool($enable)){
                    $type->enable = $enable;
                }
            }else{
                c($type)->enable = $enable;
            }
        }

        public static function Hide($type, $enable)
        {
            self::Visible($type, $enable);
            self::Enable($type, $enable);
        }

        public static function isEnable($type)
        {
            if(is_object($type) && $type != null)
            {
                return $type->enable;
            }
        }

        public static function Is($type)
        {
            if(is_object($type)) return true;
        }

    }
}
