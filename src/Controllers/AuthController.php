<?php

namespace Controllers;

class AuthController extends AbstractController
{
    public function login()
    {
        return $this->response->json($this->request->getInputHandler()->getOriginalPost());
    }
}
