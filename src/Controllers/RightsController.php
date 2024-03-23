<?php

namespace Controllers;

use Enums\Errors;
use Enums\ListRights;
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
        $resp = ['errors' => [Errors::IncompleteData->value]];
        $result = false;
        $data = $this->request->getInputHandler()->getOriginalPost();
        if (isset($data['group_id']) && isset($data['right'])) {
            $group = new Group();
            if ($group->find($data['group_id'])) {
                $declaredRights = collect(ListRights::cases())->map(fn ($item) => $item->value)->toArray();
                if ($declaredRights && in_array($data['right'], $declaredRights)) {
                    $groupRights = new GroupRights();
                    $result = $groupRights->save($data['group_id'], $data['right']);
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
        return $this->response->json($resp);
    }
}
