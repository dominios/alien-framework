<?php

namespace Alien\Rest;

use Alien\FordbiddenException;
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

            if (!strlen(trim($method))) {
                if ($request->isGet()) {
                    $method = 'list';
                }
                if ($request->isGet() && $request->getParam('id')) {
                    $method = 'get';
                }
                if ($request->isPost() || $request->isPatch()) {
                    $method = 'patch';
                }
            }

            $methodName = $method . 'Action';
            if (!method_exists($this, $methodName)) {
                $errors = [
                    'code' => Response::STATUS_BAD_REQUEST,
                    'description' => "Method '$method' is not defined or accessible."
                ];
                return $this->errorResponse(Response::STATUS_BAD_REQUEST, 'Invalid method', 'Server cannot fulfill your request due to unsupported or malformed method name given.', $errors);
            } else {
                try {
                    $response = $this->$methodName();
                } catch(\BadMethodCallException $e) {
                    $response = $this->authorizationFailedResponse($e->getMessage());
                } catch(\Exception $e) {
                    $response = $this->errorResponse(Response::STATUS_INTERNAL_SERVER_ERROR, $e->getMessage(), "");
                }
                return $response;
            }
        } else {
            return $this->errorResponse(Response::STATUS_BAD_REQUEST, 'Invalid request', 'Unsupported request. Only valid HTTP requests are possible.');
        }

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

    public function listAction()
    {
        $errors = [
            'code' => Response::STATUS_BAD_REQUEST,
            'description' => "Method list is not defined or accessible."
        ];
        return $this->errorResponse(Response::STATUS_BAD_REQUEST, 'Invalid method', 'Server cannot fulfill your request due to unsupported or malformed method name given.', $errors);
    }

    public function getAction()
    {
        $errors = [
            'code' => Response::STATUS_BAD_REQUEST,
            'description' => "Method get is not defined or accessible."
        ];
        return $this->errorResponse(Response::STATUS_BAD_REQUEST, 'Invalid method', 'Server cannot fulfill your request due to unsupported or malformed method name given.', $errors);
    }

    public function patchAction()
    {
        $errors = [
            'code' => Response::STATUS_BAD_REQUEST,
            'description' => "Method patch is not defined or accessible."
        ];
        return $this->errorResponse(Response::STATUS_BAD_REQUEST, 'Invalid method', 'Server cannot fulfill your request due to unsupported or malformed method name given.', $errors);
    }

    protected function dataResponse($data = [], $code = Response::STATUS_OK)
    {
        $response = $this->getResponse();
        $response->setContent([
            'response' => [
                'status' => $code,
            ],
            'data' => $data
        ]);
        return $response;
    }

    protected function prepareResponse()
    {
        return new Response([], Response::STATUS_OK, Response::MIME_JSON);
    }

    protected function authorizationFailedResponse($message)
    {
        $errors = [
            'code' => Response::STATUS_UNAUTHORIZED,
            'message' => $message
        ];
        return $this->errorResponse(Response::STATUS_UNAUTHORIZED, 'Authorization failed', $errors);
    }

    protected function authorize()
    {
        $token = $_GET['authorization_token'];
        if (!$token || $token !== 'heslo1234') {
            throw new \BadMethodCallException('None or invalid authorization token given.');
        }
    }

}