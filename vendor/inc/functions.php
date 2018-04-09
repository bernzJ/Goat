<?php
namespace Goat;

class Generic
{
    public static function safe_url_escape($raw)
    {
        return urlencode($raw);
    }
    public static function safe_escape_path($raw)
    {
        return preg_replace('/[^A-Za-z0-9_\-]/', '_', $raw);
    }
    public static function safe_escape_name($raw)
    {
        return htmlspecialchars(preg_replace("/[^A-Za-z0-9?!]/", '', $raw));
    }
    public static function safe_array($array)
    {
        for ($i = 0; $i < count($array); $i++) {
            $array[$i] = self::safe_escape_name($array[$i]);
        }
        return $array;
    }
    public static function safe_email_escape($data)
    {
        if (filter_var($data, FILTER_VALIDATE_EMAIL)) {
            return $data;
        } else {
            return false;
        }

    }
    /*
     * This benchs faster than spl_autoload functions.
     * */
    public static function glob_recursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {

            $files = array_merge($files, self::glob_recursive($dir . '/' . basename($pattern), $flags));
        }
        array_walk($files, function ($f) {
            require_once ($f);
        });
        return $files;
    }

    public static function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    public static function generateRandomString($length = 50)
    {
        if (!is_numeric($length)) {
            $length = 50;
        }

        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }
}
