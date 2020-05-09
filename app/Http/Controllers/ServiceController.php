<?php

namespace App\Http\Controllers;

use App\Services\ServiceService;
use App\Http\Requests\GetService;
use App\Transformers\ServiceTransformer;

class ServiceController extends ApiController
{
    protected $serviceService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ServiceService $serviceService)
    {
        parent::__construct();

        $this->serviceService = $serviceService;

        $this->setTransformer(new ServiceTransformer());
    }

    /**
     * Get bus stop's services
     *
     * @param GetService $request
     * @param string $code Bus stop code
     * @return mixed
     * @throws \Exception
     */
    public function services(GetService $request, $code)
    {
        $services = $this->serviceService->services($code);

        return $this->respond($this->transform($services));
    }
}
