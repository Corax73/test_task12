<?php

namespace Controllers;

use Enums\Errors;
use Pecee\Http\Response;
use Service\RequestDataCheck;

class EntityController extends AbstractController
{
    protected int $perPage = 12;

    /**
     * Creates a model-entity when a class exists with an argument-string in its name.
     * @param string $target
     * @return Pecee\Http\Response
     */
    public function create(string $target): Response
    {
        $resp = ['errors' => 'Entity ' . Errors::NotFound->value];
        $result = false;
        $check = new RequestDataCheck();
        if ($check->checkEntityExist($target)) {
            $data = $this->request->getInputHandler()->getOriginalPost();
            if (isset($data['title'])) {
                if ($check->checkGroupTitleUniqueness($data['title'])) {
                    $className = 'Models\\' . ucfirst($target);
                    $entity = new $className();
                    $result = $entity->save($data['title']);
                }
            } else {
                $resp = ['errors' => [Errors::IncompleteData->value]];
            }
        }
        $resp = $result ? ucfirst($target) . ' created.' : $resp;
        return $this->response->json(['response' => $resp]);
    }

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
