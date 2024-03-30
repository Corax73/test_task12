<?php

namespace Controllers;

use Enums\Errors;
use Enums\ListRights;
use Models\Group;
use Models\GroupRights;
use Models\TempBlockedRights;
use Pecee\Http\Response;
use Service\RequestDataCheck;

class RightsController extends AbstractController
{
    /**
     * Creates an pivot group rights model record.
     * @return Pecee\Http\Response
     */
    public function create(): Response
    {
        $resp = ['errors' => [Errors::IncompleteData->value]];
        $result = false;
        $data = $this->request->getInputHandler()->getOriginalPost();
        if (isset($data['group_id']) && isset($data['right']) && $data['right'] != NULL && $data['group_id'] != NULL) {
            $group = new Group();
            if ($group->find($data['group_id'])) {
                $declaredRights = collect(ListRights::cases())->map(fn ($item) => $item->value)->toArray();
                if ($declaredRights && in_array($data['right'], $declaredRights)) {
                    $requestDataCheck = new RequestDataCheck();
                    if (!$requestDataCheck->checkGroupHasRight($data['group_id'], $data['right'])) {
                        $groupRights = new GroupRights();
                        $result = $groupRights->save($data['group_id'], $data['right']);
                    } else {
                        $right = $data['right'];
                        $resp = ['errors' => "Right $right " . Errors::AlreadyAvailable->value];
                    }
                } else {
                    $right = $data['right'];
                    $resp = ['errors' => "Right $right " . Errors::NotFound->value];
                }
            } else {
                $resp = ['errors' => 'Group ' . Errors::NotFound->value];
            }
        }
        $resp = $result ? 'right settled' : $resp;
        return $this->response->json(['response' => $resp]);
    }

    /**
     * Returns an array of its rights received from the Group model by ID, if available.
     * @param int $group_id
     * @return Pecee\Http\Response
     */
    public function show(int $group_id): Response
    {
        $resp = ['errors' => 'Rights ' . Errors::NotFound->value];
        $group = new Group();
        $rights = $group->rights($group_id);
        $resp = $rights ? $rights : $resp;
        return $this->response->json(['response' => $resp]);
    }

    /**
     * Removes a right in a group.
     * @param int $group_id
     * @param string $rightName
     * @return Pecee\Http\Response
     */
    public function destroy(int $groupId, string $rightName): Response
    {
        $result = false;
        $resp = ['errors' => 'Group ' . Errors::NotFound->value];
        $group = new Group();
        if ($group->find($groupId)) {
            $declaredRights = collect(ListRights::cases())->map(fn ($item) => $item->value)->toArray();
            $resp = ['errors' => "Right $rightName " . Errors::NotFound->value];
            if ($declaredRights && in_array($rightName, $declaredRights)) {
                $groupRights = new GroupRights();
                $result = $groupRights->delete($groupId, $rightName);
            }
        }
        $resp = $result ? 'group right removed' : $resp;
        return $this->response->json(['response' => $resp]);
    }

    /**
     * Reserves the right to be blocked.
     * @return Pecee\Http\Response
     */
    public function setTempBlockedRight(): Response
    {
        $resp = ['errors' => [Errors::IncompleteData->value]];
        $data = $this->request->getInputHandler()->getOriginalPost();
        if (isset($data['right']) && $data['right'] != NULL) {
            $tempBlocked = new TempBlockedRights();
            $declaredRights = collect(ListRights::cases())->map(fn ($item) => $item->value)->toArray();
            $right = $data['right'];
            if ($declaredRights && in_array($right, $declaredRights)) {
                $requestDataCheck = new RequestDataCheck();
                if (!$requestDataCheck->checkRightBlock($data['right'])) {
                    $result = $tempBlocked->save($right);
                    if ($result) {
                        $resp = "Temporary blocking of the right $right has been established";
                    }
                } else {
                    $right = $data['right'];
                    $resp = ['errors' => "Right $right " . Errors::AlreadyBlocked->value];
                }
            } else {
                $resp = ['errors' => "Right $right " . Errors::NotFound->value];
            }
        }
        return $this->response->json(['response' => $resp]);
    }

    /**
     * Removes the right from temporary blocking.
     * @param string $rightName
     * @return Pecee\Http\Response
     */
    public function destroyTemporaryBlocking(string $rightName): Response
    {
        $resp = ['errors' => "Right $rightName " . Errors::NotFound->value];
        $declaredRights = collect(ListRights::cases())->map(fn ($item) => $item->value)->toArray();
        if ($declaredRights && in_array($rightName, $declaredRights)) {
            $result = false;
            $tempBlocked = new TempBlockedRights();
            $result = $tempBlocked->delete($rightName);
            $resp =  $result ? "Temporary blocking of the right $rightName has been removed." : Errors::Default->value;
        }
        return $this->response->json(['response' => $resp]);
    }
}
