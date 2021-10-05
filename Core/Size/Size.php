<?php




namespace UniUSM\Core\Size
{
    use UniUSM\Core\Messages\Logger;

    class Size
    {
        public static function Move($type, $xy, $wh)
        {
            if(is_array($type))
            {
                if($xy != null && is_array($xy))
                {
                    $type->x = $xy['x'];
                    $type->y = $xy['y'];
                }

                if($wh != null && is_array($wh))
                {
                    $type->w = $wh['w'];
                    $type->h = $wh['h'];
                }
            }else{
                if($xy != null && is_array($xy))
                {
                    c($type)->x = $xy['x'];
                    c($type)->y = $xy['y'];
                }

                if($wh != null && is_array($wh))
                {
                    c($type)->w = $wh['w'];
                    c($type)->h = $wh['h'];
                }
            }
            Logger::Send("Type:$type", "move", 1);
        }
    }
}
