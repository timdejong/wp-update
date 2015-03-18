<?php

namespace WPU\Utils;

class RegEx {

    /**
     * Get a single value
     *
     * @param string $pattern
     * @param string $str
     * @return string|bool
     */
    public static function get($pattern, $str) {
        preg_match($pattern, $str, $matches);

        return isset($matches[1]) ? $matches[1] : false;
    }

    /**
     * Get all matches
     *
     * @param string $pattern
     * @param string $str
     * @return array|bool
     */
    public static function getAll($pattern, $str) {
        preg_match_all($pattern, $str, $matches);

        return isset($matches[1]) ? $matches : false;
    }

    /**
     * Match only
     *
     * @param string $pattern
     * @param string $str
     * @return bool
     */
    public static function match($pattern, $str) {
        preg_match($pattern, $str, $matches);

        return isset($matches[0]) ? true : false;
    }

    /**
     * Get a list is matches
     *
     * @param string $pattern
     * @param string $str
     * @return array|bool
     */
    public static function getList($pattern, $str) {
        preg_match_all($pattern, $str, $matches);

        return isset($matches[1]) ? $matches[1] : false;
    }

    /**
     * Get a set of matches from a line
     *
     * @param string $pattern
     * @param string $str
     * @return array|bool
     */
    public static function getRow($pattern, $str) {
        preg_match($pattern, $str, $matches);
        unset($matches[0]);
        $matches = array_values($matches);
        return isset($matches[0]) ? $matches : false;
    }

    /**
     * Replace matches
     *
     * @param string $pattern
     * @param string $replace
     * @param string $str
     * @return string|array
     */
    public static function replace($pattern, $replace, $str) {
        return preg_replace($pattern, $replace, $str);
    }

    /**
     * Remove matches
     *
     * @param string $pattern
     * @param string $str
     * @return string|array
     */
    public static function remove($pattern, $str) {
        return self::replace($pattern, '', $str);
    }

}