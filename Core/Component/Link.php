<?php


namespace UniUSM\Core\Component;


class Link
{
    public static function Show($form,$text, $url)
    {
        $link = new \TLinkLabel($form);
        $link->parent = $form;
        $link->caption = $text;
        $link->link = $url;
        $link->show();
    }

    public static function Parse($string)
    {
    }
}