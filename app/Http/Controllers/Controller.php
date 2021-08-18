<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    private $status = 200;
    private $success = true;
    private $message = '';
    private $response = [];



    /**
     * @param $response(Array/String), $success(int), $status(Boolean), $message(String)
     */
    public function sendResponse($response, $success = null, $status = null, $message = null)
    {
        return response([
            'status' => $status ?? $this->status,
            'success' => $success ?? $this->success,
            'message' => $message ?? $this->message,
            'response' => $response ?? $this->response,
        ], $status ?? $this->status);
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setSuccess($success)
    {
        $this->success = $success;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }


}
