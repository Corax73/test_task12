<?php

namespace Controllers;

use Pecee\SimpleRouter\SimpleRouter;
use Pecee\Http\Response;

/**
 * @property \Pecee\Http\Request $request
 * @property \Pecee\Http\Response $response
 */
abstract class AbstractController
{
    protected $response;
    protected $request;

    public function __construct()
    {
        $this->request = SimpleRouter::router()->getRequest();
        $this->response = new Response($this->request);
    }

    public function setCors(): void
    {
        $this->response->header('Access-Control-Allow-Origin: *');
        $this->response->header('Access-Control-Request-Method: OPTIONS');
        $this->response->header('Access-Control-Allow-Credentials: true');
        $this->response->header('Access-Control-Max-Age: 3600');
        $this->response->header('Content-Type: application/json');
    }
}
