<?php

/**
 * Created by PhpStorm.
 * User: paction
 * Date: 27/09/2016
 * Time: 12:07 AM
 */
class Model
{
    public $collection;

    public function __construct($collection)
    {
        $this->collection = Application::app()->db()->db->$collection;
    }
}