<?php

namespace Controllers;

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
        $resp = 'error';
        $result = false;
        $data = $this->request->getInputHandler()->getOriginalPost();
        if (isset($data['group_id']) && isset($data['right'])) {
            $group = new Group();
            if ($group->find($data['group_id'])) {
                $declaredRights = collect(ListRights::cases())->map(fn($item) => $item->value)->toArray();
                if ($declaredRights && in_array($data['right'], $declaredRights)) {
                    $groupRights = new GroupRights();
                    $result = $groupRights->save($data['group_id'], $data['right']);
                }
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
        $group = new Group();
        $rights = $group->rights($group_id);
        $resp = $rights ? $rights : ['response' => 'Rights not found.'];
        return $this->response->json($resp);
    }
}
