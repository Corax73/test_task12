<?php

namespace Controllers;

use Pecee\Http\Response;
use Service\RequestDataCheck;

class EntityController extends AbstractController
{
    /**
     * Creates a model-entity when a class exists with an argument-string in its name.
     * @param string $target
     * @return Pecee\Http\Response
     */
    public function create(string $target): Response
    {
        $resp = 'error';
        $result = false;
        $className = 'Models\\' . ucfirst($target);
        if (class_exists($className)) {
            $data = $this->request->getInputHandler()->getOriginalPost();
            if ($data['title']) {
                $check = new RequestDataCheck();
                if ($check->checkGroupTitleUniqueness($data['title'])) {
                    $entity = new $className();
                    $result = $entity->save($data['title']);
                }
            }
        }
        $resp = $result ? ucfirst($target) . ' created.' : $resp;
        return $this->response->json(['response' => $resp]);
    }
}