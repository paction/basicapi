<?php

/**
 * Created by PhpStorm.
 * User: paction
 * Date: 26/09/2016
 * Time: 2:24 PM
 */
class Config
{
    private $data = [
        'serverAddress',
        'responseContentType',
        'controllers',
        'secretKey'
    ];

    public function __construct()
    {
        $this->data = [
            'serverAddress' => 'http://basicapi',
            'responseContentType' => 'application/json',
            'controllers' => ['index', 'Timestamp', 'Transaction', 'TransactionStats', 'ScorePost'],
            'secretKey' => 'NwvprhfBkGuPJnjJp77UPJWJUpgC7mLz'
        ];
    }

    public function getData($key)
    {
        if(isset($this->data[$key])) {
            return $this->data[$key];
        } else {
            Application::app()->respond()->sendError('Bay config key');
        }

    }
}