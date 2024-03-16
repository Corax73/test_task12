<?php

namespace Controllers;

use Models\Group;
use Models\User;
use Models\UserMembership;

class UserController extends AbstractController
{
    /**
     * Creates an pivot user's group membership model record.
     * @return Pecee\Http\Response
     */
    public function create(): Response
    {
        $resp = 'error';
        $result = false;
        $data = $this->request->getInputHandler()->getOriginalPost();
        if (isset($data['user_id']) && isset($data['group_id'])) {
            $user = new User();
            $group = new Group();
            if ($user->find($data['user_id']) && $group->find($data['group_id'])) {
                $userMembership = new UserMembership();
                $result = $userMembership->save($data['user_id'], $data['group_id']);
            }
        }
        $resp = $result ? 'user\'s group membership settled' : $resp;
        return $this->response->json(['response' => $resp]);
    }

    /**
     * Returns an array of user group IDs.
     * @param int $id
     * @return Pecee\Http\Response
     */
    public function showUsersGroups(int $id): Response
    {
        $userMembership = new UserMembership();
        $userGroups = $userMembership->memberships($id);
        $resp = $userGroups ? $userGroups : ['response' => 'User group membership not found.'];
        return $this->response->json($resp);
    }
}
