<?php


namespace Alien\Stdlib;

use InvalidArgumentException;

/**
 * Library class for useful string methods
 */
class StringFunctions
{

    /**
     * Removes any namespace information from string
     *
     * Example string: <code>\Application\Model\Customer</code><br>
     * results in string: <code>Customer</code>.
     *
     * @param string $className
     * @return string
     * @throws InvalidArgumentException when given argument is not string
     */
    public static function stripNamespace($className)
    {
        if (!is_string($className)) {
            throw new InvalidArgumentException("Given argument is not string!");
        }
        $nsw = explode('\\', $className);
        return end($nsw);
    }

    /**
     * Finds and replaces any special character in string to standard letter or number
     * @param string $string
     * @return string
     */
    public static function convertAccentsAndSpecialToNormal($string)
    {
        $table = array(
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Ă' => 'A', 'Ā' => 'A', 'Ą' => 'A', 'Æ' => 'A', 'Ǽ' => 'A',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'ă' => 'a', 'ā' => 'a', 'ą' => 'a', 'æ' => 'a', 'ǽ' => 'a',

            'Þ' => 'B', 'þ' => 'b', 'ß' => 'Ss',

            'Ç' => 'C', 'Č' => 'C', 'Ć' => 'C', 'Ĉ' => 'C', 'Ċ' => 'C',
            'ç' => 'c', 'č' => 'c', 'ć' => 'c', 'ĉ' => 'c', 'ċ' => 'c',

            'Đ' => 'Dj', 'Ď' => 'D', 'Đ' => 'D',
            'đ' => 'dj', 'ď' => 'd',

            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ĕ' => 'E', 'Ē' => 'E', 'Ę' => 'E', 'Ė' => 'E',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ĕ' => 'e', 'ē' => 'e', 'ę' => 'e', 'ė' => 'e',

            'Ĝ' => 'G', 'Ğ' => 'G', 'Ġ' => 'G', 'Ģ' => 'G',
            'ĝ' => 'g', 'ğ' => 'g', 'ġ' => 'g', 'ģ' => 'g',

            'Ĥ' => 'H', 'Ħ' => 'H',
            'ĥ' => 'h', 'ħ' => 'h',

            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'İ' => 'I', 'Ĩ' => 'I', 'Ī' => 'I', 'Ĭ' => 'I', 'Į' => 'I',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'į' => 'i', 'ĩ' => 'i', 'ī' => 'i', 'ĭ' => 'i', 'ı' => 'i',

            'Ĵ' => 'J',
            'ĵ' => 'j',

            'Ķ' => 'K',
            'ķ' => 'k', 'ĸ' => 'k',

            'Ĺ' => 'L', 'Ļ' => 'L', 'Ľ' => 'L', 'Ŀ' => 'L', 'Ł' => 'L',
            'ĺ' => 'l', 'ļ' => 'l', 'ľ' => 'l', 'ŀ' => 'l', 'ł' => 'l',

            'Ñ' => 'N', 'Ń' => 'N', 'Ň' => 'N', 'Ņ' => 'N', 'Ŋ' => 'N',
            'ñ' => 'n', 'ń' => 'n', 'ň' => 'n', 'ņ' => 'n', 'ŋ' => 'n', 'ŉ' => 'n',

            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ō' => 'O', 'Ŏ' => 'O', 'Ő' => 'O', 'Œ' => 'O',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ō' => 'o', 'ŏ' => 'o', 'ő' => 'o', 'œ' => 'o', 'ð' => 'o',

            'Ŕ' => 'R', 'Ř' => 'R',
            'ŕ' => 'r', 'ř' => 'r', 'ŗ' => 'r',

            'Š' => 'S', 'Ŝ' => 'S', 'Ś' => 'S', 'Ş' => 'S',
            'š' => 's', 'ŝ' => 's', 'ś' => 's', 'ş' => 's',

            'Ŧ' => 'T', 'Ţ' => 'T', 'Ť' => 'T',
            'ŧ' => 't', 'ţ' => 't', 'ť' => 't',

            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ũ' => 'U', 'Ū' => 'U', 'Ŭ' => 'U', 'Ů' => 'U', 'Ű' => 'U', 'Ų' => 'U',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ũ' => 'u', 'ū' => 'u', 'ŭ' => 'u', 'ů' => 'u', 'ű' => 'u', 'ų' => 'u',

            'Ŵ' => 'W', 'Ẁ' => 'W', 'Ẃ' => 'W', 'Ẅ' => 'W',
            'ŵ' => 'w', 'ẁ' => 'w', 'ẃ' => 'w', 'ẅ' => 'w',

            'Ý' => 'Y', 'Ÿ' => 'Y', 'Ŷ' => 'Y',
            'ý' => 'y', 'ÿ' => 'y', 'ŷ' => 'y',

            'Ž' => 'Z', 'Ź' => 'Z', 'Ż' => 'Z', 'Ž' => 'Z',
            'ž' => 'z', 'ź' => 'z', 'ż' => 'z', 'ž' => 'z',

            '“' => '"', '”' => '"', '‘' => "'", '’' => "'", '•' => '-', '…' => '...', '—' => '-', '–' => '-', '¿' => '?', '¡' => '!', '°' => ' degrees ',
            '¼' => ' 1/4 ', '½' => ' 1/2 ', '¾' => ' 3/4 ', '⅓' => ' 1/3 ', '⅔' => ' 2/3 ', '⅛' => ' 1/8 ', '⅜' => ' 3/8 ', '⅝' => ' 5/8 ', '⅞' => ' 7/8 ',
            '÷' => ' divided by ', '×' => ' times ', '±' => ' plus-minus ', '√' => ' square root ', '∞' => ' infinity ',
            '≈' => ' almost equal to ', '≠' => ' not equal to ', '≡' => ' identical to ', '≤' => ' less than or equal to ', '≥' => ' greater than or equal to ',
            '←' => ' left ', '→' => ' right ', '↑' => ' up ', '↓' => ' down ', '↔' => ' left and right ', '↕' => ' up and down ',
            '℅' => ' care of ', '℮' => ' estimated ',
            'Ω' => ' ohm ',
            '♀' => ' female ', '♂' => ' male ',
            '©' => ' Copyright ', '®' => ' Registered ', '™' => ' Trademark ',
        );

        $string = strtr($string, $table);
        // Currency symbols: £¤¥€  - we dont bother with them for now
        $string = preg_replace("/[^\x9\xA\xD\x20-\x7F]/u", "", $string);

        return $string;
    }

