<?php


namespace UniUSM\Core\Component;


class Triggle
{
    public static function Show()
    {
        $FLabel = new \FLabel(c("Form1"),
            array (
                'label' => array
                (
                    'autoSize' => false,
                    'anchors' => 'akRight,akBottom',
                    'layout' => tlCenter,
                    'alignment' => taCenter,
                    'font' => array(
                        'name' => 'Calibri',
                        'size' => '11',
                        'color' => '0xE6E6E6'
                    ),
                    'cursor' => crHandPoint,
                    'x' => 852,
                    'y' => 578,
                    'w' => 24,
                    'h' => 24,

                    'caption' => ".:"
                )
            )
        );

        $FLabel->onMousedown = function ($self) use ($FLabel)
        {
            $FLabel->caption = ".:";
            func()->ReleaseCapture();
            c("Form1")->perform(0x112, 0xF008, 0);
        };
        $FLabel->onMouseup = function ($self) use ($FLabel)
        {
            $FLabel->caption = ".:";
        };
        unset($FLabel);
    }
}