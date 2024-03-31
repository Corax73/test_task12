<?php

namespace Controllers;

use Enums\Errors;
use Models\Group;
use Models\User;
use Pecee\Http\Response;
use Service\RequestDataCheck;

class GroupController extends AbstractController
{
    /**
     * Creates a group when a class exists with an argument-string in its name.
     * @return Pecee\Http\Response
     */
    public function create(): Response
    {
        $resp = ['errors' => [Errors::IncompleteData->value]];
        $result = false;
        $data = $this->request->getInputHandler()->getOriginalPost();
        if (isset($data['title']) && $data['title'] != NULL) {
            $check = new RequestDataCheck();
            if ($check->checkGroupTitleUniqueness($data['title'])) {
                $group = new Group();
                $result = $group->save($data['title']);
            } else {
                $resp = ['errors' => ['The group title ' . Errors::Unique->value]];
            }
        }
        $resp = $result ? 'Group ' . ucfirst($data['title']) . ' created.' : $resp;
        return $this->response->json(['response' => $resp]);
    }
    /**
     * Returns an array of group user data.
     * @param int $group_id
     * @return Pecee\Http\Response
     */
    public function show(int $group_id): Response
    {
        $resp = ['errors' => 'Group ' . Errors::NotFound->value];
        $result = false;
        $group = new Group();
        if ($group->find($group_id)) {
            $usersIds = $group->users($group_id);
            if ($usersIds) {
                $user = new User();
                $result = $user->find($usersIds);
            }
            $resp = $result ? $result : 'Group users not found.';
        }
        return $this->response->json(['response' => $resp]);
    }
}
