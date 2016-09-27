<?php

/**
 * Created by PhpStorm.
 * User: paction
 * Date: 27/09/2016
 * Time: 12:13 AM
 */
class Validators
{
    public static function validateInteger($key = null, $array = null, $positiveRequired = false)
    {
        if(!isset($array[$key]) || !is_numeric($array[$key]) || ($positiveRequired && $array[$key] < 1)) {
            return false;
        }
        return true;
    }
}