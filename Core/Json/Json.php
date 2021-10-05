<?php


class Json
{
    public static function Encode($data)
    {
        return json_encode($data);
    }
    public static function Decode($data)
    {
        return json_decode($data);
    }
}