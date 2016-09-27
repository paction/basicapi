<?php
/**
 * Created by PhpStorm.
 * User: paction
 * Date: 26/09/2016
 * Time: 2:48 PM
 */

function autoloadClasses()
{
    require_once "classes/Model.php";
    foreach (glob("classes/*.php") as $filename) {
        require_once $filename;
    }
}