<?php

namespace Controllers;

use Enums\Errors;
use Enums\ListRights;
use Models\Group;
use Models\TempBlockedUsers;
use Models\User;
use Models\UserMembership;
use Pecee\Http\Response;
use Service\RequestDataCheck;

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
        if (isset($data['user_id']) && isset($data['group_id']) && $data['user_id'] != NULL && $data['group_id'] != NULL) {
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
                $requestDataCheck = new RequestDataCheck();
                if (!$requestDataCheck->checkGroupHasUser($data['group_id'], $data['user_id'])) {
                    $userMembership = new UserMembership();
                    $result = $userMembership->save($data['user_id'], $data['group_id']);
                } else {
                    $userId = $data['user_id'];
                    $resp = ['errors' => "User with ID $userId " . Errors::AlreadyAvailable->value];
                }
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
                $result = collect($result)->unique()->collapse()->values()->toArray();
                $rights = collect(ListRights::cases())->map(fn ($item) => $item->value);
                $resp = $rights->map(function ($item) use ($result) {
                    return in_array($item, $result) ? [$item => true] : [$item => false];
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

    /**
     * Sets the user to temporarily blocked.
     * @return Pecee\Http\Response
     */
    public function setTempBlockedUsers(): Response
    {
        $result = false;
        $resp = ['errors' => [Errors::IncompleteData->value]];
        $data = $this->request->getInputHandler()->getOriginalPost();
        if (isset($data['user_id']) && $data['user_id'] != NULL) {
            $user = new User();
            if ($user->find($data['user_id'])) {
                $tempBlocked = new TempBlockedUsers();
                $result = $tempBlocked->save($data['user_id']);
            } else {
                $resp['errors'] = 'User ' . Errors::NotFound->value;
            }
            $resp =  $result ? 'The user was placed in temporarily blocked.' : $resp;
        }
        return $this->response->json(['response' => $resp]);
    }

    /**
     * Removes a user from temporarily blocked.
     * @param int $user_id
     * @return Pecee\Http\Response
     */
    public function destroyTemporaryBlockingUser(int $user_id): Response
    {
        $resp = ['errors' => ['User ' . Errors::NotFound->value]];
        $result = false;
        $user = new User();
        if ($user->find($user_id)) {
            $tempBlocked = new TempBlockedUsers();
            $result = $tempBlocked->delete($user_id);
        }
        $resp =  $result ? 'The user has been removed from the temporarily blocked list.' : $resp;
        return $this->response->json(['response' => $resp]);
    }
}
