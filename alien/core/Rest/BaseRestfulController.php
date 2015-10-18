<?php

namespace Alien\Rest;

use Alien\Mvc\AbstractController;
use Alien\Mvc\Response;

abstract class BaseRestfulController extends AbstractController
{
    protected function prepareResponse()
    {
        return new Response([], Response::HTTP_SUCCESS, 'application/json;charset=UTF8');
    }


}