    /**
     * Strips non printable characters from string
     * @param string $string
     * @return string
     */
    public static function stripNonPrintableChars($string)
    {
        return preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $string);
    }

    /**
     * Tests if an input is valid PHP serialized string.
     *
     * Checks if a string is serialized using quick string manipulation
     * to throw out obviously incorrect strings. Unserialize is then run
     * on the string to perform the final verification.
     *
     * Valid serialized forms are the following:
     * <ul>
     * <li>boolean: <code>b:1;</code></li>
     * <li>integer: <code>i:1;</code></li>
     * <li>double: <code>d:0.2;</code></li>
     * <li>string: <code>s:4:"test";</code></li>
     * <li>array: <code>a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}</code></li>
     * <li>object: <code>O:8:"stdClass":0:{}</code></li>
     * <li>null: <code>N;</code></li>
     * </ul>
     *
     * This program is free software. It comes without any warranty, to
     * the extent permitted by applicable law. You can redistribute it
     * and/or modify it under the terms of the Do What The Fuck You Want
     * To Public License, Version 2, as published by Sam Hocevar. See
     * http://sam.zoy.org/wtfpl/COPYING for more details.
     *
     * @author        Chris Smith <code+php@chris.cs278.org>
     * @copyright    Copyright (c) 2009 Chris Smith (http://www.cs278.org/)
     * @license        http://sam.zoy.org/wtfpl/ WTFPL
     * @param        string $value Value to test for serialized form
     * @param        mixed $result Result of unserialize() of the $value
     * @return        boolean            True if $value is serialized data, otherwise false
     */
    public static function isSerialized($value, &$result = null)
    {
        // Bit of a give away this one
        if (!is_string($value)) {
            return false;
        }

        // Serialized false, return true. unserialize() returns false on an
        // invalid string or it could return false if the string is serialized
        // false, eliminate that possibility.
        if ($value === 'b:0;') {
            $result = false;
            return true;
        }

        $length = strlen($value);
        $end = '';

        switch ($value[0]) {
            case 's':
                if ($value[$length - 2] !== '"') {
                    return false;
                }
            case 'b':
            case 'i':
            case 'd':
                // This looks odd but it is quicker than isset()ing
                $end .= ';';
            case 'a':
            case 'O':
                $end .= '}';

                if ($value[1] !== ':') {
                    return false;
                }

                switch ($value[2]) {
                    case 0:
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                        break;

                    default:
                        return false;
                }
            case 'N':
                $end .= ';';

                if ($value[$length - 1] !== $end[0]) {
                    return false;
                }
                break;

            default:
                return false;
        }

        if (($result = @unserialize($value)) === false) {
            $result = null;
            return false;
        }
        return true;
    }
}