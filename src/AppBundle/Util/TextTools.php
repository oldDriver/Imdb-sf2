<?php
namespace AppBundle\Util;

class TextTools
{
    public static function cleanString($string)
    {
        $string = preg_replace('/[\n\r\t]+/', '', $string);
        $string = preg_replace('/\s+/', ' ', $string);
        $string = str_replace(' ', ' ', $string);
        return trim($string);
    }
}