<?php

namespace Controllers;

use Enums\Errors;
use Enums\ListRights;
use Models\Group;
use Models\User;
use Models\UserMembership;
use Pecee\Http\Response;

class UserController extends AbstractController
{
    /**
     * Creates an pivot user's group membership model record.
     * @return Pecee\Http\Response
     */
    public function create(): Response
    {
        $resp = ['errors' => [Errors::IncompleteData->value]];
        $result = false;
        $data = $this->request->getInputHandler()->getOriginalPost();
        if (isset($data['user_id']) && isset($data['group_id'])) {
            $user = new User();
            $group = new Group();
            $check1 = false;
            $check2 = false;

            if ($user->find($data['user_id'])) {
                $check1 = true;
            } else {
                $resp = ['errors' => ['User ' . Errors::NotFound->value]];
            }

            if ($group->find($data['group_id'])) {
                $check2 = true;
            } else {
                if ($check1) {
                    $resp = ['errors' => ['Group ' . Errors::NotFound->value]];
                } else {
                    $resp['errors'][] = 'Group ' . Errors::NotFound->value;
                }
            }

            if ($check1 && $check2) {
                $userMembership = new UserMembership();
                $result = $userMembership->save($data['user_id'], $data['group_id']);
            }
        }
        $resp = $result ? 'user\'s group membership settled' : $resp;
        return $this->response->json(['response' => $resp]);
    }

    /**
     * Returns an array of user group IDs.
     * @param int $user_id
     * @return Pecee\Http\Response
     */
    public function showUsersGroups(int $user_id): Response
    {
        $userMembership = new UserMembership();
        $userGroups = $userMembership->memberships($user_id);
        $resp = $userGroups ? $userGroups : ['errors' => 'User group membership' . Errors::NotFound->value];
        return $this->response->json(['response' => $resp]);
    }

    /**
     * Gets rights from the user by his ID.
     * @param int $user_id
     * @return Pecee\Http\Response
     */
    public function showUsersRights(int $user_id): Response
    {
        $resp = ['errors' => ['User ' . Errors::NotFound->value]];
        $result = false;
        $user = new User();
        if ($user->find($user_id)) {
            $result = $user->getRights($user_id);
            if ($result) {
                $rights = collect(ListRights::cases())->map(fn ($item) => $item->value)->toArray();
                $resp = collect($result)->unique()->map(function ($item) use ($rights) {
                    return in_array($item['right_name'], $rights) ? [$item['right_name'] => true] : [$item['right_name'] => false];
                })->values();
            } else {
                $resp = ['errors' => 'User rights ' . Errors::NotFound->value];
            }
        }
        return $this->response->json(['response' => $resp]);
    }

    /**
     * Removes a user's membership in a group.
     * @param int $user_id
     * @param int $group_id
     * @return Pecee\Http\Response
     */
    public function destroyUserMembership(int $user_id, int $group_id): Response
    {
        $resp = ['errors' => ['User ' . Errors::NotFound->value]];
        $result = false;
        $user = new User();
        $group = new Group();
        if ($user->find($user_id)) {
            if ($group->find($group_id)) {
                $userMembership = new UserMembership();
                $result = $userMembership->delete($user_id, $group_id);
            } else {
                $resp['errors'] = 'Group ' . Errors::NotFound->value;
            }
        }
        $resp =  $result ? 'User membership removed.' : $resp;
        return $this->response->json(['response' => $resp]);
    }
}
