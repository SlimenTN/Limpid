<?php

namespace framework\core\Functions;

/**
 * Class LimpidFunctions
 * @package framework\core\Functions
 */
class LimpidFunctions
{
    /**
     * Generate slug
     * @param $str
     * @param array $replace
     * @param string $delimiter
     * @return string
     *
     * Referenced: @tlongren <GitHub: https://gist.github.com/tlongren/5527129>
     */
    public static function slugit($str, $replace = array(), $delimiter = '-')
    {
        setlocale(LC_ALL, 'en_US.UTF8');

        if (!empty($replace)) {
            $str = str_replace((array)$replace, ' ', $str);
        }
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
        return $clean;
    }
}