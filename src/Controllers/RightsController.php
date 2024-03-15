<?php

namespace Controllers;

use Models\Group;
use Models\GroupRights;

class RightsController extends AbstractController
{
    /**
     * Creates an pivot group rights model record.
     * @return Pecee\Http\Response
     */
    public function create(): Response
    {
        $resp = 'error';
        $result = false;
        $data = $this->request->getInputHandler()->getOriginalPost();
        if ($data['group_id'] && $data['right']) {
            $group = new Group();
            if ($group->find($data['group_id'])) {
                $groupRights = new GroupRights();
                $result = $groupRights->save($data['group_id'], $data['right']);
            }
        }
        $resp = $result ? 'right settled' : $resp;
        return $this->response->json(['response' => $resp]);
    }
}
