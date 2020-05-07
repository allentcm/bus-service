<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection as FractalCollection;
use Symfony\Component\HttpFoundation\Response as BaseResponse;


class ApiController extends Controller
{
    protected $statusCode = 200;

    protected $transformer = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->fractal = new Manager();
        $this->fractal->setSerializer(new ArraySerializer());
        if (isset($_GET['include'])) {
            $this->fractal->parseIncludes($_GET['include']);
        }
    }

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
     * Getter for transformer
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * Setter for transformer
     */
    public function setTransformer($transformer)
    {
        $this->transformer = $transformer;
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

    /**
     * Transform data
     *
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function transform($data)
    {
        // check if transformer exist
        if ($this->transformer == null) {
            throw new \Exception(trans('errors.missing_transformer'));
        }

        if ($data instanceof Collection) {
            $resource = new FractalCollection($data, $this->transformer, 'entries');
        } elseif ($data instanceof LengthAwarePaginator) {
            $resource = new FractalCollection($data->getCollection(), $this->transformer, 'entries');
            $resource->setPaginator(new IlluminatePaginatorAdapter($data));
        } else {
            $resource = new Item($data, $this->transformer);
        }

        return $this->fractal->createData($resource)->toArray();
    }
}
