<?php

namespace Controllers;

use Enums\Errors;
use Pecee\Http\Response;
use Service\RequestDataCheck;

class EntityController extends AbstractController
{
    protected int $perPage = 12;

    /**
     * @param string $target
     * @param int $offset
     * @return Pecee\Http\Response
     */
    public function index(string $target, int $offset = 0): Response
    {
        $resp = ['errors' => 'Entity ' . Errors::NotFound->value];
        $result = [];
        $check = new RequestDataCheck();
        if ($check->checkEntityExist($target)) {
            $className = 'Models\\' . ucfirst($target);
            $entity = new $className();
            $result = $entity->all($this->perPage, $offset);
        }
        return $this->response->json(['response' => $result ? $result : $resp]);
    }
}
