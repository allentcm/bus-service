<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class ApiController extends Controller
{
    protected $statusCode = 200;

    /**
     * Getter for status code
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Setter for status code
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * Generate Not Found response
     */
    public function respondNotFound($message = 'Not Found!')
    {
        return $this->setStatusCode(BaseResponse::HTTP_NOT_FOUND)->respondWithError($message);
    }

    /**
     * Generate Internal Server Error response
     */
    public function respondInternalError($message = 'Internal Server Error!')
    {
        return $this->setStatusCode(BaseResponse::HTTP_INTERNAL_SERVER_ERROR)->respondWithError($message);
    }

    /**
     * Generate json response
     */
    public function respond($data, $headers = [])
    {
        return Response::json($data, $this->getStatusCode(), $headers);
    }

    /**
     * Generate json response with error
     */
    public function respondWithError($message)
    {
        return $this->respond([
            'error' => [$message]
        ]);
    }

    /**
     * Generate json response with success
     */
    public function respondWithMessage($message = 'SUCCESS')
    {
        return $this->respond([
            'status' => [$message]
        ]);
    }
}
