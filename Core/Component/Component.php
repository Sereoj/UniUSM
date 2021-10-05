<?php

namespace UniUSM\Core\Component
{

    use UniUSM\Core\Size\Size;

    class Component
    {
        public static function Color($type, $value)
        {
            if(is_array($type) && $type != null)
            {
                $type->color = $value;
            }else
            {
                c($type)->color = $value;
            }
        }

        public static function Transparent($type, $transparent)
        {
            if(is_array($type) && $type != null)
            {
                if(is_bool($transparent))
                {
                    $type->transparent = $transparent;
                }
            }else{
                c($type)->transparent = $transparent;
            }
        }

        public static function Move($type, $xy, $wh)
        {
            Size::Move($type, $xy, $wh);
        }

        public static function Text($type, $text)
        {
            if(is_array($type) && $type != null)
            {
                $type->caption = $text;
            }else{
                c($type)->caption = $text;
            }
        }

        public static function Visible($type, $visible)
        {
            if(is_array($type) && $type != null)
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
            if(is_array($type))
            {
                return $type->visible;
            }
        }

        public static function Enable($type, $enable)
        {
            if(is_array($type) && $type != null)
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
            if(is_array($type) && $type != null)
            {
                return $type->enable;
            }
        }

        public static function Is($type)
        {
            if(is_array($type)) return true;
        }

    }
}
