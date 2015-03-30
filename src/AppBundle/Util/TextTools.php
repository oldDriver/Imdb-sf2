<?php
namespace AppBundle\Util;

class TextTools
{
    /**
     * @param string $string
     * @return string
     */
    public static function cleanString($string)
    {
        $string = preg_replace('/[\n\r\t]+/', '', $string);
        $string = preg_replace('/\s+/', ' ', $string);
        $string = str_replace(' ', ' ', $string);
        return trim($string);
    }

    /**
     * @param string $text
     * @return mixed
     */
    public static function stripText($text)
    {
        if (is_array($text)) {
            $text = implode(' ', $text);
        }
        $text = strtolower($text);
        $text = str_replace(
            array(
                'À',
                'Á',
                'Â',
                'Ã',
                'Å',
                'Ą',
                'à',
                'á',
                'â',
                'ã',
                'å',
                'ą'
            ),
            'a',
            $text
        );
        $text = str_replace(
            array(
                'Æ',
                'Ä',
                'æ',
                'ä',
            ),
            'ae',
            $text
        );
        $text = str_replace(
            array(
                'Ç',
                'Ć',
                'Ĉ',
                'Č',
                'ç',
                'ć',
                'ĉ',
                'č',
            ),
            'c',
            $text
        );
        $text = str_replace(
            array(
                'È',
                'É',
                'Ê',
                'Ë',
                'Ē',
                'Ė',
                'Ę',
                'Ě',
                'Ә',
                'è',
                'é',
                'ê',
                'ë',
                'ē',
                'ė',
                'ę',
                'ě',
                'ә'
            ),
            'e',
            $text
        );
        $text = str_replace(
            array(
                'Ì',
                'Í',
                'Î',
                'Ï',
                'Ī',
                'Į',
    
                'ì',
                'í',
                'î',
                'ï',
                'ī',
                'į'
            ),
            'i',
            $text
        );
        $text = str_replace(
            array(
                'Ĺ',
                'Ļ',
                'Ľ',
                'Ł',
                'ĺ',
                'ļ',
                'ł'
            ),
            'l',
            $text
        );
        $text = str_replace(
            array(
                'Ń',
                'Ņ',
                'Ň',
                'ń',
                'ņ',
                'ň'
            ),
            'n',
            $text
        );
        $text = str_replace(
            array(
                'Ñ',
                'ñ'
            ),
            'nn',
            $text
        );
        $text = str_replace(
            array(
                'Ò',
                'Ó',
                'Ô',
                'ò',
                'ó',
                'ô'
            ),
            'o',
            $text
        );
        $text = str_replace(
            array(
                'Ö',
                'Ő',
                'Ø',
                'Œ',
                'ö',
                'ő',
                'ø',
                'œ'
            ),
            'oe',
            $text
        );
        $text = str_replace('ß', 'ss', $text);
        $text = str_replace(
            array(
                'Ŕ',
                'Ř',
                'ŕ',
                'ř',
            ),
            'r',
            $text
        );
        $text = str_replace(
            array(
                'Ś',
                'Ŝ',
                'Ş',
                'Š',
                'ś',
                'ŝ',
                'ş',
                'š'
            ),
            's',
            $text
        );
        $text = str_replace(
            array(
                'Þ',
                'þ'
            ),
            'th',
            $text
        );
        $text = str_replace(
            array(
                'Ţ',
                'Ț',
                'Ť',
                'Ŧ',
                'ţ',
                'ț',
                'ŧ'
            ),
            't',
            $text
        );
        $text = str_replace(
            array(
                'Ù',
                'Ú',
                'Û',
                'Ů',
                'Ų',
                'Ʉ',
                'ù',
                'ú',
                'û',
                'ų',
                'ʉ'
            ),
            'u',
            $text
        );
        $text = str_replace(
            array(
                'Ü',
                'ü'
            ),
            'ue',
            $text
        );
        $text = str_replace(
            array(
                'Ý',
                'Ÿ',
                'ý',
                'ÿ'
            ),
            'y',
            $text
        );
        $text = str_replace(
            array(
                'Ź',
                'Ż',
                'Ž',
                'ź',
                'ż',
                'ž'
            ),
            'z',
            $text
        );
        $text = str_replace(
            array(
                'Ƶ',
                'ƶ'
            ),
            'zz',
            $text
        );
        //strip all non word chars
        $text = preg_replace('/\W/', ' ', $text);
        $text = trim($text);
        // add delimiter
        $text = preg_replace('/\s+/', '-', $text);
        return $text;
    }
}