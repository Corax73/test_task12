<?php

namespace Controllers;

use Enums\PermissionsForActions;
use Models\User;
use Pecee\Http\Response;

class ServiceController extends AbstractController
{
    /**
     * Checks the presence of data in the request, the matching of the tokens and the existence of the method.
     * On success, calls the method.
     * @param string $command
     * @return Pecee\Http\Response
     */
    public function service(string $command): Response
    {
        $resp = [];
        $data = $this->request->getInputHandler()->getOriginalPost();
        if (isset($data['token']) && isset($data['email'])) {
            $user = new User();
            $usersToken = $user->getToken($data['email']);
            if ($usersToken != 'error' && $usersToken === $data['token']) {
                if (method_exists($this, $command)) {
                    $resp = $this->$command($user->getRightsByEmail($data['email']));
                }
            }
        }
        return $this->response->json($resp);
    }

    /**
     * Stub.
     * @param array $
     * @return array
     */
    private function debug(array $rights): array
    {
        $resp = false;
        if ($rights) {
            if(in_array(PermissionsForActions::DebugSet->value, $rights)) {
                $resp = true;
            }
        }
        return ['debug' => $resp];
    }
}
