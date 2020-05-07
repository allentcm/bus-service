<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\GetService;
use App\Repositories\ServiceRepository;
use App\Transformers\ServiceTransformer;

class ServiceController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ServiceRepository $services)
    {
        parent::__construct();

        $this->services = $services;

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
        $services = $this->services->getServices($code);
        return $this->respond($this->transform($services));
    }
}
