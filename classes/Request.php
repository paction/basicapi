<?php

/**
 * Created by PhpStorm.
 * User: paction
 * Date: 26/09/2016
 * Time: 2:20 PM
 */
class Request
{
    private $uri;
    private $controller;
    private $data;
    private $request;
    private $server;
    
    public function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->request = $_REQUEST;
        $this->server = $_SERVER;
    }

    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Returns name of controller
     * @return string
     */
    public function getController()
    {
        if(empty($this->controller)) {preg_match('/[a-zA-Z]+/', $this->uri, $matches);
            if(!empty($matches) && count($matches) == 1) {
                $this->controller = $matches[0];
            } else {
                $this->controller = 'index';
            }
        }

        return $this->controller;
    }

    public function getMethod()
    {
        return $this->server['REQUEST_METHOD'];
    }

    public function getData()
    {
        return $this->request;
    }
}