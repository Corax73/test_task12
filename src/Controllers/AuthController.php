<?php

namespace Controllers;

use Models\User;
use Pecee\Http\Response;
use Service\RequestDataCheck;

class AuthController extends AbstractController
{
    public function login(): Response
    {
        return $this->response->json($this->request->getInputHandler()->getOriginalPost());
    }

    /**
     * Requests data checks from the request, tries to create a user, returns json in the response.
     * @return Pecee\Http\Response
     */
    public function registration(): Response
    {
        $resp = 'error';
        $data = $this->request->getInputHandler()->getOriginalPost();
        if ($data['password'] == $data['password_confirm']) {
            $check = new RequestDataCheck();
            if ($check->checkEmailUniqueness($data['email'])) {
                if ($check->checkingPassword($data['password'])) {
                    $user = new User();
                    $resp = $user->save($data['email'], $data['password']) ? 'User created.' : $resp;
                } else {
                    $resp = 'Invalid characters in the password or shorter than 8 characters.';
                }
            } else {
                $resp = 'Email is not unique!';
            }
        } else {
            $resp = 'Password mismatch.';
        }
        return $this->response->json(['response' => $resp]);
    }
}
