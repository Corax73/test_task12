<?php

namespace Controllers;

use Models\Group;
use Models\User;
use Pecee\Http\Response;

class GroupController extends AbstractController
{
    /**
     * Returns an array of group user data.
     * @param int $group_id
     * @return Pecee\Http\Response
     */
    public function show(int $group_id): Response
    {
        $resp = 'error';
        $result = false;
        $group = new Group();
        if ($group->find($group_id)) {
            $usersIds = $group->users($group_id);
            if ($usersIds) {
                $user = new User();
                $result = $user->find($usersIds);
            }
            $resp = $result ? $result : ['response' => 'Group users not found.'];
        }
        return $this->response->json($resp);
    }
}
