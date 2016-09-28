<?php

/**
 * Created by PhpStorm.
 * User: paction
 * Date: 26/09/2016
 * Time: 2:55 PM
 */
class Application
{
    private $request;
    private $config;
    private $respond;
    private $db;

    public function __construct()
    {
        if(empty($this->config)) $this->config = new Config();
        if(empty($this->request)) $this->request = new Request();
        if(empty($this->respond)) $this->respond = new Respond();
        if(empty($this->db)) $this->db = new DB();
    }

    public function run()
    {
        $controller = $this->request()->getController();

        if(!in_array($controller, $this->config('controllers'))) {
            self::respond()->sendError('Bad controller');
        } else {
            $result = call_user_func([$this, $controller . 'ControllerAction']);
        }

        if(is_string($result)) {
            $result = [$result];
        }

        self::respond()->send($result);
    }

    public function config($key = null)
    {
        if($key) {
            return $this->config->getData($key);
        }

        return $this->config;
    }

    public function request()
    {
        return $this->request;
    }

    public function respond()
    {
        return $this->respond;
    }

    public function db()
    {
        return $this->db;
    }

    public static function app()
    {
        return new self;
    }

    // Controller Actions

    public function indexControllerAction()
    {
        return "It works";
    }

    public function TimestampControllerAction()
    {
        return (object)['Timestamp' => time()];
    }

    public function TransactionControllerAction()
    {
        $model = new TransactionModel();
        $r = $model->CreateTransaction($this->_initInput());
        $this->_handleResponse($r);
    }

    public function TransactionStatsControllerAction()
    {
        $model = new TransactionModel();
        $r = $model->TransactionStats($this->_initInput());
        $this->_handleResponse($r);
    }

    public function ScorePostControllerAction()
    {
        $model = new LeaderBoardModel();
        $r = $model->ScorePost($this->_initInput());
        $this->_handleResponse($r);
    }

    public function LeaderboardGetControllerAction()
    {
        $model = new LeaderBoardModel();
        $r = $model->Leaderboard($this->_initInput());
        $this->_handleResponse($r);
    }

    private function _initInput()
    {
        if($this->request()->getMethod() != 'GET') {
            $this->respond()->sendError('Bad method');
        }

        return $this->request()->getData();
    }

    private function _handleResponse($r)
    {
        if(isset($r['ok']) && $r['ok'] == 1) {
            return $r['result'];
        } else {
            $this->respond()->sendError('Error: ' . $r['errmsg']);
        }
    }
}