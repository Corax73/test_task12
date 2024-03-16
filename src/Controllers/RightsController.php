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

    /**
     * Returns an array of its rights received from the Group model by ID, if available.
     * @param int $id
     * @return Pecee\Http\Response
     */
    public function show(int $id): Response
    {
        $group = new Group();
        $rights = $group->rights($id);
        $resp = $rights ? $rights : ['response' => 'Rights not found.'];
        return $this->response->json($resp);
    }
}
