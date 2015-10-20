<?php

namespace Alien\Rest;

use Alien\Mvc\AbstractController;
use Alien\Mvc\Response;
use Alien\Routing\HttpRequest;

abstract class BaseRestfulController extends AbstractController
{

    public function restAction()
    {
        $request = $this->getRequest();
        if ($request instanceof HttpRequest) {
            $method = $request->getParam('method');

            if(!strlen(trim($method))) {
                if($request->isGet()) {
                    $method = 'list';
                }
                if($request->isPost() || $request->isPatch()) {
                    $method = 'patch';
                }
            }

            $methodName = $method . 'Action';
            if (!method_exists($this, $methodName)) {
                $errors = [
                    'code' => Response::STATUS_BAD_REQUEST,
                    'description' => "Method $method is not defined or accessible."
                ];
                return $this->errorResponse(Response::STATUS_BAD_REQUEST, 'Invalid method', 'Server cannot fulfill your request due to unsupported or malformed method name given.', $errors);
            } else {
                $response = $this->$methodName();
                return $response;
            }
        } else {
            return $this->errorResponse(Response::STATUS_BAD_REQUEST, 'Invalid request', 'Unsupported request. Only valid HTTP requests are possible.');
        }

    }

    protected function dataResponse($data = [], $code = Response::STATUS_OK) {
        $response = $this->getResponse();
        $response->setContent([
            'response' => [
                'status' => $code,
            ],
            'data' => $data
        ]);
        return $response;
    }

    protected function errorResponse($code, $message, $description, $errors = [])
    {
        return new Response([
            'response' => [
                'status' => $code,
                'message' => $message,
                'description' => $description,
            ],
            'errors' => [
                $errors
            ]
        ], $code, Response::MIME_JSON);
    }

    protected function prepareResponse()
    {
        return new Response([], Response::STATUS_OK, Response::MIME_JSON);
    }

}