<?php

/**
 * Created by PhpStorm.
 * User: paction
 * Date: 26/09/2016
 * Time: 2:28 PM
 */
class Respond
{
    private function setHeader($code)
    {
        header('Content-Type: ' . Application::app()->config()->getData('responseContentType'), true, $code);
    }

    /**
     * Sends response with specified data and http code, and then terminates application
     * @param array $data
     * @param int $code
     */
    public function send($data, $code = 200)
    {
        $this->setHeader($code);
        // Respond with JSON
        echo json_encode($data);

        // Respond with other type

        exit();
    }

    public function sendError($msg, $code = 400)
    {
        $this->setHeader($code);
        // Respond with JSON
        echo json_encode(['Error' => true, 'ErrorMessage' => $msg]);

        // Respond with other type

        exit();
    }
}